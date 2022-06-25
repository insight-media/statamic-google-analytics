<?php

return [

    'tracking' => [

        // Tracking ID (string)
        'tracking_id' => env('GA_TRACKING_ID', null),

        // CP Admins should not be tracked (bool)
        'ignore_admins' => env('GA_IGNORE_ADMINS', true),

        // Tracking should only be active in production environment (bool)
        'production_only' => env('GA_PRODUCTION_ONLY', true),

        // Array will be passed to gtag config (array)
        'additional_config_info' => [
            'debug' => false
        ],

    ],

    'analytics' => [

        // Path to credentials file (string)
        'service_account_credentials_json' => storage_path(env("GA_CREDENTIALS_PATH", 'laravel-analytics/credentials.json')),

        // Property ID (string)
        'property_id' => env('GA_PROPERTY_ID', null),

        // Number of days for the analytics to show data (int)
        'days' => env('GA_DAYS', 30),

        // Show analytics per entry (having a slug) (bool)
        'page_graph' => env('GA_PAGE_GRAPH', true),

    ]

];
