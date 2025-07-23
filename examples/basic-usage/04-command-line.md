# Command-Line Usage Examples

This guide demonstrates various ways to use Laravel Atlas from the command line.

## 📋 Basic Commands

### Simple Generation

```bash
# Generate complete application map (JSON output)
php artisan atlas:generate

# Generate with specific output location
php artisan atlas:generate --output=storage/atlas/complete-map.json
```

### Component-Specific Scanning

```bash
# Scan only models
php artisan atlas:generate --type=models

# Scan only routes  
php artisan atlas:generate --type=routes

# Scan only services
php artisan atlas:generate --type=services

# Scan only commands
php artisan atlas:generate --type=commands

# Scan only jobs
php artisan atlas:generate --type=jobs

# Scan only events
php artisan atlas:generate --type=events

# Scan all components
php artisan atlas:generate --type=all
```

## 🎨 Export Formats

### JSON Export (Default)

```bash
# Basic JSON export
php artisan atlas:generate --format=json

# JSON export with pretty printing
php artisan atlas:generate --format=json --pretty

# JSON export to specific file
php artisan atlas:generate --format=json --output=docs/architecture.json
```

### Markdown Documentation

```bash
# Generate markdown documentation
php artisan atlas:generate --format=markdown

# Markdown with detailed sections
php artisan atlas:generate --format=markdown --detailed

# Save to specific location
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md
```

### Interactive HTML Reports

```bash
# Generate interactive HTML report
php artisan atlas:generate --format=html

# HTML with enhanced styling
php artisan atlas:generate --format=html --theme=modern

# Save to public directory for web access
php artisan atlas:generate --format=html --output=public/docs/atlas.html
```

### PDF Reports

```bash
# Generate PDF report
php artisan atlas:generate --format=pdf

# PDF with custom styling
php artisan atlas:generate --format=pdf --template=professional

# Save to reports directory
php artisan atlas:generate --format=pdf --output=reports/architecture.pdf
```

### Image Diagrams

```bash
# Generate PNG diagram
php artisan atlas:generate --format=image

# Generate specific image format
php artisan atlas:generate --format=image --image-type=png
php artisan atlas:generate --format=image --image-type=jpg
php artisan atlas:generate --format=image --image-type=svg

# Custom image size
php artisan atlas:generate --format=image --width=1920 --height=1080
```

## ⚙️ Advanced Options

### Filtering and Scoping

```bash
# Include only specific directories
php artisan atlas:generate --include-paths=app/Models,app/Services

# Exclude vendor directories
php artisan atlas:generate --exclude-vendors

# Include specific namespaces
php artisan atlas:generate --namespaces=App\\Models,App\\Services

# Exclude test files
php artisan atlas:generate --exclude-tests
```

### Analysis Depth

```bash
# Shallow analysis (faster)
php artisan atlas:generate --depth=shallow

# Deep analysis (more detailed)
php artisan atlas:generate --depth=deep

# Maximum depth limit
php artisan atlas:generate --max-depth=5
```

### Performance Options

```bash
# Enable caching for faster subsequent runs
php artisan atlas:generate --cache

# Clear cache before running
php artisan atlas:generate --fresh

# Set memory limit
php artisan atlas:generate --memory-limit=1G

# Parallel processing
php artisan atlas:generate --parallel=4
```

## 📊 Specialized Reports

### Model Analysis

```bash
# Models with relationships
php artisan atlas:generate --type=models --include-relationships

# Models with observers and events
php artisan atlas:generate --type=models --include-observers --include-events

# Models with scopes and casts
php artisan atlas:generate --type=models --include-scopes --include-casts
```

### Route Analysis

```bash
# Routes with middleware details
php artisan atlas:generate --type=routes --include-middleware

# Routes with controller information
php artisan atlas:generate --type=routes --include-controllers

# Routes grouped by prefix
php artisan atlas:generate --type=routes --group-by-prefix
```

### Service Analysis

```bash
# Services with dependencies
php artisan atlas:generate --type=services --include-dependencies

# Services with method signatures
php artisan atlas:generate --type=services --include-methods

# Services with interface implementations
php artisan atlas:generate --type=services --include-interfaces
```

## 🔧 Configuration Options

### Custom Configuration

```bash
# Use custom configuration file
php artisan atlas:generate --config=config/custom-atlas.php

# Override specific configuration values
php artisan atlas:generate --set=output_path=/custom/path
php artisan atlas:generate --set=include_vendors=true
```

### Environment-Specific Generation

```bash
# Generate for specific environment
php artisan atlas:generate --env=production

# Skip environment-specific components
php artisan atlas:generate --skip-env-specific
```

## 🚀 CI/CD Integration Examples

### GitHub Actions

```bash
# Generate documentation in CI
name: Generate Atlas Documentation
on: [push]
jobs:
  docs:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Generate Atlas Maps
        run: |
          php artisan atlas:generate --type=all --format=json --output=docs/architecture.json
          php artisan atlas:generate --type=all --format=markdown --output=docs/ARCHITECTURE.md
          php artisan atlas:generate --type=all --format=html --output=public/docs/atlas.html
```

### Laravel Forge Deployment

```bash
# Add to deployment script
php artisan atlas:generate --type=all --format=html --output=public/docs/atlas.html --cache
```

### Local Development Workflow

```bash
# Quick development check
php artisan atlas:generate --type=models --format=json --cache

# Full documentation update
php artisan atlas:generate --type=all --format=markdown --output=docs/ARCHITECTURE.md --fresh

# Team review preparation
php artisan atlas:generate --type=all --format=html --output=public/atlas/team-review.html
```

## 💡 Pro Tips

### Batch Operations

```bash
# Generate multiple formats at once
php artisan atlas:generate --type=all --format=json,markdown,html --output-dir=docs/atlas/

# Generate for multiple component types
php artisan atlas:generate --type=models,routes,services --format=html
```

### Performance Optimization

```bash
# For large applications
php artisan atlas:generate --cache --parallel=4 --memory-limit=2G --depth=shallow

# For detailed analysis (smaller apps)
php artisan atlas:generate --depth=deep --include-relationships --include-dependencies
```

### Quality Assurance

```bash
# Validate atlas generation (returns error code if issues found)
php artisan atlas:validate

# Generate with warnings for potential issues
php artisan atlas:generate --verbose --warnings
```

## 🔍 Troubleshooting Commands

```bash
# Debug atlas configuration
php artisan atlas:config

# Check atlas status
php artisan atlas:status

# Clear atlas cache
php artisan atlas:clear-cache

# Test atlas functionality
php artisan atlas:test --component=models
```

---

**Need more help?** Run `php artisan atlas:generate --help` for complete option details.