# Laravel Atlas Examples

This directory contains working examples demonstrating how to use Laravel Atlas with the currently implemented features.

## Available Component Types

Laravel Atlas currently supports **3 core component types**:

- **models** - Eloquent models with relationships and metadata
- **routes** - Application routes with middleware and controller information
- **commands** - Artisan commands with signatures and descriptions

## Examples Overview

- [Basic Usage](basic-usage.php) - Simple scanning and exporting
- [Models Analysis](models-example.php) - Detailed model mapping with relationships
- [Routes Analysis](routes-example.php) - Route mapping with middleware information
- [Commands Analysis](commands-example.php) - Artisan command analysis
- [Complete Application Map](complete-analysis.php) - Generate comprehensive application documentation

## Quick Start

```bash
# Generate JSON output for all components
php artisan atlas:generate --format=json

# Generate specific component documentation
php artisan atlas:generate --type=models --format=markdown --output=docs/models.md
php artisan atlas:generate --type=routes --format=html --output=docs/routes.html
php artisan atlas:generate --type=commands --format=json --output=docs/commands.json
```

## Working with the Examples

All examples in this directory are tested and work with the current implementation. They demonstrate real functionality without referencing unimplemented features.

Run any example with:

```bash
php examples/[example-file].php
```

Make sure to run the examples from the root directory of your Laravel application where Laravel Atlas is installed.