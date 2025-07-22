<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Atlas Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for Laravel Atlas package.
    | You can customize these settings based on your application's needs.
    |
    */

    'enabled' => env('ATLAS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Status Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control how Atlas tracks and stores analysis status
    | and historical data about your Laravel application structure.
    |
    */

    'status_tracking' => [
        'enabled' => env('ATLAS_STATUS_TRACKING_ENABLED', true),
        'file_path' => storage_path('atlas/status.json'),
        'track_history' => env('ATLAS_TRACK_HISTORY', true),
        'max_entries' => env('ATLAS_MAX_ENTRIES', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Generation Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which output formats Atlas should generate and their
    | specific settings for different export types.
    |
    */

    'generation' => [
        'formats' => [
            'html' => env('ATLAS_GENERATE_HTML', true),
            'image' => env('ATLAS_GENERATE_IMAGE', true),
            'json' => env('ATLAS_GENERATE_JSON', true),
            'markdown' => env('ATLAS_GENERATE_MARKDOWN', true),
            'pdf' => env('ATLAS_GENERATE_PDF', false),
        ],
        'output_path' => env('ATLAS_OUTPUT_PATH', storage_path('atlas')),
        'template_path' => env('ATLAS_TEMPLATE_PATH', resource_path('views/atlas')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analysis Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how Atlas analyzes your Laravel application structure,
    | including which paths to scan and analysis depth.
    |
    */

    'analysis' => [
        'include_vendors' => env('ATLAS_INCLUDE_VENDORS', false),
        'max_depth' => env('ATLAS_MAX_DEPTH', 10),
        'scan_paths' => [
            app_path(),
            base_path('routes'),
            base_path('config'),
            database_path(),
        ],
        'exclude_patterns' => [
            '*/vendor/*',
            '*/node_modules/*',
            '*/storage/*',
            '*/bootstrap/cache/*',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Component Detection Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which Laravel components Atlas should detect and analyze
    | in your application structure.
    |
    */

    'components' => [
        'models' => env('ATLAS_DETECT_MODELS', true),
        'controllers' => env('ATLAS_DETECT_CONTROLLERS', true),
        'middleware' => env('ATLAS_DETECT_MIDDLEWARE', true),
        'requests' => env('ATLAS_DETECT_REQUESTS', true),
        'resources' => env('ATLAS_DETECT_RESOURCES', true),
        'jobs' => env('ATLAS_DETECT_JOBS', true),
        'events' => env('ATLAS_DETECT_EVENTS', true),
        'listeners' => env('ATLAS_DETECT_LISTENERS', true),
        'notifications' => env('ATLAS_DETECT_NOTIFICATIONS', true),
        'policies' => env('ATLAS_DETECT_POLICIES', true),
        'commands' => env('ATLAS_DETECT_COMMANDS', true),
        'services' => env('ATLAS_DETECT_SERVICES', true),
        'observers' => env('ATLAS_DETECT_OBSERVERS', true),
        'rules' => env('ATLAS_DETECT_RULES', true),
        'actions' => env('ATLAS_DETECT_ACTIONS', true),
        'routes' => env('ATLAS_DETECT_ROUTES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for different export formats and their specific options.
    |
    */

    'export' => [
        'html' => [
            'template' => 'atlas::layout',
            'include_css' => true,
            'include_js' => true,
            'bootstrap_cdn' => true,
        ],
        'pdf' => [
            'paper_size' => 'A4',
            'orientation' => 'portrait',
            'margin' => [10, 10, 10, 10],
        ],
        'json' => [
            'pretty_print' => env('ATLAS_JSON_PRETTY', true),
            'include_metadata' => true,
        ],
    ],
];
