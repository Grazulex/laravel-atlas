# HTML Export Examples

These examples demonstrate how to use Laravel Atlas to generate interactive HTML documentation with the intelligent workflow.

## 📋 Prerequisites

- Laravel Atlas installed: `composer require grazulex/laravel-atlas --dev`

## 🌐 Basic HTML Generation

### 1. Generate Interactive HTML Documentation

```bash
# Generate basic HTML documentation
php artisan atlas:generate --format=html

# Generate specific component documentation
php artisan atlas:generate --type=models --format=html --output=docs/models.html
php artisan atlas:generate --type=routes --format=html --output=docs/routes.html
php artisan atlas:generate --type=services --format=html --output=docs/services.html

# Generate complete application documentation with intelligent workflow
php artisan atlas:generate --type=all --format=html --output=public/architecture.html
```

### 2. Programmatic HTML Generation

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate basic HTML documentation
$htmlOutput = Atlas::export('models', 'html');
file_put_contents('docs/models.html', $htmlOutput);

// Generate with intelligent workflow for complex applications
$intelligentHtml = Atlas::export('all', 'html');
file_put_contents('public/architecture.html', $intelligentHtml);

echo "HTML documentation generated\n";
```

## 🧠 Intelligent HTML Workflow

### 1. Advanced HTML Generation with Intelligent Processing

```php
<?php

use LaravelAtlas\AtlasManager;
use LaravelAtlas\Facades\Atlas;

// Use the intelligent HTML workflow for complex applications
$manager = app(AtlasManager::class);

// Scan multiple component types
$architectureData = [
    'models' => Atlas::scan('models'),
    'routes' => Atlas::scan('routes'),
    'services' => Atlas::scan('services'),
    'controllers' => Atlas::scan('controllers'),
    'events' => Atlas::scan('events'),
    'listeners' => Atlas::scan('listeners'),
];

// Generate intelligent HTML with advanced processing
$intelligentHtml = $manager->exportIntelligentHtml($architectureData, [
    'theme' => 'default',
    'include_search' => true,
    'include_navigation' => true,
    'interactive_flows' => true,
    'use_intelligent_workflow' => true,
]);

file_put_contents('public/intelligent-architecture.html', $intelligentHtml);

echo "Intelligent HTML documentation generated\n";
```

### 2. Custom Configuration for HTML Export

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate HTML with custom theming and features
$customHtml = Atlas::export('all', 'html', [
    'theme' => 'dark',
    'include_search' => true,
    'include_navigation' => true,
    'expand_sections' => false,
    'interactive_flows' => true,
    'show_component_stats' => true,
    'include_export_options' => true,
]);

file_put_contents('public/custom-architecture.html', $customHtml);

// Generate minimal HTML for embedding
$minimalHtml = Atlas::export('models', 'html', [
    'theme' => 'minimal',
    'include_navigation' => false,
    'embed_mode' => true,
    'show_header' => false,
]);

file_put_contents('docs/embed-models.html', $minimalHtml);
```

## 🎨 HTML Customization Examples

### 1. Theme-Based HTML Generation

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Dark theme for presentations
$darkHtml = Atlas::export('services', 'html', [
    'theme' => 'dark',
    'background_color' => '#1e293b',
    'text_color' => '#f1f5f9',
    'accent_color' => '#3b82f6',
    'include_search' => true,
]);

file_put_contents('presentation/services-dark.html', $darkHtml);

// Light theme for documentation
$lightHtml = Atlas::export('models', 'html', [
    'theme' => 'light',
    'background_color' => '#ffffff',
    'text_color' => '#1f2937',
    'accent_color' => '#059669',
    'include_navigation' => true,
]);

file_put_contents('docs/models-light.html', $lightHtml);

// Minimal theme for clean presentation
$minimalHtml = Atlas::export('routes', 'html', [
    'theme' => 'minimal',
    'show_borders' => false,
    'compact_layout' => true,
    'font_size' => '14px',
]);

file_put_contents('docs/routes-minimal.html', $minimalHtml);
```

### 2. Interactive Features Configuration

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Full-featured interactive documentation
$interactiveHtml = Atlas::export('all', 'html', [
    'include_search' => true,
    'include_navigation' => true,
    'include_filters' => true,
    'interactive_flows' => true,
    'expandable_sections' => true,
    'include_breadcrumbs' => true,
    'show_component_metrics' => true,
    'enable_export' => true,
]);

file_put_contents('public/interactive-docs.html', $interactiveHtml);

// Read-only documentation
$readOnlyHtml = Atlas::export('models', 'html', [
    'read_only' => true,
    'disable_interactions' => false,
    'include_print_styles' => true,
    'show_generation_info' => true,
]);

file_put_contents('docs/models-readonly.html', $readOnlyHtml);
```

