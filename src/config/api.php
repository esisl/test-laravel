<?php

return [
    'base_url' => env('API_BASE_URL', 'https://example.com'),
    'key'      => env('API_KEY'),
    'timeout'  => env('API_TIMEOUT', 30),
    'retry'    => env('API_RETRY', 3),
];