<?php

namespace App\Http\Controllers;

use App\Exports\ContactsExport;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    /**
     * Export CSV des contacts du business connecté
     */
    public function contacts(): Response
    {
        $businessId = auth()->user()->business_id;
        $export     = new ContactsExport($businessId);

        return response($export->toCsv(), 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $export->getFilename() . '"',
        ]);
    }
}