## 📊 Advanced HTML Features

### 1. Multi-Component Architecture Documentation

```php
<?php

use LaravelAtlas\AtlasManager;
use LaravelAtlas\Facades\Atlas;

// Generate comprehensive multi-component documentation
$manager = app(AtlasManager::class);

// Prepare component data
$allComponents = [
    'models' => Atlas::scan('models', ['include_relationships' => true]),
    'controllers' => Atlas::scan('controllers', ['include_methods' => true]),
    'routes' => Atlas::scan('routes', ['include_middleware' => true]),
    'services' => Atlas::scan('services', ['include_dependencies' => true]),
    'events' => Atlas::scan('events'),
    'listeners' => Atlas::scan('listeners', ['include_queued_listeners' => true]),
    'jobs' => Atlas::scan('jobs'),
    'commands' => Atlas::scan('commands'),
];

// Generate with intelligent workflow and flow connections
$comprehensiveHtml = $manager->exportIntelligentHtml($allComponents, [
    'title' => 'Application Architecture Documentation',
    'description' => 'Complete architectural overview of the application',
    'include_toc' => true,
    'show_component_connections' => true,
    'interactive_dependency_graph' => true,
    'include_statistics' => true,
    'group_by_namespace' => true,
]);

file_put_contents('public/comprehensive-architecture.html', $comprehensiveHtml);

echo "Comprehensive architecture documentation generated\n";
```

### 2. Component Flow Visualization

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate HTML with component flow visualization
$flowHtml = Atlas::export('all', 'html', [
    'show_flows' => true,
    'flow_direction' => 'top-down',
    'include_flow_legend' => true,
    'highlight_critical_paths' => true,
    'show_dependency_strength' => true,
    'interactive_flow_explorer' => true,
]);

file_put_contents('public/architecture-flows.html', $flowHtml);

// Generate specific flow documentation
$requestFlowHtml = Atlas::export(['routes', 'controllers', 'services', 'models'], 'html', [
    'flow_type' => 'request_response',
    'start_component' => 'routes',
    'end_component' => 'models',
    'show_middleware_chain' => true,
]);

file_put_contents('docs/request-flow.html', $requestFlowHtml);
```

## 🔧 HTML Template Customization

### 1. Custom HTML Templates

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate HTML with custom template options
$customTemplateHtml = Atlas::export('models', 'html', [
    'template' => 'custom',
    'custom_css' => '
        .component-card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .relationship-line {
            stroke: #3b82f6;
            stroke-width: 2px;
        }
    ',
    'custom_js' => '
        document.addEventListener("DOMContentLoaded", function() {
            // Custom JavaScript for enhanced interactions
            console.log("Custom architecture documentation loaded");
        });
    ',
]);

file_put_contents('public/custom-template.html', $customTemplateHtml);

// Generate with embedded resources
$embeddedHtml = Atlas::export('services', 'html', [
    'embed_resources' => true,
    'minify_output' => true,
    'include_fallbacks' => true,
    'offline_ready' => true,
]);

file_put_contents('docs/services-embedded.html', $embeddedHtml);
```

### 2. Progressive Web App Features

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate HTML with PWA features
$pwaHtml = Atlas::export('all', 'html', [
    'pwa_enabled' => true,
    'service_worker' => true,
    'offline_cache' => true,
    'app_manifest' => [
        'name' => 'Architecture Documentation',
        'short_name' => 'ArchDocs',
        'description' => 'Application architecture documentation',
        'theme_color' => '#3b82f6',
    ],
    'install_prompt' => true,
]);

file_put_contents('public/pwa-architecture.html', $pwaHtml);
```

## 📈 HTML Analytics and Monitoring

### 1. Documentation Usage Analytics

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate HTML with analytics tracking
$analyticsHtml = Atlas::export('all', 'html', [
    'include_analytics' => true,
    'analytics_config' => [
        'track_page_views' => true,
        'track_component_clicks' => true,
        'track_search_queries' => true,
        'track_export_usage' => true,
    ],
    'privacy_compliant' => true,
]);

file_put_contents('public/analytics-architecture.html', $analyticsHtml);

// Generate with performance monitoring
$performanceHtml = Atlas::export('models', 'html', [
    'performance_monitoring' => true,
    'lazy_loading' => true,
    'progressive_enhancement' => true,
    'accessibility_features' => true,
]);

file_put_contents('docs/performance-models.html', $performanceHtml);
```

### 2. A/B Testing for Documentation

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate multiple variants for A/B testing
$variants = [
    'variant_a' => [
        'theme' => 'light',
        'layout' => 'sidebar',
        'component_cards' => true,
    ],
    'variant_b' => [
        'theme' => 'dark', 
        'layout' => 'tabs',
        'component_list' => true,
    ],
];

