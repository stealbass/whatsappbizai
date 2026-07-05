<?php

return [
    'phone_number_id'        => env('WHATSAPP_PHONE_NUMBER_ID'),
    'access_token'           => env('WHATSAPP_ACCESS_TOKEN'),
    'verify_token'           => env('WHATSAPP_VERIFY_TOKEN', 'your_custom_verify_token_here'),
    'business_account_id'    => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
    'api_version'            => env('WHATSAPP_API_VERSION', 'v20.0'),
];
