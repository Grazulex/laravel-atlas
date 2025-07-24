# Component Types

Laravel Atlas analyzes 16 different component types in your Laravel application. This guide provides detailed information about each component type and what Atlas discovers.

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
php artisan atlas:export --type=models --format=html --output=docs/models.html
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
php artisan atlas:export --type=routes --format=pdf --output=docs/routes.pdf
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
php artisan atlas:export --type=commands --format=json --output=api/commands.json
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
php artisan atlas:export --type=services --format=html --output=docs/services.html
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

### What Atlas Discovers

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
php artisan atlas:export --type=notifications --format=pdf --output=docs/notifications.pdf
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
php artisan atlas:export --type=middlewares --format=html --output=docs/middlewares.html
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
php artisan atlas:export --type=form_requests --format=json --output=api/form-requests.json
```

```php
// Programmatic analysis
$formRequestsData = Atlas::scan('form_requests', [
    'include_rules' => true,
    'include_authorization' => true,
    'include_attributes' => true,
]);
```

## ⚡ Events

**Purpose**: Analyze Laravel event classes and their listeners

### What Atlas Discovers

- **Event Information**
  - Event class name and namespace
  - File location and structure
  - Event properties and data

- **Listeners**
  - Associated event listeners
  - Listener classes and methods
  - Queue configuration for listeners

- **Broadcasting**
  - Broadcast channels
  - Broadcasting configuration
  - Real-time event handling

- **Properties**
  - Public event properties
  - Event data structure
  - Serialization configuration

### Usage Examples

```bash
# Generate events documentation
php artisan atlas:export --type=events --format=html --output=docs/events.html
```

```php
// Programmatic analysis
$eventsData = Atlas::scan('events', [
    'include_listeners' => true,
    'include_properties' => true,
]);
```

## 🎮 Controllers

**Purpose**: Map application controllers and their actions

### What Atlas Discovers

- **Controller Information**
  - Controller class name and namespace
  - File location and structure
  - Parent controller inheritance

- **Actions**
  - Controller methods and actions
  - Route bindings
  - Parameter requirements

- **Middleware**
  - Controller middleware
  - Action-specific middleware
  - Middleware parameters

- **Dependencies**
  - Constructor dependencies
  - Action dependencies
  - Service injections

### Usage Examples

```bash
# Generate controllers documentation
php artisan atlas:export --type=controllers --format=pdf --output=docs/controllers.pdf
```

```php
// Programmatic analysis
$controllersData = Atlas::scan('controllers', [
    'include_actions' => true,
    'include_dependencies' => true,
]);
```

## 🔄 Resources

**Purpose**: Analyze API resource classes and transformations

### What Atlas Discovers

- **Resource Information**
  - Resource class name and namespace
  - File location and structure
  - Resource type (single/collection)

- **Transformations**
  - toArray method implementation
  - Data transformation logic
  - Conditional fields

- **Relationships**
  - Related resource classes
  - Resource relationships
  - Nested resource handling

### Usage Examples

```bash
# Generate resources documentation
php artisan atlas:export --type=resources --format=html --output=docs/resources.html
```

## ⚙️ Jobs

**Purpose**: Map queue job classes and their configuration

### What Atlas Discovers

- **Job Information**
  - Job class name and namespace
  - File location and structure
  - Job purpose and functionality

- **Queue Configuration**
  - Queue name and connection
  - Job priority and delay
  - Retry configuration

- **Dependencies**
  - Constructor dependencies
  - Service injections
  - External service usage

- **Handler Logic**
  - Handle method implementation
  - Job processing logic
  - Error handling

### Usage Examples

```bash
# Generate jobs documentation
php artisan atlas:export --type=jobs --format=json --output=api/jobs.json
```

```php
// Programmatic analysis
$jobsData = Atlas::scan('jobs', [
    'include_dependencies' => true,
    'include_queue_config' => true,
]);
```

## 🎯 Actions

**Purpose**: Analyze single action controllers and action classes

### What Atlas Discovers

- **Action Information**
  - Action class name and namespace
  - File location and structure
  - Single responsibility implementation

- **Invocation**
  - __invoke method implementation
  - Parameter requirements
  - Return types

- **Dependencies**
  - Constructor dependencies
  - Service injections
  - External integrations

### Usage Examples

```bash
# Generate actions documentation
php artisan atlas:export --type=actions --format=html --output=docs/actions.html
```

## 🔐 Policies

**Purpose**: Map authorization policy classes and their methods

### What Atlas Discovers

- **Policy Information**
  - Policy class name and namespace
  - File location and structure
  - Associated model classes

- **Authorization Methods**
  - Policy method implementations
  - Permission logic
  - User authorization checks

- **Gate Integration**
  - Gate definitions
  - Policy registration
  - Authorization flow

### Usage Examples

```bash
# Generate policies documentation
php artisan atlas:export --type=policies --format=pdf --output=docs/policies.pdf
```

## ✅ Rules

**Purpose**: Analyze custom validation rule classes

### What Atlas Discovers

- **Rule Information**
  - Rule class name and namespace
  - File location and structure
  - Validation purpose

- **Validation Logic**
  - Passes method implementation
  - Validation criteria
  - Error message handling

- **Dependencies**
  - External service usage
  - Database interactions
  - API integrations

### Usage Examples

```bash
# Generate rules documentation
php artisan atlas:export --type=rules --format=html --output=docs/rules.html
```

## 👂 Listeners

**Purpose**: Map event listener classes and their handlers

### What Atlas Discovers

- **Listener Information**
  - Listener class name and namespace
  - File location and structure
  - Associated events

- **Handler Methods**
  - Handle method implementation
  - Event processing logic
  - Response handling

- **Queue Configuration**
  - Queued listener setup
  - Queue connection and name
  - Processing options

### Usage Examples

```bash
# Generate listeners documentation
php artisan atlas:export --type=listeners --format=json --output=api/listeners.json
```

## 👁️ Observers

**Purpose**: Analyze model observer classes and lifecycle hooks

### What Atlas Discovers

- **Observer Information**
  - Observer class name and namespace
  - File location and structure
  - Associated model classes

- **Lifecycle Hooks**
  - Model event methods (creating, created, updating, etc.)
  - Hook implementation logic
  - Data transformation

- **Dependencies**
  - Service dependencies
  - External integrations
  - Database interactions

### Usage Examples

```bash
# Generate observers documentation
php artisan atlas:export --type=observers --format=pdf --output=docs/observers.pdf
```

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
php artisan atlas:export --type=models --format=html --output=docs/models.html
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
php artisan atlas:export --type=routes --format=html --output=docs/routes.html
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
php artisan atlas:export --type=commands --format=json --output=api/commands.json
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
php artisan atlas:export --type=services --format=html --output=docs/services.html
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
php artisan atlas:export --type=notifications --format=pdf --output=docs/notifications.pdf
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
php artisan atlas:export --type=middlewares --format=html --output=docs/middlewares.html
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
php artisan atlas:export --type=form_requests --format=json --output=api/form-requests.json
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

- Use HTML format for interactive team documentation with dark mode support
- Use PDF format for professional presentations and compliance reports
- Use JSON format for API integration and automation

### CI/CD Integration

```bash
# Generate documentation in build pipeline
php artisan atlas:export --type=all --format=html --output=public/docs/architecture.html
php artisan atlas:export --type=models --format=pdf --output=docs/models.pdf
php artisan atlas:export --type=routes --format=json --output=api/routes.json
```

## Extending Component Analysis

Atlas is designed to be extensible. You can create custom mappers for additional component types or extend existing mappers with additional functionality.

See the source code in `src/Mappers/` for examples of how component analysis is implemented.