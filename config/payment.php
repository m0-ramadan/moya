<?php

return [
    'paymob' => [
        'api_key' => env('PAYMOB_API_KEY'),
        'integration_id' => env('PAYMOB_INTEGRATION_ID'),
        'iframe_id' => env('PAYMOB_IFRAME_ID'),
        'hmac_secret' => env('PAYMOB_HMAC_SECRET'),

        // IP whitelist for callbacks
        'allowed_ips' => env('PAYMOB_ALLOWED_IPS') ?
            explode(',', env('PAYMOB_ALLOWED_IPS')) : [],

        // URLs
        'base_url' => env('PAYMOB_BASE_URL', 'https://accept.paymob.com'),
        'auth_url' => '/api/auth/tokens',
        'order_url' => '/api/ecommerce/orders',
        'payment_key_url' => '/api/acceptance/payment_keys',
        'transaction_url' => '/api/acceptance/transactions',
        'refund_url' => '/api/acceptance/refund',

        // Settings
        'timeout' => 30,
        'retry_attempts' => 3,
        'retry_delay' => 100,

        // Card tokenization
        'tokenization_enabled' => env('PAYMOB_TOKENIZATION_ENABLED', true),
        'save_card_enabled' => env('PAYMOB_SAVE_CARD_ENABLED', false),

        // Currency
        'default_currency' => env('PAYMOB_DEFAULT_CURRENCY', 'EGP'),

        // Callback URLs
        'callback_url' => env('PAYMOB_CALLBACK_URL', env('APP_URL') . '/api/payment/callback'),
        'return_url' => env('PAYMOB_RETURN_URL', env('APP_URL') . '/payment/return'),
        'cancel_url' => env('PAYMOB_CANCEL_URL', env('APP_URL') . '/payment/cancel'),
    ],
];
