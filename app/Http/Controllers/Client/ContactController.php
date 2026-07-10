<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $contacts = Contact::where('business_id', $user->business_id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('client.contacts.index', compact('user', 'contacts'));
    }

    public function create()
    {
        $user = Auth::user();
        return view('client.contacts.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'whatsapp_number'  => 'required|string|max:50',
            'company'          => 'nullable|string|max:255',
            'status'           => 'required|in:prospect,client,inactif',
            'notes'            => 'nullable|string|max:100000',
        ]);

        $data['business_id'] = $user->business_id;

        Contact::create($data);

        return redirect(url('client/contacts'))->with('success', __('app.client.flash.contact_created'));
    }

    public function edit(Contact $contact)
    {
        $user = Auth::user();
        abort_unless($contact->business_id === $user->business_id, 403);
        return view('client.contacts.edit', compact('user', 'contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $user = Auth::user();
        abort_unless($contact->business_id === $user->business_id, 403);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:50',
            'whatsapp_number'  => 'required|string|max:50',
            'company'          => 'nullable|string|max:255',
            'status'           => 'required|in:prospect,client,inactif',
            'notes'            => 'nullable|string|max:100000',
        ]);

        $contact->update($data);

        return redirect(url('client/contacts'))->with('success', __('app.client.flash.contact_updated'));
    }

    public function destroy(Contact $contact)
    {
        $user = Auth::user();
        abort_unless($contact->business_id === $user->business_id, 403);
        $contact->delete();
        return redirect(url('client/contacts'))->with('success', __('app.client.flash.contact_deleted'));
    }

    public function import()
    {
        $user = Auth::user();
        return view('client.contacts.import', compact('user'));
    }

    public function importStore(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $path     = $request->file('csv_file')->store('imports/tmp');
        $fullPath = storage_path('app/' . $path);

        $importer = new \App\Imports\ContactsImport($user->business_id);
        $results  = $importer->import($fullPath);

        @unlink($fullPath);

        return redirect(url('client/contacts'))->with('import_results', $results);
    }

    public function importTemplate()
    {
        $csv = "\xEF\xBB\xBF";
        $csv .= "name;whatsapp_number;email;company;status;notes\n";
        $csv .= "Jean Dupont;+237600000001;jean@example.com;Dupont SARL;client;Client depuis 2023\n";
        $csv .= "Marie Mbarga;+237699000002;;Mbarga Tech;prospect;\n";
        $csv .= "Ali Hassan;+221771234567;ali@gmail.com;;inactif;\n";

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="contacts-import-template.csv"',
        ]);
    }
}
