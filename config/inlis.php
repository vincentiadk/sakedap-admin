<?php

return [
    'domain' => env('INLIS_DOMAIN'),
    'token' => env('INLIS_TOKEN'),
    'base_url' => env('INLIS_DOMAIN') . '/' . env('INLIS_BASE_URL'),
    'api_url' => env('INLIS_DOMAIN') . '/' . env('INLIS_API_URL'),
    'aes_key' => null,
    'aes_iv' => null,
];
