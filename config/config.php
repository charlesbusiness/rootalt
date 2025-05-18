<?php

return [

     'token_expiration_duration' => env('PERSONAL_ACCESS_TOKEN_EXPIRES', 1),
     'refresh_token_expiration_duration' => env('REFRESH_PERSONAL_ACCESS_TOKEN_EXPIRES', 20),
     'app_version' => env('APP_VERSION', 'v1'),
     'app_env' => env('APP_ENV'),
];