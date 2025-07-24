# Laravel Atlas Examples

This directory contains working examples demonstrating how to use Laravel Atlas with the currently implemented features.

## Available Component Types

Laravel Atlas currently supports **7 component types**:

- **models** - Eloquent models with relationships and metadata
- **routes** - Application routes with middleware and controller information
- **commands** - Artisan commands with signatures and descriptions
- **services** - Application service classes with methods and dependencies
- **notifications** - Laravel notification classes with channels and methods  
- **middlewares** - HTTP middleware with parameters and dependencies
- **form_requests** - Form request validation classes with rules and authorization

## Examples Overview

- [Basic Usage](basic-usage.php) - Simple scanning and exporting
- [HTML Export Example](html-export-example.php) - **NEW** Interactive HTML dashboard generation
- [Models Analysis](models-example.php) - Detailed model mapping with relationships
- [Routes Analysis](routes-example.php) - Route mapping with middleware information
- [Commands Analysis](commands-example.php) - Artisan command analysis
- [Services Analysis](services-example.php) - Service class mapping with dependencies
- [Notifications Analysis](notifications-example.php) - Notification mapping with channels
- [Middlewares Analysis](middlewares-example.php) - Middleware analysis with parameters
- [Form Requests Analysis](form-requests-example.php) - Form request validation mapping
- [Complete Application Map](complete-analysis.php) - Generate comprehensive application documentation

## Quick Start

```bash
# Generate JSON output for all components
php artisan atlas:generate --format=json

# Generate interactive HTML dashboard - NEW FEATURE
php artisan atlas:generate --format=html --output=docs/architecture.html

# Generate specific component documentation
php artisan atlas:generate --type=models --format=markdown --output=docs/models.md
php artisan atlas:generate --type=routes --format=html --output=docs/routes.html
php artisan atlas:generate --type=commands --format=json --output=docs/commands.json
php artisan atlas:generate --type=services --format=markdown --output=docs/services.md
php artisan atlas:generate --type=notifications --format=html --output=docs/notifications.html
php artisan atlas:generate --type=middlewares --format=json --output=docs/middlewares.json
php artisan atlas:generate --type=form_requests --format=markdown --output=docs/form-requests.md
```

## Working with the Examples

All examples in this directory are tested and work with the current implementation. They demonstrate real functionality without referencing unimplemented features.

Run any example with:

```bash
php examples/[example-file].php
```

Make sure to run the examples from the root directory of your Laravel application where Laravel Atlas is installed.