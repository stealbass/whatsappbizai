<?php

namespace App\Exports;

use App\Models\Contact;
use Illuminate\Support\Collection;

class ContactsExport
{
    private Collection $contacts;

    public function __construct(int $businessId)
    {
        $this->contacts = Contact::withoutGlobalScopes()
            ->where('business_id', $businessId)
            ->with('business')
            ->orderBy('name')
            ->get();
    }

    /**
     * Génère et retourne le contenu CSV
     */
    public function toCsv(): string
    {
        $handle = fopen('php://temp', 'r+');

        // En-tête BOM UTF-8 pour Excel
        fputs($handle, "\xEF\xBB\xBF");

        // Colonnes
        fputcsv($handle, [
            'Nom',
            'WhatsApp',
            'Email',
            'Entreprise',
            'Statut',
            'Tags',
            'Total facturé',
            'Total payé',
            'Dernière activité',
            'Date création',
        ], ';');

        foreach ($this->contacts as $contact) {
            fputcsv($handle, [
                $contact->name            ?? '',
                $contact->whatsapp_number ?? '',
                $contact->email           ?? '',
                $contact->company         ?? '',
                $contact->status          ?? '',
                implode(', ', $contact->tags ?? []),
                number_format($contact->total_invoiced, 0, ',', ' '),
                number_format($contact->total_paid, 0, ',', ' '),
                $contact->last_seen_at?->format('d/m/Y H:i') ?? '',
                $contact->created_at->format('d/m/Y'),
            ], ';');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    public function getFilename(): string
    {
        return 'contacts-' . now()->format('Y-m-d') . '.csv';
    }
}
