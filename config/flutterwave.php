<?php

return [
    'public_key' => env('FLUTTERWAVE_PUBLIC_KEY', ''),
    'secret_key' => env('FLUTTERWAVE_SECRET_KEY', ''),
    'webhook_secret' => env('FLUTTERWAVE_WEBHOOK_SECRET', ''),
    'base_url' => 'https://api.flutterwave.com/v3',

    /*
    |--------------------------------------------------------------------------
    | Plans & Pricing (XAF — base currency)
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'starter' => [
            'monthly' => 9900,
            'yearly'  => 99000,
        ],
        'business' => [
            'monthly' => 24900,
            'yearly'  => 249000,
        ],
        'pro' => [
            'monthly' => 49900,
            'yearly'  => 499000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Currencies & Exchange Rates (relative to XAF)
    |--------------------------------------------------------------------------
    | Flutterwave will auto-detect payment methods based on the currency
    | and the user's country. Rates are approximate, for display only.
    */
    'currencies' => [
        'XAF'  => ['rate' => 1,        'country' => 'CM', 'name' => 'CFA Franc BEAC'],
        'NGN'  => ['rate' => 0.98,     'country' => 'NG', 'name' => 'Nigerian Naira'],
        'GHS'  => ['rate' => 0.08,     'country' => 'GH', 'name' => 'Ghanaian Cedi'],
        'KES'  => ['rate' => 0.11,     'country' => 'KE', 'name' => 'Kenyan Shilling'],
        'ZAR'  => ['rate' => 0.028,    'country' => 'ZA', 'name' => 'South African Rand'],
        'UGX'  => ['rate' => 0.006,    'country' => 'UG', 'name' => 'Ugandan Shilling'],
        'TZS'  => ['rate' => 0.006,    'country' => 'TZ', 'name' => 'Tanzanian Shilling'],
        'RWF'  => ['rate' => 0.002,    'country' => 'RW', 'name' => 'Rwandan Franc'],
        'USD'  => ['rate' => 0.00165,  'country' => 'US', 'name' => 'US Dollar'],
        'EUR'  => ['rate' => 0.00152,  'country' => 'FR', 'name' => 'Euro'],
        'GBP'  => ['rate' => 0.00128,  'country' => 'GB', 'name' => 'British Pound'],
        'CAD'  => ['rate' => 0.00222,  'country' => 'CA', 'name' => 'Canadian Dollar'],
        'CDF'  => ['rate' => 4.25,     'country' => 'CD', 'name' => 'Congolese Franc'],
        'BIF'  => ['rate' => 4.65,     'country' => 'BI', 'name' => 'Burundian Franc'],
        'GMD'  => ['rate' => 0.107,    'country' => 'GM', 'name' => 'Gambian Dalasi'],
        'SLL'  => ['rate' => 40.0,     'country' => 'SL', 'name' => 'Sierra Leonean Leone'],
        'LRD'  => ['rate' => 0.32,     'country' => 'LR', 'name' => 'Liberian Dollar'],
        'MWK'  => ['rate' => 2.85,     'country' => 'MW', 'name' => 'Malawian Kwacha'],
        'SZL'  => ['rate' => 0.028,    'country' => 'SZ', 'name' => 'Swazi Lilangeni'],
        'MGA'  => ['rate' => 0.0075,   'country' => 'MG', 'name' => 'Malagasy Ariary'],
        'CVE'  => ['rate' => 0.15,     'country' => 'CV', 'name' => 'Cape Verdean Escudo'],
        'XOF'  => ['rate' => 1,        'country' => 'SN', 'name' => 'CFA Franc BCEAO'],
        'DJF'  => ['rate' => 0.28,     'country' => 'DJ', 'name' => 'Djiboutian Franc'],
        'KMF'  => ['rate' => 0.71,     'country' => 'KM', 'name' => 'Comorian Franc'],
        'AED'  => ['rate' => 0.006,    'country' => 'AE', 'name' => 'UAE Dirham'],
        'SAR'  => ['rate' => 0.006,    'country' => 'SA', 'name' => 'Saudi Riyal'],
        'EGP'  => ['rate' => 0.08,     'country' => 'EG', 'name' => 'Egyptian Pound'],
        'MAD'  => ['rate' => 0.015,    'country' => 'MA', 'name' => 'Moroccan Dirham'],
        'TND'  => ['rate' => 0.005,    'country' => 'TN', 'name' => 'Tunisian Dinar'],
    ],
];
