<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'verify_sid' => env('TWILIO_VERIFY_SID'),
        'from' => env('TWILIO_FROM'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    ],

    'paymob' => [
        'username' => env('PAYMOB_USERNAME'),
        'password' => env('PAYMOB_PASSWORD'),
        'api_key' => env('PAYMOB_API_KEY'),
        'hmac_secret' => env('PAYMOB_HMAC_SECRET'),
        'integration_id' => env('PAYMOB_INTEGRATION_ID', '19293'),
        'iframe_id' => env('PAYMOB_IFRAME_ID', '11784'),
        'mode' => env('PAYMOB_MODE', 'test'),        // test أو live
        'currency' => env('PAYMOB_CURRENCY', 'SAR'),
        'base_url' => env('PAYMOB_BASE_URL', 'https://ksa.paymob.com'),

        // Endpoints
        'auth_url' => env('PAYMOB_BASE_URL', 'https://ksa.paymob.com') . '/api/auth/tokens',
        'payment_links_url' => env('PAYMOB_BASE_URL', 'https://ksa.paymob.com') . '/api/ecommerce/payment-links',

        // Public key
        'public_key' => env('PAYMOB_PUBLIC_KEY'),

        // Features
        'tokenization_enabled' => env('PAYMOB_TOKENIZATION_ENABLED', false),
        'save_card_enabled' => env('PAYMOB_SAVE_CARD_ENABLED', true),

        // IP Whitelist
        'allowed_ips' => explode(',', env('PAYMOB_ALLOWED_IPS', '')),

        // URLs
        'callback_url' => env('PAYMOB_CALLBACK_URL'),
        'return_url' => env('PAYMOB_RETURN_URL'),
        'cancel_url' => env('PAYMOB_CANCEL_URL'),
    ],
    'tamara' => [
        'sandbox' => env('TAMARA_SANDBOX', true),
        'username' => env('TAMARA_USERNAME'),
        'password' => env('TAMARA_PASSWORD'),
        'api_key' => env('TAMARA_API_KEY'),
        'notification_token' => env('TAMARA_NOTIFICATION_TOKEN'),
        'webhook_token' => env('TAMARA_WEBHOOK_TOKEN'),
        'api_token' => env('TAMARA_API_TOKEN'),
        'currency' => 'SAR',
    ],
    'tabby' => [
        // بيئة Tabby (Sandbox / Production)
        'sandbox' => env('TABBY_SANDBOX', true),

        // Merchant Info
        'merchant_code' => env('TABBY_MERCHANT_CODE'),

        // Keys
        'secret_key' => env('TABBY_SECRET_KEY'),
        'public_key' => env('TABBY_PUBLIC_KEY'),

        // Webhook
        'webhook_secret' => env('TABBY_WEBHOOK_SECRET'),

        'currency' => 'SAR',
        'base_url' => env('TABBY_SANDBOX', true)
            ? 'https://api.tabby.ai/api/v1/sandbox'
            : 'https://api.tabby.ai/api/v1',
    ],

    'orders' => [
        'expiration_minutes' => env('ORDER_EXPIRATION_MINUTES', 10),
    ],
    'region' => [
        'default' => env('APP_TIMEZONE', 'asia/riyadh'),
    ],

];
