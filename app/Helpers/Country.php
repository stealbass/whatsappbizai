<?php

namespace App\Helpers;

class Country
{
    /**
     * All African countries (ISO 3166-1 alpha-2 codes).
     * Plus a few relevant international countries for African SMEs.
     */
    public static function options(): array
    {
        return [
            // --- Afrique Centrale (CEMAC / XAF) ---
            'CM' => 'Cameroun',
            'CG' => 'Congo',
            'CD' => 'Rép. Dém. du Congo',
            'GA' => 'Gabon',
            'GQ' => 'Guinée Équatoriale',
            'CF' => 'Centrafrique',
            'TD' => 'Tchad',

            // --- Afrique de l\'Ouest (UEMOA / XOF) ---
            'SN' => 'Sénégal',
            'CI' => 'Côte d\'Ivoire',
            'ML' => 'Mali',
            'BF' => 'Burkina Faso',
            'NE' => 'Niger',
            'TG' => 'Togo',
            'BJ' => 'Bénin',
            'GW' => 'Guinée-Bissau',
            'GN' => 'Guinée',

            // --- Afrique de l\'Ouest (hors UEMOA) ---
            'NG' => 'Nigeria',
            'GH' => 'Ghana',
            'LR' => 'Libéria',
            'SL' => 'Sierra Leone',
            'GM' => 'Gambie',
            'CV' => 'Cap-Vert',

            // --- Afrique du Nord ---
            'MA' => 'Maroc',
            'TN' => 'Tunisie',
            'DZ' => 'Algérie',
            'EG' => 'Égypte',
            'LY' => 'Libye',

            // --- Afrique de l\'Est ---
            'KE' => 'Kenya',
            'TZ' => 'Tanzanie',
            'UG' => 'Ouganda',
            'RW' => 'Rwanda',
            'ET' => 'Éthiopie',
            'BI' => 'Burundi',
            'DJ' => 'Djibouti',
            'SO' => 'Somalie',
            'SS' => 'Soudan du Sud',
            'SD' => 'Soudan',

            // --- Afrique Australe ---
            'ZA' => 'Afrique du Sud',
            'MZ' => 'Mozambique',
            'ZM' => 'Zambie',
            'ZW' => 'Zimbabwe',
            'MW' => 'Malawi',
            'AO' => 'Angola',
            'BW' => 'Botswana',
            'NA' => 'Namibie',
            'LS' => 'Lesotho',
            'SZ' => 'Eswatini',
            'MG' => 'Madagascar',
            'MU' => 'Maurice',
            'SC' => 'Seychelles',
            'KM' => 'Comores',

            // --- Autres ---
            'ST' => 'São Tomé-et-Príncipe',
            'MR' => 'Mauritanie',

            // --- Pays internationaux pertinents ---
            'FR' => 'France',
            'BE' => 'Belgique',
            'GB' => 'Royaume-Uni',
            'US' => 'États-Unis',
            'CA' => 'Canada',
            'AE' => 'Émirats Arabes Unis',
            'CN' => 'Chine',
            'IN' => 'Inde',
            'TR' => 'Turquie',
        ];
    }

    /**
     * Get timezone list for a given country code.
     */
    public static function timezones(string $countryCode): array
    {
        $map = [
            'CM' => ['Africa/Douala'],
            'CG' => ['Africa/Brazzaville'],
            'CD' => ['Africa/Kinshasa', 'Africa/Lubumbashi'],
            'GA' => ['Africa/Libreville'],
            'GQ' => ['Africa/Malabo'],
            'CF' => ['Africa/Bangui'],
            'TD' => ['Africa/Ndjamena'],
            'SN' => ['Africa/Dakar'],
            'CI' => ['Africa/Abidjan'],
            'ML' => ['Africa/Bamako'],
            'BF' => ['Africa/Ouagadougou'],
            'NE' => ['Africa/Niamey'],
            'TG' => ['Africa/Lome'],
            'BJ' => ['Africa/Porto-Novo'],
            'GN' => ['Africa/Conakry'],
            'NG' => ['Africa/Lagos'],
            'GH' => ['Africa/Accra'],
            'MA' => ['Africa/Casablanca'],
            'TN' => ['Africa/Tunis'],
            'DZ' => ['Africa/Algiers'],
            'EG' => ['Africa/Cairo'],
            'KE' => ['Africa/Nairobi'],
            'TZ' => ['Africa/Dar_es_Salaam'],
            'UG' => ['Africa/Kampala'],
            'RW' => ['Africa/Kigali'],
            'ET' => ['Africa/Addis_Ababa'],
            'ZA' => ['Africa/Johannesburg'],
            'MZ' => ['Africa/Maputo'],
            'ZM' => ['Africa/Lusaka'],
            'ZW' => ['Africa/Harare'],
            'MW' => ['Africa/Blantyre'],
            'AO' => ['Africa/Luanda'],
            'MG' => ['Indian/Antananarivo'],
            'MU' => ['Indian/Mauritius'],
            'FR' => ['Europe/Paris'],
            'BE' => ['Europe/Brussels'],
            'GB' => ['Europe/London'],
            'US' => ['America/New_York', 'America/Chicago', 'America/Los_Angeles'],
            'CA' => ['America/Toronto', 'America/Vancouver'],
            'AE' => ['Asia/Dubai'],
            'CN' => ['Asia/Shanghai'],
            'IN' => ['Asia/Kolkata'],
            'TR' => ['Europe/Istanbul'],
        ];

        return $map[$countryCode] ?? ['UTC'];
    }

    /**
     * Get the default currency for a given country code.
     */
    public static function defaultCurrency(string $countryCode): string
    {
        $map = [
            'CM' => 'XAF', 'CG' => 'XAF', 'CD' => 'USD', 'GA' => 'XAF',
            'GQ' => 'XAF', 'CF' => 'XAF', 'TD' => 'XAF',
            'SN' => 'XOF', 'CI' => 'XOF', 'ML' => 'XOF', 'BF' => 'XOF',
            'NE' => 'XOF', 'TG' => 'XOF', 'BJ' => 'XOF', 'GN' => 'GNF',
            'NG' => 'NGN', 'GH' => 'GHS', 'LR' => 'LRD', 'SL' => 'SLL',
            'MA' => 'MAD', 'TN' => 'TND', 'DZ' => 'DZD', 'EG' => 'EGP',
            'KE' => 'KES', 'TZ' => 'TZS', 'UG' => 'UGX', 'RW' => 'RWF',
            'ET' => 'ETB', 'ZA' => 'ZAR', 'MZ' => 'MZN', 'ZM' => 'ZMW',
            'ZW' => 'ZWL', 'MW' => 'MWK', 'AO' => 'AOA', 'MG' => 'MGA',
            'MU' => 'MUR', 'FR' => 'EUR', 'BE' => 'EUR', 'GB' => 'GBP',
            'US' => 'USD', 'CA' => 'CAD', 'AE' => 'AED', 'CN' => 'CNY',
            'IN' => 'INR', 'TR' => 'TRY',
        ];

        return $map[$countryCode] ?? 'USD';
    }
}
