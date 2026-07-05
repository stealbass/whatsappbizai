<?php

namespace App\Imports;

use App\Models\Contact;
use Illuminate\Support\Str;

class ContactsImport
{
    private int $businessId;
    private array $results = ['imported' => 0, 'skipped' => 0, 'errors' => []];

    public function __construct(int $businessId)
    {
        $this->businessId = $businessId;
    }

    /**
     * Parse and import a CSV file
     * Accepted columns (order-independent, case-insensitive):
     *   name | nom, whatsapp | whatsapp_number | telephone | phone,
     *   email, company | entreprise, status | statut, tags, notes
     */
    public function import(string $filePath): array
    {
        if (!file_exists($filePath)) {
            $this->results['errors'][] = 'File not found.';
            return $this->results;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->results['errors'][] = 'Cannot open file.';
            return $this->results;
        }

        // Strip UTF-8 BOM if present (Excel exports)
        $firstBytes = fread($handle, 3);
        if ($firstBytes !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Detect delimiter (comma or semicolon)
        $sample    = fgets($handle);
        $delimiter = substr_count($sample, ';') >= substr_count($sample, ',') ? ';' : ',';
        rewind($handle);
        if ($firstBytes === "\xEF\xBB\xBF") fread($handle, 3); // re-skip BOM

        // Parse header row
        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (!$rawHeaders) {
            $this->results['errors'][] = 'Empty or unreadable header row.';
            fclose($handle);
            return $this->results;
        }

        $headers   = array_map(fn($h) => strtolower(trim($h)), $rawHeaders);
        $colMap    = $this->buildColumnMap($headers);

        if (!isset($colMap['whatsapp_number'])) {
            $this->results['errors'][] = 'Required column missing: whatsapp / phone / telephone / whatsapp_number.';
            fclose($handle);
            return $this->results;
        }

        $row = 1;
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $row++;
            if (count(array_filter($data)) === 0) continue; // skip blank rows

            try {
                $this->processRow($data, $colMap, $row);
            } catch (\Throwable $e) {
                $this->results['errors'][] = "Row {$row}: " . $e->getMessage();
            }
        }

        fclose($handle);
        return $this->results;
    }

    private function buildColumnMap(array $headers): array
    {
        $map = [];
        $aliases = [
            'whatsapp_number' => ['whatsapp', 'whatsapp_number', 'phone', 'telephone', 'tel', 'mobile', 'numéro', 'numero'],
            'name'            => ['name', 'nom', 'full_name', 'fullname', 'prenom_nom'],
            'email'           => ['email', 'e-mail', 'courriel'],
            'company'         => ['company', 'entreprise', 'société', 'societe', 'organization'],
            'status'          => ['status', 'statut'],
            'tags'            => ['tags', 'étiquettes', 'etiquettes', 'labels'],
            'notes'           => ['notes', 'note', 'remarques', 'comments'],
        ];

        foreach ($aliases as $field => $candidates) {
            foreach ($candidates as $candidate) {
                $idx = array_search($candidate, $headers);
                if ($idx !== false) {
                    $map[$field] = $idx;
                    break;
                }
            }
        }

        return $map;
    }

    private function processRow(array $data, array $colMap, int $row): void
    {
        $phone = $this->normalizePhone($data[$colMap['whatsapp_number']] ?? '');

        if (empty($phone)) {
            $this->results['skipped']++;
            $this->results['errors'][] = "Row {$row}: empty phone number — skipped.";
            return;
        }

        $name    = trim($data[$colMap['name']] ?? '') ?: null;
        $email   = strtolower(trim($data[$colMap['email']] ?? '')) ?: null;
        $company = trim($data[$colMap['company']] ?? '') ?: null;
        $notes   = trim($data[$colMap['notes']] ?? '') ?: null;

        // Status: normalize to allowed values
        $rawStatus = strtolower(trim($data[$colMap['status']] ?? 'prospect'));
        $status = match(true) {
            in_array($rawStatus, ['client', 'customer', 'active']) => 'client',
            in_array($rawStatus, ['inactif', 'inactive', 'inactivo']) => 'inactif',
            default => 'prospect',
        };

        // Tags: comma-separated string → array
        $rawTags = trim($data[$colMap['tags']] ?? '');
        $tags    = $rawTags ? array_filter(array_map('trim', explode(',', $rawTags))) : null;

        // Upsert: update if exists, create if not
        Contact::withoutGlobalScopes()->updateOrCreate(
            ['business_id' => $this->businessId, 'whatsapp_number' => $phone],
            [
                'name'         => $name,
                'email'        => $email,
                'company'      => $company,
                'notes'        => $notes,
                'status'       => $status,
                'tags'         => $tags ?: null,
                'portal_token' => Str::random(48),
            ]
        );

        $this->results['imported']++;
    }

    /**
     * Normalize phone: keep + and digits only, ensure international format
     */
    private function normalizePhone(string $raw): string
    {
        $phone = preg_replace('/[^\d+]/', '', trim($raw));

        // If starts with 00, replace with +
        if (str_starts_with($phone, '00')) {
            $phone = '+' . substr($phone, 2);
        }

        // If no country code (less than 10 digits), try to be lenient
        if (!str_starts_with($phone, '+') && strlen($phone) >= 8) {
            $phone = '+' . $phone;
        }

        return strlen($phone) >= 10 ? $phone : '';
    }

    public function getResults(): array
    {
        return $this->results;
    }
}
