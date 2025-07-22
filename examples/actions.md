# Action Analysis Examples

These examples demonstrate how to use Laravel Atlas to analyze action classes and single-purpose classes.

## 📋 Prerequisites

- Laravel Atlas installed: `composer require grazulex/laravel-atlas --dev`
- Laravel application with action classes

## ⚡ Basic Action Analysis

### 1. Scan All Actions

```bash
# Generate basic action analysis
php artisan atlas:generate --type=actions

# Save to JSON file
php artisan atlas:generate --type=actions --format=json --output=docs/actions.json

# Generate detailed markdown documentation
php artisan atlas:generate --type=actions --format=markdown --output=docs/actions.md
```

### 2. Programmatic Action Analysis

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Basic action scanning
$actionData = Atlas::scan('actions');

echo "Found " . count($actionData['data']) . " actions\n";

// Detailed action analysis with options
$detailedActions = Atlas::scan('actions', [
    'include_dependencies' => true,
    'include_invokable' => true,
    'detect_patterns' => true,
]);

foreach ($detailedActions['data'] as $action) {
    echo "Action: {$action['name']}\n";
    echo "Path: {$action['path']}\n";
    
    if (isset($action['is_invokable']) && $action['is_invokable']) {
        echo "Type: Invokable Action\n";
    }
    
    if (isset($action['dependencies'])) {
        echo "Dependencies:\n";
        foreach ($action['dependencies'] as $dependency) {
            echo "  - {$dependency}\n";
        }
    }
    
    if (isset($action['pattern'])) {
        echo "Pattern: {$action['pattern']}\n";
    }
    
    echo "\n";
}
```

## 📊 Export Examples

### 1. Generate Action Documentation

```bash
# Create comprehensive action documentation
php artisan atlas:generate --type=actions --format=markdown --output=docs/ACTIONS.md

# Generate visual action diagram
php artisan atlas:generate --type=actions --format=image --output=diagrams/actions.png

# Create HTML report with intelligent workflow
php artisan atlas:generate --type=actions --format=html --output=public/actions.html

# Generate PDF report for architecture review
php artisan atlas:generate --type=actions --format=pdf --output=reports/actions.pdf
```

### 2. Action Data Processing

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Export action data for analysis
$actionJson = Atlas::export('actions', 'json');
file_put_contents('storage/actions-data.json', $actionJson);

// Generate PHP data for custom processing
$actionPhp = Atlas::export('actions', 'php');
file_put_contents('storage/actions-data.php', $actionPhp);

// Include and process the generated data
$actionData = include 'storage/actions-data.php';

// Analyze action patterns
$patterns = [];
foreach ($actionData['data']['actions']['data'] as $action) {
    if (isset($action['pattern'])) {
        $patterns[$action['pattern']][] = $action['name'];
    }
}

echo "Action Patterns:\n";
foreach ($patterns as $pattern => $actionList) {
    echo "{$pattern}: " . count($actionList) . " actions\n";
}
```

## 🎯 Action Architecture Analysis

### 1. Action Pattern Detection

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze action patterns and naming conventions
$actionData = Atlas::scan('actions', [
    'detect_patterns' => true,
    'include_invokable' => true,
]);

$patterns = [
    'Command' => [],
    'Query' => [],
    'Handler' => [],
    'Service' => [],
    'Processor' => [],
    'Builder' => [],
    'Other' => [],
];

foreach ($actionData['data'] as $action) {
    $name = $action['name'];
    $classified = false;
    
    foreach (['Command', 'Query', 'Handler', 'Service', 'Processor', 'Builder'] as $pattern) {
        if (str_contains($name, $pattern)) {
            $patterns[$pattern][] = $name;
            $classified = true;
            break;
        }
    }
    
    if (!$classified) {
        $patterns['Other'][] = $name;
    }
}

