# Configuration

This document covers the configuration options available in Laravel Atlas.

## 📁 Configuration File

Laravel Atlas uses a configuration file located at `config/atlas.php`. You can publish the configuration file using:

```bash
php artisan vendor:publish --tag=atlas-config
```

## ⚙️ Configuration Options

### Basic Configuration

```php
<?php

return [
    /**
     * Enable or disable Atlas functionality
     */
    'enabled' => env('ATLAS_ENABLED', true),
    
    // ... other configuration options
];
```

### Status Tracking

Configure how Atlas tracks generation status and history:

```php
'status_tracking' => [
    'enabled' => env('ATLAS_STATUS_TRACKING_ENABLED', true),
    'file_path' => env('ATLAS_STATUS_FILE_PATH', storage_path('logs/atlas_status.log')),
    'track_history' => env('ATLAS_TRACK_HISTORY', true),
    'max_entries' => env('ATLAS_MAX_ENTRIES', 1000),
],
```

**Options:**
- `enabled` - Enable/disable status tracking
- `file_path` - Path to store status log
- `track_history` - Keep history of generations
- `max_entries` - Maximum log entries to keep

### Generation Settings

Configure default generation behavior:

```php
'generation' => [
    'output_path' => env('ATLAS_OUTPUT_PATH', base_path('atlas')),
    'formats' => [
        'image' => env('ATLAS_FORMAT_IMAGE', true),
        'json' => env('ATLAS_FORMAT_JSON', true),
        'markdown' => env('ATLAS_FORMAT_MARKDOWN', true),
    ],
],
```

**Options:**
- `output_path` - Default output directory
- `formats` - Enable/disable specific export formats

### Analysis Configuration

Configure analysis depth and scope:

```php
'analysis' => [
    'include_vendors' => env('ATLAS_INCLUDE_VENDORS', false),
    'max_depth' => env('ATLAS_MAX_DEPTH', 10),
    'scan_paths' => [
        app_path(),
        database_path(),
        config_path(),
    ],
],
```

**Options:**
- `include_vendors` - Include vendor packages in analysis
- `max_depth` - Maximum directory traversal depth
- `scan_paths` - Directories to scan for components

## 🔧 Environment Variables

You can override configuration using environment variables:

```env
# Enable/disable Atlas
ATLAS_ENABLED=true

# Status tracking
ATLAS_STATUS_TRACKING_ENABLED=true
ATLAS_STATUS_FILE_PATH="/custom/path/atlas_status.log"
ATLAS_TRACK_HISTORY=true
ATLAS_MAX_ENTRIES=5000

# Generation settings
ATLAS_OUTPUT_PATH="/custom/atlas/output"
ATLAS_FORMAT_IMAGE=true
ATLAS_FORMAT_JSON=true
ATLAS_FORMAT_MARKDOWN=true

# Analysis settings
ATLAS_INCLUDE_VENDORS=false
ATLAS_MAX_DEPTH=15
```

## 📊 Export Format Configuration

### JSON Export Options

```php
'json' => [
    'pretty_print' => env('ATLAS_JSON_PRETTY_PRINT', true),
    'include_metadata' => env('ATLAS_JSON_METADATA', true),
],
```

### HTML Export Options

```php
'html' => [
    'theme' => env('ATLAS_HTML_THEME', 'default'),
    'include_search' => env('ATLAS_HTML_SEARCH', true),
    'intelligent_workflow' => env('ATLAS_HTML_INTELLIGENT', true),
],
```

### Image Export Options

```php
'image' => [
    'default_width' => env('ATLAS_IMAGE_WIDTH', 1920),
    'default_height' => env('ATLAS_IMAGE_HEIGHT', 1080),
    'format' => env('ATLAS_IMAGE_FORMAT', 'png'),
],
```

## 🎯 Component-Specific Configuration

### Models Configuration

```php
'mappers' => [
    'models' => [
        'include_relationships' => true,
        'include_observers' => true,
        'include_factories' => true,
        'scan_path' => app_path('Models'),
    ],
],
```

### Routes Configuration

```php
'mappers' => [
    'routes' => [
        'include_middleware' => true,
        'include_controllers' => true,
        'exclude_vendor_routes' => true,
    ],
],
```

### Services Configuration

```php
'mappers' => [
    'services' => [
        'include_dependencies' => true,
        'detect_patterns' => true,
        'scan_paths' => [
            app_path('Services'),
            app_path('Domain/Services'),
        ],
    ],
],
```

## 🔐 Security Configuration

### Path Restrictions

```php
'security' => [
    'allowed_paths' => [
        app_path(),
        database_path('migrations'),
        base_path('routes'),
    ],
    'excluded_paths' => [
        storage_path(),
        public_path(),
    ],
    'max_file_size' => 1024 * 1024, // 1MB
],
```

### Sensitive Data Filtering

