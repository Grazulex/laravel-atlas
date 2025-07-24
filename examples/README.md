# Laravel Atlas Examples

This directory contains working examples demonstrating how to use Laravel Atlas with all currently implemented features.

## Available Component Types

Laravel Atlas currently supports **16 component types**:

- **models** - Eloquent models with relationships and metadata
- **routes** - Application routes with middleware and controller information
- **commands** - Artisan commands with signatures and descriptions
- **services** - Application service classes with methods and dependencies
- **notifications** - Laravel notification classes with channels and methods  
- **middlewares** - HTTP middleware with parameters and dependencies
- **form_requests** - Form request validation classes with rules and authorization
- **events** - Laravel event classes with their properties and methods
- **controllers** - Application controllers with their actions and dependencies
- **resources** - API resource classes with their transformations
- **jobs** - Queue job classes with their handles and dependencies
- **actions** - Single action controllers and action classes
- **policies** - Authorization policy classes with their methods
- **rules** - Custom validation rule classes
- **listeners** - Event listener classes with their handlers
- **observers** - Model observer classes with their lifecycle hooks

## Examples Overview

- [Basic Usage](basic-usage.php) - Simple scanning and exporting
- [HTML Export Example](html-export-example.php) - Interactive HTML dashboard generation
- [PDF Export Example](pdf-export-example.php) - **NEW** Professional PDF report generation
- [Models Analysis](models-example.php) - Detailed model mapping with relationships
- [Routes Analysis](routes-example.php) - Route mapping with middleware information
- [Commands Analysis](commands-example.php) - Artisan command analysis
- [Services Analysis](services-example.php) - Service class mapping with dependencies
- [Notifications Analysis](notifications-example.php) - Notification mapping with channels
- [Middlewares Analysis](middlewares-example.php) - Middleware analysis with parameters
- [Form Requests Analysis](form-requests-example.php) - Form request validation mapping
- [Events Analysis](events-example.php) - **NEW** Event class mapping with listeners
- [Controllers Analysis](controllers-example.php) - **NEW** Controller mapping with actions
- [Jobs Analysis](jobs-example.php) - **NEW** Job class mapping with queue configuration
- [Complete Application Map](complete-analysis.php) - Generate comprehensive application documentation

## Export Formats

All examples demonstrate the **3 available export formats**:

- **JSON** - Machine-readable data format for API integration
- **HTML** - Interactive dashboard with dark mode and responsive design
- **PDF** - Professional documentation suitable for presentations and reports

## Quick Start

```bash
# Generate JSON output for all components
php artisan atlas:export --format=json

# Generate interactive HTML dashboard
php artisan atlas:export --format=html --output=docs/architecture.html

# Generate professional PDF documentation
php artisan atlas:export --format=pdf --output=docs/architecture.pdf

# Generate specific component documentation
php artisan atlas:export --type=models --format=html --output=docs/models.html
php artisan atlas:export --type=routes --format=json --output=docs/routes.json
php artisan atlas:export --type=commands --format=pdf --output=docs/commands.pdf
php artisan atlas:export --type=services --format=html --output=docs/services.html
php artisan atlas:export --type=notifications --format=json --output=docs/notifications.json
php artisan atlas:export --type=middlewares --format=pdf --output=docs/middlewares.pdf
php artisan atlas:export --type=form_requests --format=html --output=docs/form-requests.html
php artisan atlas:export --type=events --format=json --output=docs/events.json
php artisan atlas:export --type=controllers --format=html --output=docs/controllers.html
php artisan atlas:export --type=jobs --format=pdf --output=docs/jobs.pdf
```

## Working with the Examples

All examples in this directory are tested and work with the current implementation. They demonstrate real functionality without referencing unimplemented features.

Run any example with:

```bash
php examples/[example-file].php
```

Make sure to run the examples from the root directory of your Laravel application where Laravel Atlas is installed.