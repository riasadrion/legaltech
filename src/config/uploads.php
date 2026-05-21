<?php

return [
    'modules' => [
        'brand' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 4096,
            'public' => true,
            'pattern' => 'module',
            'max_width' => 500,
        ],
        'users' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 4096,
            'public' => true,
            'pattern' => 'module-owner',
            'max_width' => 300,
        ],
        'admins' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 4096,
            'public' => true,
            'pattern' => 'module-owner',
            'max_width' => 300,
        ],
        'pages' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
            'max_kb' => 4096,
            'public' => true,
            'pattern' => 'module-owner',
            'max_width' => 1920,
        ],
        'blog' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 4096,
            'public' => true,
            'pattern' => 'module-yearmonth',
            'max_width' => 1200,
        ],
        'gateways' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 4096,
            'public' => true,
            'pattern' => 'module',
            'max_width' => 200,
        ],
        'payout-methods' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 4096,
            'public' => true,
            'pattern' => 'module',
            'max_width' => 200,
        ],
        'notifications' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 4096,
            'public' => true,
            'pattern' => 'module',
            'max_width' => 600,
        ],
        'settings' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp'],
            'max_kb' => 4096,
            'public' => true,
            'pattern' => 'module',
            'max_width' => 1200,
        ],
        'kyc' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
            'max_kb' => 8192,
            'public' => false,
            'pattern' => 'module-owner-submission',
        ],
        'payments' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
            'max_kb' => 5120,
            'public' => false,
            'pattern' => 'module-yearmonth',
        ],
        'tickets' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp', 'application/pdf', 'text/plain'],
            'max_kb' => 10240,
            'public' => false,
            'pattern' => 'module-yearmonth',
        ],
    ],
];
