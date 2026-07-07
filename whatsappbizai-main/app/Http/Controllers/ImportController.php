<?php

namespace App\Http\Controllers;

use App\Imports\ContactsImport;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    /**
     * Show the import form
     */
    public function form()
    {
        return view('import.contacts');
    }

    /**
     * Process the uploaded CSV
     */
    public function contacts(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $businessId = auth()->user()->business_id;
        $path       = $request->file('csv_file')->store('imports/tmp');
        $fullPath   = storage_path('app/' . $path);

        $importer = new ContactsImport($businessId);
        $results  = $importer->import($fullPath);

        // Clean up temp file
        @unlink($fullPath);

        return back()->with('import_results', $results);
    }

    /**
     * Download the CSV template
     */
    public function template()
    {
        $templatePath = public_path('templates/contacts-import-template.csv');

        if (!file_exists($templatePath)) {
            // Generate on the fly
            $csv = "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
            $csv .= "name;whatsapp_number;email;company;status;tags;notes\n";
            $csv .= "Jean Dupont;+237600000001;jean@example.com;Dupont SARL;client;VIP,fidele;Client depuis 2023\n";
            $csv .= "Marie Mbarga;+237699000002;;Mbarga Tech;prospect;nouveau;\n";
            $csv .= "Ali Hassan;+221771234567;ali@gmail.com;;client;;Payeur ponctuel\n";

            return response($csv, 200, [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="contacts-import-template.csv"',
            ]);
        }

        return response()->download($templatePath);
    }
}