echo "Action Pattern Analysis:\n";
foreach ($patterns as $pattern => $actions) {
    if (!empty($actions)) {
        echo "{$pattern}: " . count($actions) . " actions\n";
        foreach (array_slice($actions, 0, 3) as $action) {
            echo "  - {$action}\n";
        }
        if (count($actions) > 3) {
            echo "  ... and " . (count($actions) - 3) . " more\n";
        }
        echo "\n";
    }
}
```

### 2. Action Dependency Analysis

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze action dependencies and complexity
$actionData = Atlas::scan('actions', [
    'include_dependencies' => true,
    'analyze_dependencies' => true,
]);

$complexityReport = [];

foreach ($actionData['data'] as $action) {
    $complexity = 0;
    $factors = [];
    
    // Dependency count factor
    if (isset($action['dependencies'])) {
        $depCount = count($action['dependencies']);
        $complexity += $depCount * 2;
        if ($depCount > 5) {
            $factors[] = "High dependencies ({$depCount})";
        }
    }
    
    // Size factor (if available)
    if (isset($action['lines_of_code'])) {
        if ($action['lines_of_code'] > 100) {
            $complexity += 10;
            $factors[] = "Large size ({$action['lines_of_code']} lines)";
        }
    }
    
    $complexityReport[$action['name']] = [
        'score' => $complexity,
        'factors' => $factors,
        'dependencies' => $action['dependencies'] ?? [],
    ];
}

// Sort by complexity
uasort($complexityReport, function($a, $b) {
    return $b['score'] - $a['score'];
});

echo "Action Complexity Analysis:\n";
foreach (array_slice($complexityReport, 0, 10, true) as $action => $analysis) {
    echo "{$action}: Complexity {$analysis['score']}\n";
    if (!empty($analysis['factors'])) {
        foreach ($analysis['factors'] as $factor) {
            echo "  ⚠ {$factor}\n";
        }
    }
    if (count($analysis['dependencies']) > 0) {
        echo "  Dependencies: " . implode(', ', array_slice($analysis['dependencies'], 0, 3));
        if (count($analysis['dependencies']) > 3) {
            echo " (+" . (count($analysis['dependencies']) - 3) . " more)";
        }
        echo "\n";
    }
    echo "\n";
}
```

## 🔧 Advanced Action Analysis

### 1. Action Usage Patterns

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze how actions are used throughout the application
$actions = Atlas::scan('actions', ['include_dependencies' => true]);
$controllers = Atlas::scan('controllers', ['include_methods' => true]);
$routes = Atlas::scan('routes', ['include_controllers' => true]);

// Build usage map
$actionUsage = [];
foreach ($actions['data'] as $action) {
    $actionUsage[$action['name']] = [
        'controllers' => [],
        'routes' => [],
        'other_actions' => [],
    ];
}

// Check controller usage (simplified example)
foreach ($controllers['data'] as $controller) {
    foreach ($controller['methods'] ?? [] as $method) {
        foreach ($actions['data'] as $action) {
            // This would require more sophisticated AST analysis in real implementation
            if (str_contains($method['body'] ?? '', $action['name'])) {
                $actionUsage[$action['name']]['controllers'][] = $controller['name'] . '::' . $method['name'];
            }
        }
    }
}

echo "Action Usage Analysis:\n";
foreach ($actionUsage as $action => $usage) {
    $totalUsage = count($usage['controllers']) + count($usage['routes']) + count($usage['other_actions']);
    
    if ($totalUsage === 0) {
        echo "⚠ {$action}: Not used (potential dead code)\n";
    } else {
        echo "✓ {$action}: Used in {$totalUsage} places\n";
        if (!empty($usage['controllers'])) {
            echo "  Controllers: " . implode(', ', array_slice($usage['controllers'], 0, 2)) . "\n";
        }
    }
}
```

### 2. Action Testing Coverage

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze action testing coverage (conceptual example)
$actionData = Atlas::scan('actions');

$testCoverage = [];

foreach ($actionData['data'] as $action) {
    $actionName = $action['name'];
    $testPath = base_path("tests/Unit/Actions/{$actionName}Test.php");
    $featureTestPath = base_path("tests/Feature/Actions/{$actionName}Test.php");
    
    $coverage = [
        'has_unit_test' => file_exists($testPath),
        'has_feature_test' => file_exists($featureTestPath),
    ];
    
    $testCoverage[$actionName] = $coverage;
}

echo "Action Test Coverage:\n";
$covered = 0;
$total = count($testCoverage);

foreach ($testCoverage as $action => $coverage) {
    $hasCoverage = $coverage['has_unit_test'] || $coverage['has_feature_test'];
    
    if ($hasCoverage) {
        $covered++;
        $testTypes = [];
        if ($coverage['has_unit_test']) $testTypes[] = 'Unit';
        if ($coverage['has_feature_test']) $testTypes[] = 'Feature';
        echo "✓ {$action}: " . implode(', ', $testTypes) . " tests\n";
    } else {
        echo "⚠ {$action}: No tests found\n";
    }
}

$percentage = round(($covered / $total) * 100, 1);
echo "\nOverall Coverage: {$covered}/{$total} ({$percentage}%)\n";
```

## 📈 Action Monitoring and Quality

### 1. Action Quality Score

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Calculate quality scores for actions
$actionData = Atlas::scan('actions', [
    'include_dependencies' => true,
    'include_invokable' => true,
    'detect_patterns' => true,
]);

$qualityScores = [];

