# Configuration Guide

Laravel Atlas provides extensive configuration options to customize analysis behavior, export formats, and performance settings.

## 📝 Configuration File

### Publishing Configuration

To customize Atlas configuration, publish the config file:

```bash
php artisan vendor:publish --provider="LaravelAtlas\LaravelAtlasServiceProvider" --tag="config"
```

This creates `config/atlas.php` with all available options.

### Default Configuration

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Atlas Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether Atlas is enabled in your application.
    | You may wish to disable Atlas in production environments.
    |
    */
    'enabled' => env('ATLAS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Output Configuration
    |--------------------------------------------------------------------------
    |
    | Configure where Atlas stores generated maps and analysis results.
    |
    */
    'output' => [
        'path' => env('ATLAS_OUTPUT_PATH', storage_path('atlas')),
        'public_path' => env('ATLAS_PUBLIC_PATH', public_path('atlas')),
        'create_directories' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Analysis Configuration
    |--------------------------------------------------------------------------
    |
    | Control what Atlas analyzes and how deeply it scans your application.
    |
    */
    'analysis' => [
        'max_depth' => env('ATLAS_MAX_DEPTH', 10),
        'include_vendors' => env('ATLAS_INCLUDE_VENDORS', false),
        'include_tests' => env('ATLAS_INCLUDE_TESTS', false),
        'memory_limit' => env('ATLAS_MEMORY_LIMIT', '256M'),
        
        'scan_paths' => [
            app_path(),
            database_path(),
            config_path(),
        ],
        
        'exclude_paths' => [
            base_path('vendor'),
            base_path('node_modules'),
            storage_path(),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Formats
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific export formats and configure their options.
    |
    */
    'formats' => [
        'json' => [
            'enabled' => env('ATLAS_FORMAT_JSON', true),
            'pretty_print' => env('ATLAS_JSON_PRETTY', true),
            'include_metadata' => true,
        ],
        
        'markdown' => [
            'enabled' => env('ATLAS_FORMAT_MARKDOWN', true),
            'include_toc' => true,
            'template' => 'default',
        ],
        
        'html' => [
            'enabled' => env('ATLAS_FORMAT_HTML', true),
            'theme' => env('ATLAS_HTML_THEME', 'default'),
            'interactive' => true,
            'searchable' => true,
        ],
        
        'pdf' => [
            'enabled' => env('ATLAS_FORMAT_PDF', false),
            'template' => 'professional',
            'page_size' => 'A4',
            'orientation' => 'portrait',
        ],
        
        'image' => [
            'enabled' => env('ATLAS_FORMAT_IMAGE', false),
            'type' => 'png',
            'width' => 1920,
            'height' => 1080,
            'dpi' => 150,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Atlas can cache analysis results to improve performance on subsequent runs.
    |
    */
    'cache' => [
        'enabled' => env('ATLAS_CACHE_ENABLED', true),
        'ttl' => env('ATLAS_CACHE_TTL', 3600), // 1 hour
        'store' => env('ATLAS_CACHE_STORE', 'file'),
        'key_prefix' => 'atlas',
    ],

    /*
    |--------------------------------------------------------------------------
    | Component Analysis
    |--------------------------------------------------------------------------
    |
    | Configure which components Atlas should analyze and their options.
    |
    */
    'components' => [
        'models' => [
            'enabled' => true,
            'include_relationships' => true,
            'include_scopes' => true,
            'include_observers' => true,
            'include_casts' => true,
        ],
        
        'routes' => [
            'enabled' => true,
            'include_middleware' => true,
            'include_controllers' => true,
            'include_parameters' => true,
        ],
        
        'services' => [
            'enabled' => true,
            'include_dependencies' => true,
            'include_methods' => true,
            'include_interfaces' => true,
        ],
        
        'commands' => [
            'enabled' => true,
            'include_signature' => true,
            'include_flow' => true,
        ],
        
        'jobs' => [
            'enabled' => true,
            'include_queue_info' => true,
            'include_dependencies' => true,
        ],
        
        'events' => [
            'enabled' => true,
            'include_listeners' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Optimize Atlas performance for your application size and server resources.
    |
    */
    'performance' => [
        'parallel_processing' => env('ATLAS_PARALLEL_PROCESSING', false),
        'max_parallel_processes' => env('ATLAS_MAX_PROCESSES', 4),
        'chunk_size' => 100,
        'timeout' => 300, // 5 minutes
    ],
];
```

## 🌍 Environment Variables

### Basic Settings

```env
# Enable/disable Atlas
ATLAS_ENABLED=true

# Output directories
ATLAS_OUTPUT_PATH=storage/atlas
ATLAS_PUBLIC_PATH=public/atlas

# Analysis settings
ATLAS_MAX_DEPTH=10
ATLAS_INCLUDE_VENDORS=false
ATLAS_INCLUDE_TESTS=false
ATLAS_MEMORY_LIMIT=512M
```

### Export Format Settings

```env
# Enable/disable formats
ATLAS_FORMAT_JSON=true
ATLAS_FORMAT_MARKDOWN=true
ATLAS_FORMAT_HTML=true
ATLAS_FORMAT_PDF=false
ATLAS_FORMAT_IMAGE=false

# JSON options
ATLAS_JSON_PRETTY=true

# HTML options
ATLAS_HTML_THEME=modern

# PDF options
ATLAS_PDF_TEMPLATE=professional
```

### Performance Settings

```env
# Caching
ATLAS_CACHE_ENABLED=true
ATLAS_CACHE_TTL=3600
ATLAS_CACHE_STORE=redis

# Parallel processing
ATLAS_PARALLEL_PROCESSING=true
ATLAS_MAX_PROCESSES=8
```

## ⚙️ Component-Specific Configuration

### Model Analysis

```php
'components' => [
    'models' => [
        'enabled' => true,
        'include_relationships' => true,
        'include_scopes' => true,
        'include_observers' => true,
        'include_casts' => true,
        'include_fillable' => true,
        'include_hidden' => true,
        'include_dates' => true,
        'analyze_complexity' => false,
    ],
],
```

### Route Analysis

```php
'components' => [
    'routes' => [
        'enabled' => true,
        'include_middleware' => true,
        'include_controllers' => true,
        'include_parameters' => true,
        'include_names' => true,
        'group_by_prefix' => false,
        'analyze_security' => true,
    ],
],
```

### Service Analysis

```php
'components' => [
    'services' => [
        'enabled' => true,
        'include_dependencies' => true,
        'include_methods' => true,
        'include_interfaces' => true,
        'include_traits' => true,
        'analyze_complexity' => false,
        'service_patterns' => [
            'App\\Services\\*',
            'App\\Repositories\\*',
        ],
    ],
],
```

## 🎨 Export Configuration

### HTML Export Themes

```php
'formats' => [
    'html' => [
        'theme' => 'modern', // default, modern, minimal, dark
        'interactive' => true,
        'searchable' => true,
        'responsive' => true,
        'include_css' => true,
        'include_js' => true,
    ],
],
```

### PDF Export Templates

```php
'formats' => [
    'pdf' => [
        'template' => 'professional', // default, professional, minimal
        'page_size' => 'A4', // A4, Letter, Legal
        'orientation' => 'portrait', // portrait, landscape
        'margins' => [
            'top' => 20,
            'right' => 20,
            'bottom' => 20,
            'left' => 20,
        ],
    ],
],
```

### Image Export Options

```php
'formats' => [
    'image' => [
        'type' => 'png', // png, jpg, svg
        'width' => 1920,
        'height' => 1080,
        'dpi' => 300,
        'background' => '#ffffff',
        'font_size' => 12,
    ],
],
```

## 🚀 Performance Tuning

### For Large Applications

```php
'analysis' => [
    'max_depth' => 5, // Reduce depth for faster scanning
    'include_vendors' => false,
    'memory_limit' => '1G',
],

'performance' => [
    'parallel_processing' => true,
    'max_parallel_processes' => 8,
    'chunk_size' => 50,
],

'cache' => [
    'enabled' => true,
    'ttl' => 7200, // 2 hours
    'store' => 'redis',
],
```

### For Development

```php
'analysis' => [
    'max_depth' => 10,
    'include_tests' => true,
],

'cache' => [
    'enabled' => false, // Always fresh analysis
],

'formats' => [
    'html' => [
        'interactive' => true,
        'searchable' => true,
    ],
],
```

### For Production

```php
'enabled' => env('ATLAS_ENABLED', false), // Disabled by default

'analysis' => [
    'include_vendors' => false,
    'memory_limit' => '512M',
],

'cache' => [
    'enabled' => true,
    'ttl' => 86400, // 24 hours
],
```

## 🔧 Runtime Configuration

### Programmatic Configuration

```php
use LaravelAtlas\Facades\Atlas;

// Configure analysis options
Atlas::configure([
    'include_relationships' => true,
    'max_depth' => 5,
    'cache_enabled' => false,
]);

// Scan with custom options
$data = Atlas::scan('models', [
    'include_observers' => true,
    'include_scopes' => false,
]);
```

### Command-Line Overrides

```bash
# Override configuration via command line
php artisan atlas:generate --max-depth=5 --no-cache --include-vendors

# Set custom output path
php artisan atlas:generate --output-path=/custom/path

# Configure export format
php artisan atlas:generate --format=html --theme=dark --interactive
```

## 📊 Configuration Validation

Atlas provides commands to validate and test your configuration:

```bash
# Show current configuration
php artisan atlas:config

# Validate configuration
php artisan atlas:config --validate

# Test configuration with dry run
php artisan atlas:generate --dry-run
```

---

**Next:** Learn about [API usage](api.md) or explore [troubleshooting](troubleshooting.md) for common configuration issues.