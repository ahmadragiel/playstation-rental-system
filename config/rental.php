<?php

return [
    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin Nexus Play'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    'seed_demo_data' => filter_var(env('SEED_DEMO_DATA', false), FILTER_VALIDATE_BOOLEAN),
];