foreach ($actionData['data'] as $action) {
    $score = 100; // Start with perfect score
    $issues = [];
    
    // Naming convention
    if (!preg_match('/^[A-Z][a-zA-Z]+(Action|Command|Query|Handler)$/', $action['name'])) {
        $score -= 10;
        $issues[] = "Poor naming convention";
    }
    
    // Single responsibility (low dependency count)
    $depCount = count($action['dependencies'] ?? []);
    if ($depCount > 7) {
        $score -= 20;
        $issues[] = "Too many dependencies ({$depCount})";
    } elseif ($depCount > 4) {
        $score -= 10;
        $issues[] = "High dependencies ({$depCount})";
    }
    
    // Proper action pattern
    if (isset($action['is_invokable']) && $action['is_invokable']) {
        $score += 5; // Bonus for invokable
    }
    
    // Size (if available)
    if (isset($action['lines_of_code']) && $action['lines_of_code'] > 150) {
        $score -= 15;
        $issues[] = "Large class ({$action['lines_of_code']} lines)";
    }
    
    $qualityScores[$action['name']] = [
        'score' => max(0, $score),
        'issues' => $issues,
    ];
}

// Sort by quality score
uasort($qualityScores, function($a, $b) {
    return $b['score'] - $a['score'];
});

echo "Action Quality Report:\n";
foreach (array_slice($qualityScores, 0, 10, true) as $action => $quality) {
    $status = $quality['score'] >= 80 ? '✓' : ($quality['score'] >= 60 ? '⚠' : '❌');
    echo "{$status} {$action}: {$quality['score']}/100\n";
    
    if (!empty($quality['issues'])) {
        foreach ($quality['issues'] as $issue) {
            echo "    • {$issue}\n";
        }
    }
}
```

### 2. Action Architecture Recommendations

```bash
#!/bin/bash
# action-architecture-audit.sh

echo "Running action architecture audit..."

# Generate action analysis
php artisan atlas:generate --type=actions --format=json --output=/tmp/actions-audit.json

# Generate recommendations report
php -r "
\$data = json_decode(file_get_contents('/tmp/actions-audit.json'), true);
\$recommendations = [];

foreach (\$data['data']['actions']['data'] as \$action) {
    \$name = \$action['name'];
    
    // Check for action suffix
    if (!str_ends_with(\$name, 'Action') && !str_ends_with(\$name, 'Command') && !str_ends_with(\$name, 'Query')) {
        \$recommendations[] = \"Consider renaming '\$name' to include Action/Command/Query suffix\";
    }
    
    // Check dependency count
    \$deps = count(\$action['dependencies'] ?? []);
    if (\$deps > 5) {
        \$recommendations[] = \"'\$name' has \$deps dependencies - consider splitting responsibilities\";
    }
}

if (!empty(\$recommendations)) {
    echo \"\\nArchitecture Recommendations:\\n\";
    foreach (\$recommendations as \$rec) {
        echo \"• \$rec\\n\";
    }
} else {
    echo \"\\n✓ No architectural issues found!\\n\";
}
"

echo "Action architecture audit complete!"
```

## 💡 Best Practices

### 1. Action Design Patterns

```php
<?php

// Example of analyzing action design patterns
use LaravelAtlas\Facades\Atlas;

$actionData = Atlas::scan('actions', [
    'detect_patterns' => true,
    'include_invokable' => true,
]);

$designPatterns = [
    'Command Pattern' => 0,
    'Query Pattern' => 0,
    'Single Responsibility' => 0,
    'Dependency Injection' => 0,
];

foreach ($actionData['data'] as $action) {
    // Command pattern detection
    if (str_contains($action['name'], 'Command') || 
        (isset($action['is_invokable']) && $action['is_invokable'])) {
        $designPatterns['Command Pattern']++;
    }
    
    // Query pattern detection  
    if (str_contains($action['name'], 'Query') || str_contains($action['name'], 'Get')) {
        $designPatterns['Query Pattern']++;
    }
    
    // Single responsibility (few dependencies)
    if (count($action['dependencies'] ?? []) <= 3) {
        $designPatterns['Single Responsibility']++;
    }
    
    // Dependency injection usage
    if (!empty($action['dependencies'])) {
        $designPatterns['Dependency Injection']++;
    }
}

echo "Design Pattern Usage:\n";
foreach ($designPatterns as $pattern => $count) {
    echo "{$pattern}: {$count} actions\n";
}
```

### 2. Action Refactoring Opportunities

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Identify refactoring opportunities
$controllers = Atlas::scan('controllers');
$actions = Atlas::scan('actions');

// Look for complex controller methods that could be extracted to actions
foreach ($controllers['data'] as $controller) {
    foreach ($controller['methods'] ?? [] as $method) {
        if (isset($method['complexity']) && $method['complexity'] > 10) {
            echo "Refactoring opportunity: {$controller['name']}::{$method['name']}\n";
            echo "  Current complexity: {$method['complexity']}\n";
            echo "  Consider extracting to Action class\n\n";
        }
    }
}
```

## 🔗 Related Examples

- [Service Analysis](services.md) - Comparing actions vs services
- [Controller Analysis](controllers.md) - Moving logic from controllers to actions
- [Command Analysis](commands.md) - Console commands vs action classes

---

**Need help?** Check our [documentation](../docs/) or open an issue on GitHub.