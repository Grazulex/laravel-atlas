# Image Export Examples

These examples demonstrate how to use Laravel Atlas to generate visual diagrams as images.

## 📋 Prerequisites

- Laravel Atlas installed: `composer require grazulex/laravel-atlas --dev`
- GD extension enabled: `ext-gd`

## 🖼️ Basic Image Generation

### 1. Generate Architecture Diagrams

```bash
# Generate basic architecture diagram
php artisan atlas:generate --format=image

# Generate specific component diagrams
php artisan atlas:generate --type=models --format=image --output=diagrams/models.png
php artisan atlas:generate --type=routes --format=image --output=diagrams/routes.png
php artisan atlas:generate --type=services --format=image --output=diagrams/services.png

# Generate complete application architecture
php artisan atlas:generate --type=all --format=image --output=public/architecture.png
```

### 2. Custom Image Configuration

```bash
# High-resolution diagram for presentations
php artisan atlas:generate --type=models --format=image --output=presentation/models-hd.png

# Wide format for route visualization
php artisan atlas:generate --type=routes --format=image --output=diagrams/routes-wide.png
```

## 📊 Programmatic Image Generation

### 1. Basic Image Export

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate image from scanned data
$modelData = Atlas::scan('models');
$imageData = Atlas::export('models', 'image');

// Save to file
file_put_contents('storage/models-diagram.png', $imageData);

// Generate multiple component diagrams
$components = ['models', 'controllers', 'routes', 'services'];

foreach ($components as $component) {
    $imageData = Atlas::export($component, 'image');
    file_put_contents("public/diagrams/{$component}.png", $imageData);
}

echo "Generated " . count($components) . " diagrams\n";
```

### 2. Custom Image Configuration

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate high-quality PNG diagram
$imageData = Atlas::export('models', 'image', [
    'format' => 'png',
    'width' => 1920,
    'height' => 1080,
    'background_color' => 'white',
    'include_legend' => true,
]);

file_put_contents('docs/models-hd.png', $imageData);

// Generate JPG for smaller file size
$jpegData = Atlas::export('routes', 'image', [
    'format' => 'jpg',
    'width' => 1440,
    'height' => 900,
    'background_color' => '#f8f9fa',
    'quality' => 85,
]);

file_put_contents('public/routes.jpg', $jpegData);
```

## 🎨 Visual Customization Examples

### 1. Different Layout Styles

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Hierarchical layout for model relationships
$hierarchicalImage = Atlas::export('models', 'image', [
    'layout' => 'hierarchical',
    'width' => 1600,
    'height' => 1200,
    'include_legend' => true,
    'node_spacing' => 100,
]);

file_put_contents('diagrams/models-hierarchy.png', $hierarchicalImage);

// Circular layout for service dependencies
$circularImage = Atlas::export('services', 'image', [
    'layout' => 'circular',
    'width' => 1200,
    'height' => 1200,
    'background_color' => '#1e293b',
    'text_color' => 'white',
]);

file_put_contents('diagrams/services-circular.png', $circularImage);

// Grid layout for component overview
$gridImage = Atlas::export('all', 'image', [
    'layout' => 'grid',
    'width' => 2400,
    'height' => 1800,
    'include_legend' => true,
    'show_connections' => true,
]);

file_put_contents('public/architecture-overview.png', $gridImage);
```

### 2. Theme-Based Styling

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Dark theme for presentations
$darkTheme = Atlas::export('controllers', 'image', [
    'theme' => 'dark',
    'background_color' => '#0f172a',
    'text_color' => '#f1f5f9',
    'accent_color' => '#3b82f6',
    'width' => 1920,
    'height' => 1080,
]);

file_put_contents('presentation/controllers-dark.png', $darkTheme);

// Light theme for documentation
$lightTheme = Atlas::export('models', 'image', [
    'theme' => 'light',
    'background_color' => '#ffffff',
    'text_color' => '#1f2937',
    'accent_color' => '#059669',
    'border_color' => '#e5e7eb',
]);

file_put_contents('docs/models-light.png', $lightTheme);

// Minimal theme for clean diagrams
$minimalTheme = Atlas::export('routes', 'image', [
    'theme' => 'minimal',
    'background_color' => '#fafafa',
    'show_borders' => false,
    'font_size' => 12,
    'node_padding' => 8,
]);

file_put_contents('diagrams/routes-minimal.png', $minimalTheme);
```

## 📈 Advanced Image Generation

### 1. Multi-Component Architecture Diagrams

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate comprehensive architecture diagram
$architectureData = [
    'models' => Atlas::scan('models'),
    'controllers' => Atlas::scan('controllers'),  
    'services' => Atlas::scan('services'),
    'routes' => Atlas::scan('routes'),
];

// Create layered architecture diagram
$layeredImage = Atlas::export($architectureData, 'image', [
    'layout' => 'layered',
    'width' => 2000,
    'height' => 1500,
    'layers' => [
        'routes' => ['y' => 100, 'color' => '#3b82f6'],
        'controllers' => ['y' => 400, 'color' => '#10b981'],
        'services' => ['y' => 700, 'color' => '#f59e0b'],
        'models' => ['y' => 1000, 'color' => '#ef4444'],
    ],
    'show_layer_labels' => true,
    'include_legend' => true,
]);

file_put_contents('docs/layered-architecture.png', $layeredImage);
```

### 2. Interactive Map Generation

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate detailed component map with hover information
$interactiveData = Atlas::export('all', 'image', [
    'width' => 2400,
    'height' => 1600,
    'include_tooltips' => true,
    'show_metrics' => true,
    'include_legend' => true,
    'export_metadata' => true,
]);

// Save main image
file_put_contents('public/interactive-map.png', $interactiveData['image']);

// Save metadata for web integration
file_put_contents('public/interactive-map.json', json_encode($interactiveData['metadata']));
```

