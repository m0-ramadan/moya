<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://72.62.25.136:3000',
        'https://talaaljazeera.com',
        'https://www.talaaljazeera.com',
        'https://ecommerce-xo.vercel.app',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Authorization',
        'Content-Type'
    ],

    'max_age' => 86400,

    'supports_credentials' => false,
];
