<?php

return [
    'expiration_minutes' => env('ORDER_EXPIRATION_MINUTES', 5),
    'notification_channels' => [
        'new_orders' => 'new.orders',
        'user_orders' => 'user.{id}',
        'order_updates' => 'order.{id}',
        'driver_updates' => 'driver.{id}',
    ],
];
