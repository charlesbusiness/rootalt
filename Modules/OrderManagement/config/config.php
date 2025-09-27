<?php

return [
    'name' => 'OrderManagement',
    'stripe' => [
        'secret' => env('STRIPE_API_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'key'    => env('STRIPE_API_KEY'),
    ],
];
