<?php

return [
    'name' => 'Core',
    'frontend_url' => env('FRONTEND_URL'),
    'termii' => [
        'api_key' => env('TERMII_API_KEY'),
        'api_secret' => env('TERMII_API_SECRET'),
        'baseurl' => env('TERMII_BASEURL'),
        'sms_from' => env('TERMII_SMS_FROM'),
    ],
];
