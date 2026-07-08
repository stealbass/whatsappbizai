<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Cloud API
    |--------------------------------------------------------------------------
    | verify_token : token que vous choisissez librement et copiez dans Meta
    | api_version  : version de l'API Graph à utiliser
    */
    'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'your_custom_verify_token_here'),
    'api_version'  => env('WHATSAPP_API_VERSION', 'v20.0'),

    /*
    |--------------------------------------------------------------------------
    | Meta Embedded Signup (option #4 — connexion WhatsApp en 1 clic)
    |--------------------------------------------------------------------------
    | meta_app_id     : App ID de votre Meta App (App Dashboard)
    | meta_app_secret : App Secret de votre Meta App
    | meta_config_id  : Configuration ID de votre flux Embedded Signup
    |
    | Si ces valeurs sont vides, le bouton "Connecter mon WhatsApp" est masqué
    | et les clients configurent leurs credentials manuellement.
    |--------------------------------------------------------------------------
    */
    'meta_app_id'     => env('META_APP_ID'),
    'meta_app_secret' => env('META_APP_SECRET'),
    'meta_config_id'  => env('META_CONFIG_ID'),
];
