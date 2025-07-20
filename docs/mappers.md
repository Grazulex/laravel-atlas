# Mappers

Laravel Atlas uses specialized mappers to analyze different types of Laravel components. Each mapper scans specific parts of your application and extracts relevant information.

## 🗂️ Available Mappers

### Models Mapper (`models`)

Analyzes Eloquent models and their relationships.

**Default Options:**
```php
[
    'include_relationships' => true,
    'include_observers' => true,
    'include_factories' => true,
    'include_attributes' => true,
    'include_scopes' => true,
    'scan_path' => app_path('Models'),
]
```

**What it analyzes:**
- Model class structure and namespace
- Database table associations
- Eloquent relationships (hasMany, belongsTo, etc.)
- Model observers
- Factory classes
- Custom attributes and mutators
- Query scopes

**Usage:**
```bash
# Basic model scanning
php artisan atlas:generate --type=models

# Detailed model analysis
php artisan atlas:generate --type=models --detailed --format=markdown
```

### Routes Mapper (`routes`)

Analyzes application routes, middleware, and controllers.

**Default Options:**
```php
[
    'include_middleware' => true,
    'include_controllers' => true,
    'include_parameters' => true,
    'group_by_prefix' => false,
    'group_by_middleware' => false,
    'exclude_vendor_routes' => true,
]
```

**What it analyzes:**
- Route definitions (GET, POST, PUT, DELETE, etc.)
- Route parameters and constraints
- Applied middleware
- Controller actions
- Route groups and prefixes
- Named routes

**Usage:**
```bash
# Analyze all routes
php artisan atlas:generate --type=routes

# Focus on middleware relationships  
php artisan atlas:generate --type=routes --format=mermaid
```

### Jobs Mapper (`jobs`)

Scans queued job classes and their configurations.

**Default Options:**
```php
[
    'include_failed_jobs' => true,
    'include_job_batches' => true,
    'scan_path' => app_path('Jobs'),
    'analyze_dependencies' => true,
]
```

**What it analyzes:**
- Job class structure and properties
- Queue configurations
- Job dependencies and services used
- Failed job handling
- Job batching configurations
- Job middleware

### Services Mapper (`services`)

Analyzes service classes and their dependencies.

**Default Options:**
```php
[
    'include_dependencies' => true,
    'include_interfaces' => true,
    'scan_path' => app_path('Services'),
    'detect_patterns' => true,
]
```

**What it analyzes:**
- Service class structure
- Constructor dependencies
- Implemented interfaces
- Service provider bindings
- Method signatures and visibility

### Controllers Mapper (`controllers`)

Examines controller classes and their methods.

**Default Options:**
```php
[
    'include_middleware' => true,
    'include_methods' => true,
    'include_dependencies' => true,
    'scan_path' => app_path('Http/Controllers'),
    'analyze_responses' => true,
]
```

**What it analyzes:**
- Controller class hierarchy
- Action methods and parameters
- Applied middleware
- Dependency injection
- Response types and structures
- Resource controllers

### Events Mapper (`events`)

Maps application events and their listeners.

**Default Options:**
```php
[
    'include_listeners' => true,
    'include_subscribers' => true,
    'scan_paths' => [app_path('Events'), app_path('Listeners')],
    'trace_event_flow' => true,
]
```

**What it analyzes:**
- Event class definitions
- Event listeners and subscribers
- Event-listener relationships
- Queue configurations for listeners
- Event service provider bindings

### Commands Mapper (`commands`)

Analyzes Artisan console commands.

**Default Options:**
```php
[
    'include_arguments' => true,
    'include_options' => true,
    'scan_path' => app_path('Console/Commands'),
    'include_schedule' => true,
]
```

**What it analyzes:**
- Command signatures and descriptions
- Command arguments and options
- Command scheduling configuration
- Command dependencies and services

### Middleware Mapper (`middleware`)

Examines HTTP middleware classes.

**Default Options:**
```php
[
    'include_global_middleware' => true,
    'include_route_middleware' => true,
    'include_group_middleware' => true,
    'scan_path' => app_path('Http/Middleware'),
]
```

**What it analyzes:**
- Middleware class structure
- Global, route, and group middleware
- Middleware parameters and configuration
- Middleware ordering and dependencies

### Policies Mapper (`policies`)

Maps authorization policies and their methods.