## 🔧 Batch Image Generation

### 1. Generate All Component Diagrams

```bash
#!/bin/bash
# generate-all-diagrams.sh

echo "Generating component diagrams..."

# Create diagrams directory
mkdir -p public/diagrams

# Generate individual component diagrams
components=("models" "controllers" "routes" "services" "jobs" "events" "commands" "middleware" "policies" "resources" "notifications" "requests" "rules" "observers" "listeners" "actions")

for component in "${components[@]}"
do
    echo "Generating ${component} diagram..."
    php artisan atlas:generate --type=${component} --format=image --output=public/diagrams/${component}.png
done

# Generate combined architecture diagram
echo "Generating complete architecture diagram..."
php artisan atlas:generate --type=all --format=image --output=public/diagrams/complete-architecture.png

echo "All diagrams generated successfully!"
```

### 2. Automated Diagram Updates

```php
<?php

// Script to update diagrams when code changes
use LaravelAtlas\Facades\Atlas;

$diagramConfigs = [
    'models' => [
        'output' => 'public/diagrams/models.png',
        'config' => [
            'layout' => 'hierarchical',
            'include_relationships' => true,
            'width' => 1600,
            'height' => 1200,
        ],
    ],
    'services' => [
        'output' => 'public/diagrams/services.png', 
        'config' => [
            'layout' => 'circular',
            'show_dependencies' => true,
            'width' => 1400,
            'height' => 1400,
        ],
    ],
    'routes' => [
        'output' => 'public/diagrams/routes.png',
        'config' => [
            'layout' => 'tree',
            'group_by_prefix' => true,
            'width' => 1800,
            'height' => 1000,
        ],
    ],
];

foreach ($diagramConfigs as $type => $config) {
    echo "Updating {$type} diagram...\n";
    
    $imageData = Atlas::export($type, 'image', $config['config']);
    file_put_contents($config['output'], $imageData);
    
    echo "✓ {$config['output']} updated\n";
}

echo "All diagrams updated successfully!\n";
```

## 📊 Quality and Optimization

### 1. Image Quality Optimization

```php
<?php

use LaravelAtlas\Facades\Atlas;

// High-quality diagrams for print
$printQuality = Atlas::export('models', 'image', [
    'format' => 'png',
    'width' => 3000,
    'height' => 2000,
    'dpi' => 300,
    'compression' => 'lossless',
    'include_legend' => true,
]);

file_put_contents('print/models-print.png', $printQuality);

// Web-optimized diagrams
$webOptimized = Atlas::export('routes', 'image', [
    'format' => 'jpg',
    'width' => 1200,
    'height' => 800,
    'quality' => 80,
    'progressive' => true,
]);

file_put_contents('public/routes-web.jpg', $webOptimized);

// Thumbnail generation
$thumbnail = Atlas::export('services', 'image', [
    'width' => 400,
    'height' => 300,
    'format' => 'png',
    'simplify_layout' => true,
    'hide_details' => true,
]);

file_put_contents('public/thumbs/services-thumb.png', $thumbnail);
```

### 2. Performance Monitoring

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Monitor image generation performance
$start = microtime(true);

$imageData = Atlas::export('all', 'image', [
    'width' => 2000,
    'height' => 1500,
    'layout' => 'hierarchical',
]);

$generationTime = microtime(true) - $start;
$imageSize = strlen($imageData);

echo "Generation Statistics:\n";
echo "Time: " . round($generationTime, 2) . " seconds\n";  
echo "Size: " . round($imageSize / 1024 / 1024, 2) . " MB\n";

file_put_contents('public/architecture-perf.png', $imageData);

// Log performance metrics
file_put_contents('logs/image-generation.log', json_encode([
    'timestamp' => now()->toISOString(),
    'generation_time' => $generationTime,
    'image_size' => $imageSize,
    'type' => 'all',
]) . "\n", FILE_APPEND);
```

## 💡 Integration Examples

### 1. Documentation Integration

```markdown
<!-- In your README.md or documentation -->
# Application Architecture

Here's our current application architecture:

![Architecture Overview](public/diagrams/complete-architecture.png)

## Component Details

### Models
![Models](public/diagrams/models.png)

### Services  
![Services](public/diagrams/services.png)

### Routes
![Routes](public/diagrams/routes.png)
```

### 2. CI/CD Integration

```yaml
# .github/workflows/documentation.yml
name: Update Architecture Diagrams

on:
  push:
    branches: [ main ]
    paths: 
      - 'app/**'
      - 'routes/**'

jobs:
  update-diagrams:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: gd
          
      - name: Install dependencies
        run: composer install --no-dev
        
      - name: Generate diagrams
        run: |
          php artisan atlas:generate --type=all --format=image --output=docs/architecture.png
          php artisan atlas:generate --type=models --format=image --output=docs/models.png
          php artisan atlas:generate --type=routes --format=image --output=docs/routes.png
          
      - name: Commit diagrams
        run: |
          git config --local user.email "action@github.com"
          git config --local user.name "GitHub Action"
          git add docs/*.png
          git commit -m "Update architecture diagrams" || exit 0
          git push
```

## 🔗 Related Examples

- [HTML Reports](html.md) - Interactive visualizations
- [PDF Reports](pdf.md) - Printable documentation
- [Model Analysis](../models.md) - Generating model relationship diagrams

---

**Need help?** Check our [documentation](../../docs/) or open an issue on GitHub.