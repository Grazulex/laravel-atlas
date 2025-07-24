# Component Types

Laravel Atlas analyzes 7 different component types in your Laravel application. This guide provides detailed information about each component type and what Atlas discovers.

## 🧱 Models

**Purpose**: Analyze Eloquent models and their relationships

### What Atlas Discovers

- **Basic Information**
  - Model class name and namespace
  - File path and location
  - Table name (if specified)
  - Primary key configuration

- **Attributes**
  - Fillable attributes
  - Guarded attributes
  - Casts configuration
  - Dates configuration
  - Hidden attributes

- **Relationships**
  - hasOne, hasMany relationships
  - belongsTo, belongsToMany relationships
  - morphTo, morphMany relationships
  - Relationship target models

- **Additional Features**
  - Observers (if registered)
  - Factories (if available)
  - Scopes (global and local)
  - Mutators and accessors

### Usage Examples

```bash
# Generate models documentation
php artisan atlas:generate --type=models --format=html --output=docs/models.html
```

```php
// Programmatic analysis
$modelsData = Atlas::scan('models', [
    'include_relationships' => true,
    'include_observers' => true,
    'include_factories' => true,
]);
```

## 🛣️ Routes

**Purpose**: Map application routes and their configuration

### What Atlas Discovers

- **Route Information**
  - HTTP methods (GET, POST, PUT, DELETE, etc.)
  - URI patterns and parameters
  - Route names
  - Action (controller@method or closure)

- **Middleware**
  - Route-specific middleware
  - Group middleware
  - Middleware parameters

- **Route Groups**
  - Prefix configuration
  - Namespace configuration
  - Middleware groups

- **Controllers**
  - Controller class references
  - Controller methods
  - Resource controller routes

### Usage Examples

```bash
# Generate routes documentation
php artisan atlas:generate --type=routes --format=markdown --output=docs/routes.md
```

```php
// Programmatic analysis
$routesData = Atlas::scan('routes', [
    'include_middleware' => true,
    'include_controllers' => true,
    'group_by_prefix' => true,
]);
```

## 💬 Commands

**Purpose**: Document Artisan commands and their configuration

### What Atlas Discovers

- **Command Information**
  - Command signature
  - Command name
  - Description
  - Hidden status

- **Arguments and Options**
  - Required arguments
  - Optional arguments
  - Command options
  - Default values

- **Command Flow**
  - Dependencies
  - Called services
  - File operations

### Usage Examples

```bash
# Generate commands documentation
php artisan atlas:generate --type=commands --format=json --output=api/commands.json
```

```php
// Programmatic analysis
$commandsData = Atlas::scan('commands', [
    'include_signatures' => true,
    'include_descriptions' => true,
]);
```

## 🔧 Services

**Purpose**: Analyze service classes and their dependencies

### What Atlas Discovers

- **Service Information**
  - Class name and namespace
  - File location
  - Service dependencies

- **Methods**
  - Public methods
  - Method parameters
  - Return types

- **Dependencies**
  - Constructor dependencies
  - Injected services
  - External dependencies

- **Design Patterns**
  - Service interfaces
  - Implementation patterns

### Usage Examples

```bash
# Generate services documentation
php artisan atlas:generate --type=services --format=html --output=docs/services.html
```

```php
// Programmatic analysis
$servicesData = Atlas::scan('services', [
    'include_dependencies' => true,
    'include_methods' => true,
]);
```

## 📢 Notifications

**Purpose**: Map notification classes and their channels

### What Atlas Discoveries

- **Notification Information**
  - Notification class name
  - File location
  - Purpose and usage

- **Channels**
  - Available channels (mail, database, slack, etc.)
  - Channel-specific configuration
  - Custom channels

- **Methods**
  - toMail, toDatabase methods
  - Via method configuration
  - Routing methods

- **Dependencies**
  - Required services
  - External integrations

### Usage Examples

```bash
# Generate notifications documentation
php artisan atlas:generate --type=notifications --format=markdown --output=docs/notifications.md
```

```php
// Programmatic analysis
$notificationsData = Atlas::scan('notifications', [
    'include_channels' => true,
    'include_flow' => true,
]);
```

## 🛡️ Middlewares

**Purpose**: Analyze HTTP middleware and their configuration

### What Atlas Discovers

- **Middleware Information**
  - Middleware class name
  - File location
  - Registration in kernel

- **Parameters**
  - Middleware parameters
  - Configuration options
  - Parameter validation

- **Dependencies**
  - Service dependencies
  - External services
  - Configuration dependencies

- **Flow Analysis**
  - Before/after logic
  - Termination handling
  - Exception handling

### Usage Examples

```bash
# Generate middlewares documentation
php artisan atlas:generate --type=middlewares --format=html --output=docs/middlewares.html
```

```php
// Programmatic analysis
$middlewaresData = Atlas::scan('middlewares', [
    'include_parameters' => true,
    'include_dependencies' => true,
]);
```

## 📋 Form Requests

**Purpose**: Document form request validation classes

### What Atlas Discovers

- **Request Information**
  - Form request class name
  - File location
  - Usage context

- **Validation Rules**
  - Rules method content
  - Field validation rules
  - Custom validation rules

- **Authorization**
  - Authorize method logic
  - Permission checks
  - User authorization

- **Additional Methods**
  - Messages customization
  - Attributes customization
  - After validation hooks

### Usage Examples

```bash
# Generate form requests documentation
php artisan atlas:generate --type=form_requests --format=json --output=api/form-requests.json
```

```php
// Programmatic analysis
$formRequestsData = Atlas::scan('form_requests', [
    'include_rules' => true,
    'include_authorization' => true,
    'include_attributes' => true,
]);
```

## Component Analysis Options

Each component type supports various analysis options:

### Common Options

```php
$options = [
    'include_dependencies' => true,    // Include dependency analysis
    'include_methods' => true,         // Include method information
    'detailed' => true,                // Enable detailed analysis
    'include_metadata' => true,        // Include additional metadata
];
```

### Component-Specific Options

```php
// Models
$modelOptions = [
    'include_relationships' => true,
    'include_observers' => true,
    'include_factories' => true,
    'include_scopes' => true,
];

// Routes
$routeOptions = [
    'include_middleware' => true,
    'include_controllers' => true,
    'group_by_prefix' => true,
    'include_parameters' => true,
];

// Services
$serviceOptions = [
    'include_dependencies' => true,
    'include_methods' => true,
    'include_interfaces' => true,
];
```

## Best Practices

### Performance Optimization

- Use component-specific scanning for large applications
- Enable only necessary analysis options
- Consider memory limits for complex applications

### Documentation Generation

- Use HTML format for interactive team documentation
- Use Markdown format for static documentation sites
- Use JSON format for API integration and automation

### CI/CD Integration

```bash
# Generate documentation in build pipeline
php artisan atlas:generate --type=all --format=html --output=public/docs/architecture.html
php artisan atlas:generate --type=models --format=markdown --output=docs/models.md
php artisan atlas:generate --type=routes --format=json --output=api/routes.json
```

## Extending Component Analysis

Atlas is designed to be extensible. You can create custom mappers for additional component types or extend existing mappers with additional functionality.

See the source code in `src/Mappers/` for examples of how component analysis is implemented.