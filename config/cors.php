<?php

return [

    'paths' => ['refresh-dashboard', 'api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST'],


    'allowed_origins' => [
        'https://liquid-prefers-rendered-tiny.trycloudflare.com',
        'https://elections-control.netlify.app',
        'http://localhost', 
        'http://127.0.0.1',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Accept'],

    'exposed_headers' => [],

    'max_age' => 3600,

    'supports_credentials' => false,

];
