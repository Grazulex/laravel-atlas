# Configuration

Laravel Atlas provides extensive configuration options to customize the analysis and export behavior.

## Publishing Configuration

Publish the configuration file to customize Atlas behavior:

```bash
php artisan vendor:publish --tag=atlas-config
```

This creates `config/atlas.php` in your Laravel application.

## Configuration Structure

### Basic Configuration

```php
<?php

return [
    'enabled' => env('ATLAS_ENABLED', true),
    
    'status_tracking' => [
        'enabled' => env('ATLAS_STATUS_TRACKING_ENABLED', true),
        'file_path' => env('ATLAS_STATUS_FILE_PATH', storage_path('logs/atlas_status.log')),
        'track_history' => env('ATLAS_TRACK_HISTORY', true),
        'max_entries' => env('ATLAS_MAX_ENTRIES', 1000),
    ],
    
    'generation' => [
        'output_path' => env('ATLAS_OUTPUT_PATH', base_path('atlas')),
        'formats' => [
            'json' => env('ATLAS_FORMAT_JSON', true),
            'markdown' => env('ATLAS_FORMAT_MARKDOWN', true),
            'html' => env('ATLAS_FORMAT_HTML', true),
        ],
    ],
    
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
```

## Configuration Options

### General Settings

#### `enabled`
- **Type**: `boolean`
- **Default**: `true`
- **Environment**: `ATLAS_ENABLED`
- **Description**: Enable or disable Atlas functionality

```php
'enabled' => env('ATLAS_ENABLED', true),
```

### Status Tracking

#### `status_tracking.enabled`
- **Type**: `boolean`
- **Default**: `true`
- **Environment**: `ATLAS_STATUS_TRACKING_ENABLED`
- **Description**: Enable status tracking and logging

#### `status_tracking.file_path`
- **Type**: `string`
- **Default**: `storage_path('logs/atlas_status.log')`
- **Environment**: `ATLAS_STATUS_FILE_PATH`
- **Description**: Path to the status tracking log file

#### `status_tracking.track_history`
- **Type**: `boolean`
- **Default**: `true`
- **Environment**: `ATLAS_TRACK_HISTORY`
- **Description**: Track historical analysis data

#### `status_tracking.max_entries`
- **Type**: `integer`
- **Default**: `1000`
- **Environment**: `ATLAS_MAX_ENTRIES`
- **Description**: Maximum number of status entries to keep

### Generation Settings

#### `generation.output_path`
- **Type**: `string`
- **Default**: `base_path('atlas')`
- **Environment**: `ATLAS_OUTPUT_PATH`
- **Description**: Default output directory for generated files

#### `generation.formats`
- **Type**: `array`
- **Description**: Enable/disable specific export formats

```php
'formats' => [
    'json' => env('ATLAS_FORMAT_JSON', true),
    'markdown' => env('ATLAS_FORMAT_MARKDOWN', true),
    'html' => env('ATLAS_FORMAT_HTML', true),
],
```

### Analysis Settings

#### `analysis.include_vendors`
- **Type**: `boolean`
- **Default**: `false`
- **Environment**: `ATLAS_INCLUDE_VENDORS`
- **Description**: Include vendor packages in analysis

#### `analysis.max_depth`
- **Type**: `integer`
- **Default**: `10`
- **Environment**: `ATLAS_MAX_DEPTH`
- **Description**: Maximum depth for dependency analysis

#### `analysis.scan_paths`
- **Type**: `array`
- **Default**: `[app_path(), database_path(), config_path()]`
- **Description**: Directories to scan for components

## Environment Variables

You can configure Atlas using environment variables in your `.env` file:

```env
# General settings
ATLAS_ENABLED=true

# Status tracking
ATLAS_STATUS_TRACKING_ENABLED=true
ATLAS_STATUS_FILE_PATH=/var/log/atlas_status.log
ATLAS_TRACK_HISTORY=true
ATLAS_MAX_ENTRIES=2000

# Generation settings
ATLAS_OUTPUT_PATH=/var/www/html/docs/atlas
ATLAS_FORMAT_JSON=true
ATLAS_FORMAT_MARKDOWN=true
ATLAS_FORMAT_HTML=true

# Analysis settings
ATLAS_INCLUDE_VENDORS=false
ATLAS_MAX_DEPTH=15
```

## Advanced Configuration

### Custom Scan Paths

Add custom directories to scan:

```php
'analysis' => [
    'scan_paths' => [
        app_path(),
        database_path(),
        config_path(),
        base_path('packages'), // Custom packages
        base_path('modules'),  // Custom modules
    ],
],
```

### Component-Specific Configuration

Configure specific component types:

```php
'components' => [
    'models' => [
        'enabled' => true,
        'include_relationships' => true,
        'include_observers' => true,
        'include_factories' => true,
    ],
    'routes' => [
        'enabled' => true,
        'include_middleware' => true,
        'include_controllers' => true,
        'group_by_prefix' => true,
    ],
    'services' => [
        'enabled' => true,
        'include_dependencies' => true,
        'include_methods' => true,
    ],
],
```

### Export Configuration

Customize export behavior:

```php
'export' => [
    'html' => [
        'theme' => 'default',
        'dark_mode_default' => false,
        'include_search' => true,
        'include_navigation' => true,
    ],
    'markdown' => [
        'include_toc' => true,
        'include_stats' => true,
        'format' => 'github', // github, gitlab, default
    ],
    'json' => [
        'pretty_print' => true,
        'include_metadata' => true,
    ],
],
```

## Performance Configuration

### Memory Optimization

```php
'performance' => [
    'memory_limit' => '512M',
    'chunk_size' => 100,
    'enable_caching' => true,
    'cache_ttl' => 3600, // 1 hour
],
```

### Caching Configuration

```php
'cache' => [
    'enabled' => true,
    'store' => 'file', // file, redis, database
    'prefix' => 'atlas_cache',
    'ttl' => 3600,
],
```

## Production Configuration

For production environments:

```php
return [
    'enabled' => env('ATLAS_ENABLED', false), // Disabled by default in production
    
    'status_tracking' => [
        'enabled' => false, // Disable in production
    ],
    
    'generation' => [
        'output_path' => env('ATLAS_OUTPUT_PATH', storage_path('atlas')),
    ],
    
    'analysis' => [
        'include_vendors' => false,
        'max_depth' => 5, // Lower depth for performance
    ],
];
```

## Development Configuration

For development environments:

```php
return [
    'enabled' => true,
    
    'status_tracking' => [
        'enabled' => true,
        'track_history' => true,
    ],
    
    'analysis' => [
        'include_vendors' => true, // Include for complete analysis
        'max_depth' => 15,
    ],
];
```

## Testing Configuration

For testing environments:

```php
return [
    'enabled' => env('ATLAS_ENABLED', false),
    
    'status_tracking' => [
        'enabled' => false,
    ],
    
    'generation' => [
        'output_path' => storage_path('testing/atlas'),
    ],
];
```

## Custom Configuration Examples

### CI/CD Pipeline Configuration

```php
// config/atlas.php for CI/CD
return [
    'enabled' => true,
    
    'generation' => [
        'output_path' => env('CI_PROJECT_DIR', base_path()) . '/docs',
        'formats' => [
            'html' => true,
            'markdown' => true,
            'json' => true,
        ],
    ],
    
    'analysis' => [
        'include_vendors' => false,
        'max_depth' => 10,
    ],
];
```

### Enterprise Configuration

```php
// config/atlas.php for enterprise
return [
    'enabled' => env('ATLAS_ENABLED', true),
    
    'status_tracking' => [
        'enabled' => true,
        'file_path' => '/var/log/atlas/status.log',
        'max_entries' => 5000,
    ],
    
    'generation' => [
        'output_path' => '/var/www/docs/atlas',
        'formats' => [
            'html' => true,
            'markdown' => true,
            'json' => true,
        ],
    ],
    
    'analysis' => [
        'scan_paths' => [
            app_path(),
            base_path('packages'),
            base_path('modules'),
        ],
        'max_depth' => 20,
    ],
];
```

## Troubleshooting Configuration

### Common Issues

1. **Permission Issues**
   ```php
   'generation' => [
       'output_path' => storage_path('atlas'), // Use storage path
   ],
   ```

2. **Memory Issues**
   ```php
   'analysis' => [
       'max_depth' => 5, // Reduce depth
       'include_vendors' => false, // Exclude vendors
   ],
   ```

3. **Path Issues**
   ```php
   'analysis' => [
       'scan_paths' => [
           realpath(app_path()),
           realpath(database_path()),
       ],
   ],
   ```

## Configuration Validation

Atlas validates configuration on startup. Invalid configurations will result in helpful error messages.

To validate your configuration:

```bash
php artisan atlas:debug:config
```

This command will show your current configuration and highlight any issues.