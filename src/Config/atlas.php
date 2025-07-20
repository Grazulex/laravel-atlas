<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Atlas Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for Laravel Atlas.
    | You can customize various aspects of the Atlas functionality here.
    |
    */

    /**
     * Enable or disable Atlas functionality
     */
    'enabled' => env('ATLAS_ENABLED', true),

    /**
     * Status tracking configuration
     */
    'status_tracking' => [
        'enabled' => env('ATLAS_STATUS_TRACKING_ENABLED', true),
        'file_path' => env('ATLAS_STATUS_FILE_PATH', storage_path('logs/atlas_status.log')),
        'track_history' => env('ATLAS_TRACK_HISTORY', true),
        'max_entries' => env('ATLAS_MAX_ENTRIES', 1000),
    ],

    /**
     * Atlas generation options
     */
    'generation' => [
        'output_path' => env('ATLAS_OUTPUT_PATH', base_path('atlas')),
        'formats' => [
            'mermaid' => env('ATLAS_FORMAT_MERMAID', true),
            'json' => env('ATLAS_FORMAT_JSON', true),
            'markdown' => env('ATLAS_FORMAT_MARKDOWN', true),
        ],
    ],

    /**
     * Analysis depth and scope
     */
    'analysis' => [
        'include_vendors' => env('ATLAS_INCLUDE_VENDORS', false),
        'max_depth' => env('ATLAS_MAX_DEPTH', 10),
        'scan_paths' => [
            app_path(),
            database_path(),
            config_path(),
        ],
    ],
];
