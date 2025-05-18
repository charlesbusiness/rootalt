<?php

return [
    'name' => 'Authentication',
    'access_token' => env('PERSONAL_ACCESS_TOKEN'),
    'frontend_url' => env('FRONTEND_URL'),
    'reset_password_uri' => env('RESET_PASSWORD_URI'),
    'google' => [
      'client_id' => env('GOOGLE_CLIENT_ID'),
      'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
      'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    ],
];