foreach ($variants as $name => $config) {
    $variantHtml = Atlas::export('all', 'html', array_merge($config, [
        'variant_name' => $name,
        'ab_testing' => true,
    ]));
    
    file_put_contents("public/architecture-{$name}.html", $variantHtml);
}
```

## 🔄 Automated HTML Generation

### 1. Scheduled Documentation Updates

```bash
#!/bin/bash
# update-html-docs.sh

echo "Updating HTML documentation..."

# Create documentation directory
mkdir -p public/docs
mkdir -p public/docs/components

# Generate main architecture documentation
php artisan atlas:generate --type=all --format=html --output=public/docs/architecture.html

# Generate component-specific documentation
components=("models" "controllers" "routes" "services" "events" "listeners" "jobs" "commands")

for component in "${components[@]}"
do
    echo "Generating ${component} HTML documentation..."
    php artisan atlas:generate --type=${component} --format=html --output=public/docs/components/${component}.html
done

# Generate specialized documentation
echo "Generating API documentation..."
php artisan atlas:generate --type=routes --format=html --output=public/docs/api.html

echo "Generating model relationships..."
php artisan atlas:generate --type=models --format=html --output=public/docs/models-relationships.html

echo "HTML documentation update complete!"
```

### 2. CI/CD Integration

```yaml
# .github/workflows/docs.yml
name: Update HTML Documentation

on:
  push:
    branches: [ main ]
    paths: 
      - 'app/**'
      - 'routes/**'
      - 'config/**'

jobs:
  update-docs:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          
      - name: Install dependencies
        run: composer install --no-dev
        
      - name: Generate HTML documentation
        run: |
          php artisan atlas:generate --type=all --format=html --output=public/docs/architecture.html
          php artisan atlas:generate --type=models --format=html --output=public/docs/models.html
          php artisan atlas:generate --type=routes --format=html --output=public/docs/api.html
          
      - name: Deploy to GitHub Pages
        uses: peaceiris/actions-gh-pages@v3
        with:
          github_token: ${{ secrets.GITHUB_TOKEN }}
          publish_dir: ./public/docs
```

## 💡 HTML Best Practices

### 1. Performance Optimization

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate optimized HTML for performance
$optimizedHtml = Atlas::export('all', 'html', [
    'optimize_performance' => true,
    'lazy_load_components' => true,
    'compress_output' => true,
    'inline_critical_css' => true,
    'defer_non_critical_js' => true,
    'progressive_loading' => true,
    'cache_assets' => true,
]);

file_put_contents('public/optimized-architecture.html', $optimizedHtml);
```

### 2. Accessibility Features

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Generate accessible HTML documentation
$accessibleHtml = Atlas::export('models', 'html', [
    'accessibility_compliant' => true,
    'high_contrast_mode' => true,
    'keyboard_navigation' => true,
    'screen_reader_optimized' => true,
    'aria_labels' => true,
    'focus_indicators' => true,
]);

file_put_contents('docs/accessible-models.html', $accessibleHtml);
```

## 🔗 Integration Examples

### 1. Embedding in Existing Applications

```html
<!-- Embed architecture documentation in your admin panel -->
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Architecture</title>
    <style>
        .architecture-frame {
            width: 100%;
            height: 800px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Application Architecture</h1>
        <iframe src="/docs/architecture.html" class="architecture-frame"></iframe>
    </div>
</body>
</html>
```

### 2. API Integration

```javascript
// JavaScript to integrate with generated HTML documentation
class ArchitectureViewer {
    constructor(containerId, documentationUrl) {
        this.container = document.getElementById(containerId);
        this.url = documentationUrl;
        this.init();
    }
    
    async init() {
        try {
            const response = await fetch(this.url);
            const html = await response.text();
            this.container.innerHTML = html;
            this.setupInteractions();
        } catch (error) {
            console.error('Failed to load architecture documentation:', error);
        }
    }
    
    setupInteractions() {
        // Add custom interactions
        const components = this.container.querySelectorAll('.component');
        components.forEach(component => {
            component.addEventListener('click', this.handleComponentClick.bind(this));
        });
    }
    
    handleComponentClick(event) {
        const componentName = event.target.dataset.component;
        console.log('Component clicked:', componentName);
        // Add your custom logic here
    }
}

// Initialize the viewer
const viewer = new ArchitectureViewer('architecture-container', '/docs/architecture.html');
```

## 🔗 Related Examples

- [Image Diagrams](image.md) - Visual architecture diagrams
- [PHP Data](php.md) - Raw data for custom processing
- [PDF Reports](pdf.md) - Printable documentation

---

**Need help?** Check our [documentation](../../docs/) or open an issue on GitHub.