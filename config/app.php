<?php
declare(strict_types=1);
return [
    'name' => env('APP_NAME', 'Multi-Channel Content Manager'),
    'url' => env('APP_URL', ''),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'debug' => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL),
];
