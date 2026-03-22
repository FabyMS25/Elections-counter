<?php

return [

    'paths' => ['refresh-dashboard', 'api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'allowed_origins' => [
        'https://brothers-display-concern-building.trycloudflare.com',
        'https://elections-control.netlify.app',
        'https://elections-control.neocities.org',
        'http://localhost', 
        'http://127.0.0.1',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Accept'],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => false,

];
