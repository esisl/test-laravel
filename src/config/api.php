<?php
return [
    'base_url' => env('API_BASE_URL'),
    'key' => env('API_KEY'),
    'timeout' => (int) env('API_TIMEOUT', 30),
    'retry' => (int) env('API_RETRY', 3),
    'entities' => [
        'sales' => '/api/sales',
        'orders' => '/api/orders',
        'stocks' => '/api/stocks',
        'incomes' => '/api/incomes',
    ],
];