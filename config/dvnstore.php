<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DVNStore Platform Rules
    |--------------------------------------------------------------------------
    | Aturan keuangan & operasional yang dipakai di seluruh aplikasi.
    */

    'upload_fee'          => (int) env('DVN_UPLOAD_FEE', 10000),
    'platform_fee_percent'=> (int) env('DVN_PLATFORM_FEE_PERCENT', 10),
    'min_withdraw'        => (int) env('DVN_MIN_WITHDRAW', 50000),

    'genres' => [
        'Game' => [
            'Action',
            'Adventure',
            'Arcade',
            'Battle Royale',
            'Casual',
            'Fighting',
            'Horror',
            'Puzzle',
            'Racing',
            'RPG',
            'Simulation',
            'Sports',
            'Strategy',
        ],
        'Aplikasi' => [
            'Business',
            'Communication',
            'Design',
            'Education',
            'Entertainment',
            'Finance',
            'Health & Fitness',
            'Lifestyle',
            'Music & Audio',
            'Photo & Video',
            'Productivity',
            'Shopping',
            'Social',
            'Tools',
            'Utilities',
        ],
    ],

    'midtrans' => [
        'server_key'    => env('MIDTRANS_SERVER_KEY'),
        'client_key'    => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
        'is_sanitized'  => (bool) env('MIDTRANS_IS_SANITIZED', true),
        'is_3ds'        => (bool) env('MIDTRANS_IS_3DS', true),
    ],

    'iris' => [
        'enabled'       => (bool) env('IRIS_ENABLED', false),
        'api_key'       => env('MIDTRANS_IRIS_API_KEY'),
        'is_production' => (bool) env('MIDTRANS_IRIS_IS_PRODUCTION', false),
        'base_url_sandbox'    => 'https://app.sandbox.midtrans.com/iris/api/v1',
        'base_url_production' => 'https://app.midtrans.com/iris/api/v1',
    ],

    'virustotal' => [
        'api_key'  => env('VIRUSTOTAL_API_KEY'),
        'enabled'  => (bool) env('VIRUSTOTAL_ENABLED', true),
        'base_url' => 'https://www.virustotal.com/api/v3',
    ],

    'admin' => [
        'name'     => env('ADMIN_NAME', 'adminalex'),
        'email'    => env('ADMIN_EMAIL', 'adminalex@dvnstore.local'),
        'password' => env('ADMIN_PASSWORD', 'change-me-please'),
    ],
];
