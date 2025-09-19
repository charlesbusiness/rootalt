<?php

return [
    'name' => 'Core',
    'app_url' => env('APP_URL'),
    'frontend_url' => env('FRONTEND_URL'),
    'termii' => [
        'api_key' => env('TERMII_API_KEY'),
        'api_secret' => env('TERMII_API_SECRET'),
        'baseurl' => env('TERMII_BASEURL'),
        'sms_from' => env('TERMII_SMS_FROM'),
    ],

    'default_admin' => [
        'lastname' => env('DEFAULT_ADMIN_LASTNAME'),
        'firstname' => env('DEFAULT_ADMIN_FIRSTNAME'),
        'password' => env('DEFAULT_ADMIN_PASSWORD'),
        'phone' => env('DEFAULT_ADMIN_PHONE'),
        'username' => env('DEFAULT_ADMIN_USERNAME'),
        'email' => env('DEFAULT_ADMIN_EMAIL'),
    ]
];
