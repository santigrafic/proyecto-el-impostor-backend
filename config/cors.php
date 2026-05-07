<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:3000',
        'https://proyecto-el-impostor-frontend.vercel.app/',
        'http://proyecto-el-impostor-frontend-git-main-santigrafics-projects.vercel.app/',
        'https://proyecto-el-impostor-frontend-nyg5yl236-santigrafics-projects.vercel.app/'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];