```php
'filtering' => [
    'exclude_patterns' => [
        '/password/i',
        '/secret/i',
        '/token/i',
        '/key/i',
    ],
    'anonymize_namespaces' => false,
    'hide_private_methods' => true,
],
```

## ⚡ Performance Configuration

### Caching

```php
'cache' => [
    'enabled' => env('ATLAS_CACHE_ENABLED', true),
    'ttl' => env('ATLAS_CACHE_TTL', 3600), // 1 hour
    'key_prefix' => 'atlas:',
    'store' => env('ATLAS_CACHE_STORE', 'file'),
],
```

### Memory Management

```php
'performance' => [
    'memory_limit' => env('ATLAS_MEMORY_LIMIT', '512M'),
    'timeout' => env('ATLAS_TIMEOUT', 300), // 5 minutes
    'batch_size' => env('ATLAS_BATCH_SIZE', 100),
    'parallel_processing' => env('ATLAS_PARALLEL', false),
],
```

## 🧪 Development Configuration

### Debug Mode

```php
'debug' => [
    'enabled' => env('ATLAS_DEBUG', false),
    'log_level' => env('ATLAS_LOG_LEVEL', 'info'),
    'output_raw_data' => env('ATLAS_OUTPUT_RAW', false),
    'timing_enabled' => env('ATLAS_TIMING', false),
],
```

### Testing Configuration

```php
'testing' => [
    'mock_file_system' => env('ATLAS_MOCK_FS', false),
    'use_fixtures' => env('ATLAS_USE_FIXTURES', false),
    'fixture_path' => base_path('tests/fixtures/atlas'),
],
```

## 📝 Custom Configuration Examples

### Multi-Environment Setup

```php
// config/atlas.php
return [
    'enabled' => env('ATLAS_ENABLED', app()->environment() !== 'production'),
    
    'analysis' => [
        'include_vendors' => app()->environment('local'),
        'max_depth' => app()->environment('local') ? 20 : 10,
    ],
    
    'generation' => [
        'output_path' => app()->environment('production') 
            ? storage_path('atlas')
            : base_path('atlas'),
    ],
];
```

### Custom Mapper Paths

```php
'mappers' => [
    'models' => [
        'scan_paths' => [
            app_path('Models'),
            app_path('Domain/*/Models'),
            app_path('Modules/*/Models'),
        ],
    ],
    'services' => [
        'scan_paths' => [
            app_path('Services'),
            app_path('Domain/*/Services'),
            app_path('Infrastructure/Services'),
        ],
    ],
],
```

### Export Format Customization

```php
'exports' => [
    'html' => [
        'template' => 'custom',
        'assets_path' => public_path('atlas-assets'),
        'custom_css' => resource_path('assets/atlas/custom.css'),
        'custom_js' => resource_path('assets/atlas/custom.js'),
    ],
    
    'image' => [
        'themes' => [
            'corporate' => [
                'background' => '#f8f9fa',
                'primary' => '#007bff',
                'secondary' => '#6c757d',
            ],
            'dark' => [
                'background' => '#1a1a1a',
                'primary' => '#4fc3f7',
                'secondary' => '#9e9e9e',
            ],
        ],
    ],
],
```

## 🔄 Dynamic Configuration

### Runtime Configuration

```php
use LaravelAtlas\Facades\Atlas;

// Override configuration at runtime
$customOptions = [
    'include_relationships' => true,
    'detailed_analysis' => true,
];

$data = Atlas::scan('models', $customOptions);
```

### Conditional Configuration

```php
'analysis' => [
    'include_vendors' => function() {
        return app()->environment('local') && config('app.debug');
    },
    'scan_paths' => function() {
        $paths = [app_path()];
        
        if (is_dir(app_path('Domain'))) {
            $paths[] = app_path('Domain');
        }
        
        if (is_dir(app_path('Modules'))) {
            $paths[] = app_path('Modules');
        }
        
        return $paths;
    },
],
```

## 📋 Configuration Validation

### Validating Configuration

```php
use LaravelAtlas\Support\ConfigValidator;

// Validate current configuration
$validator = new ConfigValidator();
$issues = $validator->validate(config('atlas'));

if (!empty($issues)) {
    foreach ($issues as $issue) {
        echo "Configuration issue: {$issue}\n";
    }
} else {
    echo "Configuration is valid\n";
}
```

### Configuration Health Check

```bash
# Check configuration health
php artisan atlas:config:check

# Validate specific configuration section
php artisan atlas:config:check --section=analysis
```

## 🔗 Related Documentation

- [Installation](installation.md) - Installation and setup
- [Usage Guide](usage.md) - Basic usage instructions
- [Mappers](mappers.md) - Available component mappers
- [Exporters](exporters.md) - Available export formats

---

For more information about specific configuration options, check the inline documentation in the published configuration file.