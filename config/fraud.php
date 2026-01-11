<?php

return [
    /*
    |--------------------------------------------------------------------------
    | إعدادات كشف الاحتيال
    |--------------------------------------------------------------------------
    */

    'risk_threshold' => env('FRAUD_RISK_THRESHOLD', 60),
    'new_user_threshold' => env('FRAUD_NEW_USER_THRESHOLD', 50),
    'driver_threshold' => env('FRAUD_DRIVER_THRESHOLD', 60),
    'payment_threshold' => env('FRAUD_PAYMENT_THRESHOLD', 50),

    /*
    |--------------------------------------------------------------------------
    | Velocity Limits
    |--------------------------------------------------------------------------
    */
    'velocity' => [
        'max_deposits_per_hour' => env('FRAUD_MAX_DEPOSITS_HOUR', 10),
        'max_withdrawals_per_hour' => env('FRAUD_MAX_WITHDRAWALS_HOUR', 5),
        'max_transfers_per_hour' => env('FRAUD_MAX_TRANSFERS_HOUR', 20),
        'max_transactions_per_5min' => env('FRAUD_MAX_TRANSACTIONS_5MIN', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Amount Limits
    |--------------------------------------------------------------------------
    */
    'amount' => [
        'max_deposit' => env('FRAUD_MAX_DEPOSIT_AMOUNT', 50000),
        'max_withdrawal' => env('FRAUD_MAX_WITHDRAWAL_AMOUNT', 20000),
        'max_transfer' => env('FRAUD_MAX_TRANSFER_AMOUNT', 10000),
        'min_transaction' => env('FRAUD_MIN_TRANSACTION_AMOUNT', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Time Restrictions
    |--------------------------------------------------------------------------
    */
    'time' => [
        'night_hours_start' => env('FRAUD_NIGHT_HOURS_START', 0),
        'night_hours_end' => env('FRAUD_NIGHT_HOURS_END', 6),
        'rapid_transaction_seconds' => env('FRAUD_RAPID_TRANSACTION_SECONDS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Geographic Restrictions
    |--------------------------------------------------------------------------
    */
    'geographic' => [
        'high_risk_countries' => explode(',', env('FRAUD_HIGH_RISK_COUNTRIES', 'AF,IR,KP,SY,YE')),
        'max_distance_km' => env('FRAUD_MAX_DISTANCE_KM', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Device & IP Restrictions
    |--------------------------------------------------------------------------
    */
    'device' => [
        'max_devices_per_day' => env('FRAUD_MAX_DEVICES_DAY', 3),
        'blacklist_duration_days' => env('FRAUD_BLACKLIST_DURATION', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Settings
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enable_realtime' => env('FRAUD_ENABLE_REALTIME', true),
        'enable_analytics' => env('FRAUD_ENABLE_ANALYTICS', true),
        'alert_admins' => env('FRAUD_ALERT_ADMINS', true),
        'auto_suspend' => env('FRAUD_AUTO_SUSPEND', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third-party Services
    |--------------------------------------------------------------------------
    */
    'services' => [
        'maxmind_user_id' => env('MAXMIND_USER_ID'),
        'maxmind_license_key' => env('MAXMIND_LICENSE_KEY'),
        'ipapi_key' => env('IPAPI_KEY'),
        'haveibeenpwned_key' => env('HAVEIBEENPWNED_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'level' => env('FRAUD_LOG_LEVEL', 'warning'),
        'channel' => env('FRAUD_LOG_CHANNEL', 'fraud'),
        'store_days' => env('FRAUD_LOG_STORE_DAYS', 90),
    ],
];
