<?php

return [
    'isbn' => [
        'endpoint' => [
            'localhost' => [
                'scheme'  => env('SOLR_SCHEME', 'http'),
                'host'    => env('SOLR_HOST', 'localhost'),
                'port'    => env('SOLR_PORT', '8983'),
                'path'    => env('SOLR_PATH', '/'),
                'core'    => 'isbn',
                'timeout' => '3600'
            ]
        ]
    ],
    'isrc' => [
        'endpoint' => [
            'localhost' => [
                'scheme'  => 'http',
                'host'    => '127.0.0.1',
                'port'    => '8983',
                'path'    => '/',
                'core'    => 'isrcdev',
                'timeout' => '3600'
            ]
        ]
    ]
];