**Default Options:**
```php
[
    'include_model_bindings' => true,
    'include_gate_definitions' => true,
    'scan_path' => app_path('Policies'),
    'analyze_permissions' => true,
]
```

**What it analyzes:**
- Policy class structure
- Authorization methods
- Model-policy bindings
- Gate definitions and custom logic

### Resources Mapper (`resources`)

Analyzes API resource classes.

**Default Options:**
```php
[
    'include_collections' => true,
    'include_relationships' => true,
    'scan_path' => app_path('Http/Resources'),
    'analyze_transformations' => true,
]
```

**What it analyzes:**
- Resource class definitions
- Resource collections
- Data transformations
- Conditional attributes
- Resource relationships

### Notifications Mapper (`notifications`)

Maps notification classes and channels.

**Default Options:**
```php
[
    'include_channels' => true,
    'include_templates' => true,
    'scan_path' => app_path('Notifications'),
    'analyze_delivery' => true,
]
```

**What it analyzes:**
- Notification class structure
- Delivery channels (mail, database, broadcast, etc.)
- Notification templates and content
- Queue configurations
- Notification routing

### Requests Mapper (`requests`)

Examines form request classes and validation rules.

**Default Options:**
```php
[
    'include_validation_rules' => true,
    'include_authorization' => true,
    'scan_path' => app_path('Http/Requests'),
    'analyze_custom_rules' => true,
]
```

**What it analyzes:**
- Request class structure
- Validation rules and custom validators
- Authorization logic
- Request data transformation
- Error message customization

### Rules Mapper (`rules`)

Analyzes custom validation rules.

**Default Options:**
```php
[
    'include_implicit_rules' => true,
    'include_rule_objects' => true,
    'scan_path' => app_path('Rules'),
    'analyze_dependencies' => true,
]
```

**What it analyzes:**
- Custom validation rule classes
- Rule logic and parameters
- Implicit validation rules
- Rule dependencies and services

## 🔧 Customizing Mapper Behavior

### Using Options in Commands

```bash
# Example: Custom path scanning for models
php artisan atlas:generate --type=models --detailed
```

### Programmatic Usage with Options

```php
use LaravelAtlas\Facades\Atlas;

// Scan models with custom options
$modelData = Atlas::scan('models', [
    'include_relationships' => true,
    'include_observers' => false,
    'scan_path' => app_path('Domain/Models'),
]);

// Scan routes with grouping
$routeData = Atlas::scan('routes', [
    'group_by_prefix' => true,
    'include_middleware' => true,
    'exclude_vendor_routes' => false,
]);
```

### Creating Custom Mapper Options

You can extend the default behavior by passing custom options:

```php
// Focus on specific aspects
$serviceData = Atlas::scan('services', [
    'include_dependencies' => true,
    'detect_patterns' => true,
    'custom_scan_paths' => [
        app_path('Services'),
        app_path('Domain/Services'),
    ],
]);
```

## 📊 Mapper Output Structure

Each mapper returns data in a consistent structure:

```json
{
  "type": "models",
  "scan_path": "/app/Models",
  "options": {
    "include_relationships": true,
    "include_observers": true
  },
  "data": [
    {
      "name": "User",
      "namespace": "App\\Models",
      "path": "/app/Models/User.php",
      "extends": "Illuminate\\Foundation\\Auth\\User",
      "implements": ["Illuminate\\Contracts\\Auth\\MustVerifyEmail"],
      "relationships": [
        {
          "type": "hasMany",
          "related": "App\\Models\\Post",
          "method": "posts"
        }
      ],
      "observers": ["App\\Observers\\UserObserver"],
      "factory": "Database\\Factories\\UserFactory"
    }
  ]
}
```

## 🎯 Best Practices

### Performance Optimization

```php
// Scan only what you need
$quickScan = Atlas::scan('models', [
    'include_relationships' => false,
    'include_observers' => false,
    'include_factories' => false,
]);

// Use custom paths for focused analysis
$domainModels = Atlas::scan('models', [
    'scan_path' => app_path('Domain/Models'),
]);
```

### Combining Mappers

```php
// Generate comprehensive documentation
$architectureMap = [
    'models' => Atlas::scan('models', ['include_relationships' => true]),
    'controllers' => Atlas::scan('controllers', ['include_middleware' => true]),
    'routes' => Atlas::scan('routes', ['group_by_prefix' => true]),
];

// Export combined data
$exporter = Atlas::exporter('markdown');
$documentation = $exporter->export($architectureMap);
```