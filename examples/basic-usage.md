# Basic Usage Examples

These examples demonstrate the fundamental ways to use Laravel Atlas for architectural analysis.

## 📋 Prerequisites

- Laravel Atlas installed: `composer require grazulex/laravel-atlas --dev`
- Laravel application with some models, routes, and controllers

## 🚀 Getting Started

### 1. Your First Architecture Scan

```bash
# Generate a basic JSON map of your entire application
php artisan atlas:generate
```

This creates a JSON output showing all discovered components in your application.

**Expected Output:**
```json
{
  "atlas_version": "1.0.0",
  "generated_at": "2024-01-01T12:00:00.000000Z",
  "generation_time_ms": 245.67,
  "type": "all",
  "format": "json",
  "data": {
    "models": {
      "type": "models",
      "data": [...]
    },
    "routes": {
      "type": "routes", 
      "data": [...]
    }
  }
}
```

### 2. Scan Specific Component Types

```bash
# Scan only models
php artisan atlas:generate --type=models

# Scan only routes
php artisan atlas:generate --type=routes

# Scan only controllers
php artisan atlas:generate --type=controllers
```

### 3. Choose Different Output Formats

```bash
# Generate readable Markdown documentation
php artisan atlas:generate --format=markdown

# Generate visual diagram as image
php artisan atlas:generate --format=image

# Generate interactive HTML with intelligent workflow
php artisan atlas:generate --format=html

# Generate PDF report
php artisan atlas:generate --format=pdf

# Generate PHP data for custom processing
php artisan atlas:generate --format=php
```

### 4. Save to Files

```bash
# Save JSON to file
php artisan atlas:generate --output=storage/architecture.json

# Save Markdown documentation
php artisan atlas:generate --format=markdown --output=docs/ARCHITECTURE.md

# Save visual diagram as image
php artisan atlas:generate --format=image --output=diagrams/app-structure.png

# Save intelligent HTML report
php artisan atlas:generate --format=html --output=public/architecture.html
```

### 5. Get Detailed Information

```bash
# Include detailed information about components
php artisan atlas:generate --type=models

# Combine with specific type and format
php artisan atlas:generate --type=models --format=markdown --output=docs/models.md
```

## 💻 Programmatic Usage

### Basic PHP API Usage

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Scan all components
$allData = Atlas::scan('all');
echo "Found " . count($allData['data']) . " component types\n";

// Scan specific component type
$modelData = Atlas::scan('models');
echo "Found " . count($modelData['data']) . " models\n";

// Export to JSON
$jsonOutput = Atlas::export('models', 'json');
file_put_contents('models.json', $jsonOutput);

// Export to Markdown
$markdownDocs = Atlas::export('routes', 'markdown');
file_put_contents('routes.md', $markdownDocs);
```

### Working with the Data

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Get model information
$modelData = Atlas::scan('models', [
    'include_relationships' => true
]);

foreach ($modelData['data'] as $model) {
    echo "Model: {$model['name']}\n";
    echo "Path: {$model['path']}\n";
    
    if (isset($model['relationships'])) {
        echo "Relationships:\n";
        foreach ($model['relationships'] as $relationship) {
            echo "  - {$relationship['type']}: {$relationship['related']}\n";
        }
    }
    echo "\n";
}
```

### Simple Service Integration

```php
<?php

namespace App\Services;

use LaravelAtlas\Facades\Atlas;

class DocumentationService
{
    public function generateDocumentation(): string
    {
        // Generate comprehensive architecture documentation
        return Atlas::export('all', 'markdown', [
            'include_toc' => true,
            'include_stats' => true,
            'detailed_sections' => true
        ]);
    }
    
    public function getApplicationStats(): array
    {
        $allData = Atlas::scan('all');
        
        $stats = [];
        foreach ($allData['data'] as $type => $typeData) {
            $stats[$type] = count($typeData['data'] ?? []);
        }
        
        return $stats;
    }
}
```

## 📊 Understanding the Output

### JSON Structure

Every Laravel Atlas scan returns data in this consistent structure:

```json
{
  "atlas_version": "1.0.0",           // Laravel Atlas version
  "generated_at": "2024-01-01...",    // Generation timestamp
  "generation_time_ms": 150.25,       // Time taken to generate
  "type": "models",                    // Component type scanned
  "format": "json",                    // Output format
  "options": {                         // Options used for scanning
    "detailed": false
  },
  "summary": {                         // Summary statistics
    "total_components": 5
  },
  "data": {                           // Actual component data
    "models": {
      "type": "models",
      "scan_path": "/app/Models",
      "data": [
        // Individual component entries
      ]
    }
  }
}
```

### Component Data Structure

Each component (model, route, controller, etc.) includes:

```json
{
  "name": "User",                      // Component name
  "namespace": "App\\Models",          // Full namespace
  "path": "/app/Models/User.php",      // File path
  "created_at": "2024-01-01...",       // File creation time
  "modified_at": "2024-01-02...",      // Last modification time
  "size": 2048,                        // File size in bytes
  // ... component-specific data
}
```

## 🎯 Common Use Cases

### 1. Quick Application Overview

```bash
# Get a quick overview of your application structure
php artisan atlas:generate --format=markdown | head -50
```

### 2. Model Relationship Visualization

```bash
# Create a visual diagram of model relationships
php artisan atlas:generate --type=models --format=image --output=models.png
```

### 3. Route Documentation

```bash
# Generate route documentation for API docs
php artisan atlas:generate --type=routes --format=markdown --output=API_ROUTES.md
```

### 4. Architecture Review Preparation

```bash
# Generate comprehensive documentation for code review
php artisan atlas:generate --type=all --format=html --output=public/architecture-review.html
```

### 5. CI/CD Documentation

```bash
#!/bin/bash
# In your deployment script
echo "Generating architecture documentation..."
php artisan atlas:generate --format=json --output=public/api/architecture.json
php artisan atlas:generate --format=markdown --output=docs/CURRENT_ARCHITECTURE.md
echo "Documentation updated!"
```

## ⚠️ Common Pitfalls

### 1. Large Application Performance
For large applications, scanning all components can be slow:
```bash
# Instead of scanning everything at once
php artisan atlas:generate  # Slow for large apps

# Scan specific types
php artisan atlas:generate --type=models    # Faster
php artisan atlas:generate --type=routes    # Faster
```

### 2. File Permissions
Ensure Laravel Atlas can write to output directories:
```bash
# Create output directory with proper permissions
mkdir -p storage/atlas
chmod 755 storage/atlas
```

### 3. Memory Usage
For detailed scans of large applications, you might need more memory:
```bash
# Temporarily increase memory limit
php -d memory_limit=512M artisan atlas:generate --detailed
```

## 🔗 Next Steps

After mastering these basics, explore:

- [Command Line Examples](command-line.md) - Advanced CLI usage
- [Programmatic Usage](programmatic.md) - Complex PHP integration
- [Model Analysis](models.md) - Deep model relationship analysis
- [Export Formats](exports/) - Detailed format-specific examples

## 💡 Tips

1. **Start Simple**: Begin with basic scans before using detailed options
2. **Save Everything**: Always output to files for easier sharing and version control
3. **Pick the Right Format**: JSON for processing, Markdown for documentation, HTML for intelligent interactive exploration, Image for visual presentations
4. **Use Version Control**: Commit generated documentation to track architectural changes
5. **Automate**: Include documentation generation in your build/deployment process