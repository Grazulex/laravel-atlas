# Listener Analysis Examples

These examples demonstrate how to use Laravel Atlas to analyze event listeners.

## 📋 Prerequisites

- Laravel Atlas installed: `composer require grazulex/laravel-atlas --dev`
- Laravel application with event listeners

## 🎧 Basic Listener Analysis

### 1. Scan All Listeners

```bash
# Generate basic listener analysis
php artisan atlas:generate --type=listeners

# Save to JSON file
php artisan atlas:generate --type=listeners --format=json --output=docs/listeners.json

# Generate detailed markdown documentation
php artisan atlas:generate --type=listeners --format=markdown --output=docs/listeners.md
```

### 2. Programmatic Listener Analysis

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Basic listener scanning
$listenerData = Atlas::scan('listeners');

echo "Found " . count($listenerData['data']) . " listeners\n";

// Detailed listener analysis with options
$detailedListeners = Atlas::scan('listeners', [
    'include_handled_events' => true,
    'include_queued_listeners' => true,
    'analyze_dependencies' => true,
]);

foreach ($detailedListeners['data'] as $listener) {
    echo "Listener: {$listener['name']}\n";
    echo "Path: {$listener['path']}\n";
    
    if (isset($listener['handled_events'])) {
        echo "Handles Events:\n";
        foreach ($listener['handled_events'] as $event) {
            echo "  - {$event}\n";
        }
    }
    
    if (isset($listener['is_queued']) && $listener['is_queued']) {
        echo "Queued: Yes\n";
        if (isset($listener['queue'])) {
            echo "Queue: {$listener['queue']}\n";
        }
    }
    
    echo "\n";
}
```

## 📊 Export Examples

### 1. Generate Listener Documentation

```bash
# Create comprehensive listener documentation
php artisan atlas:generate --type=listeners --format=markdown --output=docs/LISTENERS.md

# Generate visual listener diagram
php artisan atlas:generate --type=listeners --format=image --output=diagrams/listeners.png

# Create HTML report with intelligent workflow
php artisan atlas:generate --type=listeners --format=html --output=public/listeners.html

# Generate PDF report for architecture review
php artisan atlas:generate --type=listeners --format=pdf --output=reports/listeners.pdf
```

### 2. Listener Data Processing

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Export listener data for analysis
$listenerJson = Atlas::export('listeners', 'json');
file_put_contents('storage/listeners-data.json', $listenerJson);

// Generate PHP data for custom processing
$listenerPhp = Atlas::export('listeners', 'php');
file_put_contents('storage/listeners-data.php', $listenerPhp);

// Include and process the generated data
$listenerData = include 'storage/listeners-data.php';

// Analyze listener-event relationships
foreach ($listenerData['data']['listeners']['data'] as $listener) {
    if (isset($listener['handled_events'])) {
        echo "{$listener['name']} handles:\n";
        foreach ($listener['handled_events'] as $event) {
            echo "  - {$event}\n";
        }
    }
}
```

## 🎯 Event-Listener Architecture Analysis

### 1. Event-Listener Mapping

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze event-listener relationships
$listeners = Atlas::scan('listeners', [
    'include_handled_events' => true,
]);

$events = Atlas::scan('events');

// Build event-to-listener mapping
$eventListenerMap = [];
foreach ($listeners['data'] as $listener) {
    foreach ($listener['handled_events'] ?? [] as $event) {
        $eventListenerMap[$event][] = $listener['name'];
    }
}

echo "Event-Listener Mapping:\n";
foreach ($eventListenerMap as $event => $listenerList) {
    echo "{$event} handled by: " . implode(', ', $listenerList) . "\n";
}

// Find events with no listeners
foreach ($events['data'] as $event) {
    if (!isset($eventListenerMap[$event['name']])) {
        echo "Warning: {$event['name']} has no listeners\n";
    }
}
```

### 2. Queue Analysis

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze queued vs synchronous listeners
$listenerData = Atlas::scan('listeners', [
    'include_queued_listeners' => true,
]);

$queuedListeners = [];
$syncListeners = [];

foreach ($listenerData['data'] as $listener) {
    if (isset($listener['is_queued']) && $listener['is_queued']) {
        $queue = $listener['queue'] ?? 'default';
        $queuedListeners[$queue][] = $listener['name'];
    } else {
        $syncListeners[] = $listener['name'];
    }
}

echo "Queue Analysis:\n";
echo "Synchronous Listeners: " . count($syncListeners) . "\n";
echo "Queued Listeners: " . array_sum(array_map('count', $queuedListeners)) . "\n\n";

echo "Queued Listeners by Queue:\n";
foreach ($queuedListeners as $queue => $listeners) {
    echo "{$queue}: " . count($listeners) . " listeners\n";
    foreach (array_slice($listeners, 0, 3) as $listener) {
        echo "  - {$listener}\n";
    }
    if (count($listeners) > 3) {
        echo "  ... and " . (count($listeners) - 3) . " more\n";
    }
}
```

## 🔧 Advanced Listener Analysis

### 1. Listener Performance Impact Analysis

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Analyze listener performance characteristics
$listenerData = Atlas::scan('listeners', [
    'include_queued_listeners' => true,
    'analyze_dependencies' => true,
]);

$performanceReport = [];

foreach ($listenerData['data'] as $listener) {
    $score = 0;
    $issues = [];
    
    // Check if listener is queued (good for performance)
    if (isset($listener['is_queued']) && $listener['is_queued']) {
        $score += 10;
    } else {
        $issues[] = "Not queued - may block request";
    }
    
    // Check for heavy dependencies
    if (isset($listener['dependencies'])) {
        if (count($listener['dependencies']) > 5) {
            $issues[] = "Many dependencies (" . count($listener['dependencies']) . ")";
            $score -= 5;
        }
    }
    
    $performanceReport[$listener['name']] = [
        'score' => $score,
        'issues' => $issues,
    ];
}

// Sort by performance score
arsort($performanceReport);

echo "Listener Performance Analysis:\n";
foreach (array_slice($performanceReport, 0, 10, true) as $listener => $analysis) {
    echo "{$listener}: Score {$analysis['score']}\n";
    if (!empty($analysis['issues'])) {
        foreach ($analysis['issues'] as $issue) {
            echo "  ⚠ {$issue}\n";
        }
    }
    echo "\n";
}
```

### 2. Combined Analysis with Events

```bash
# Generate combined event and listener analysis
php artisan atlas:generate --type=events --format=json --output=/tmp/events.json
php artisan atlas:generate --type=listeners --format=json --output=/tmp/listeners.json
```

```php
<?php

// Load both datasets
$eventData = json_decode(file_get_contents('/tmp/events.json'), true);
$listenerData = json_decode(file_get_contents('/tmp/listeners.json'), true);

// Generate comprehensive event system report
$report = "# Event System Architecture Report\n\n";

// Build complete event-listener mapping
$eventMap = [];
foreach ($listenerData['data']['listeners']['data'] as $listener) {
    foreach ($listener['handled_events'] ?? [] as $event) {
        $eventMap[$event][] = [
            'name' => $listener['name'],
            'queued' => $listener['is_queued'] ?? false,
            'queue' => $listener['queue'] ?? 'default',
        ];
    }
}

$report .= "## Event-Listener Overview\n\n";
$report .= "- Total Events: " . count($eventData['data']['events']['data']) . "\n";
$report .= "- Total Listeners: " . count($listenerData['data']['listeners']['data']) . "\n\n";

$report .= "## Event Coverage\n\n";
foreach ($eventData['data']['events']['data'] as $event) {
    $report .= "### {$event['name']}\n";
    
    if (isset($eventMap[$event['name']])) {
        foreach ($eventMap[$event['name']] as $listener) {
            $queueInfo = $listener['queued'] ? " (queued: {$listener['queue']})" : " (sync)";
            $report .= "- {$listener['name']}{$queueInfo}\n";
        }
    } else {
        $report .= "- **No listeners found**\n";
    }
    $report .= "\n";
}

file_put_contents('docs/EVENT-SYSTEM-ANALYSIS.md', $report);
echo "Event system analysis saved to docs/EVENT-SYSTEM-ANALYSIS.md\n";
```

## 📈 Listener Monitoring and Reporting

### 1. Regular Listener Health Checks

```bash
#!/bin/bash
# listener-health-check.sh

echo "Generating listener health report..."

# Generate current listener state
php artisan atlas:generate --type=listeners --format=json --output=reports/current-listeners.json

# Generate markdown report
php artisan atlas:generate --type=listeners --format=markdown --output=reports/listeners-$(date +%Y%m%d).md

echo "Listener health check complete!"
```

### 2. Listener Dependency Tracking

```php
<?php

use LaravelAtlas\Facades\Atlas;

// Track listener dependencies over time
$listenerData = Atlas::scan('listeners', [
    'analyze_dependencies' => true,
]);

$dependencyReport = [];
foreach ($listenerData['data'] as $listener) {
    if (isset($listener['dependencies'])) {
        $dependencyReport[$listener['name']] = [
            'count' => count($listener['dependencies']),
            'dependencies' => $listener['dependencies'],
        ];
    }
}

// Sort by dependency count
uasort($dependencyReport, function($a, $b) {
    return $b['count'] - $a['count'];
});

echo "Listeners with Most Dependencies:\n";
foreach (array_slice($dependencyReport, 0, 5, true) as $listener => $info) {
    echo "{$listener}: {$info['count']} dependencies\n";
    foreach (array_slice($info['dependencies'], 0, 3) as $dep) {
        echo "  - {$dep}\n";
    }
    if ($info['count'] > 3) {
        echo "  ... and " . ($info['count'] - 3) . " more\n";
    }
    echo "\n";
}
```

## 💡 Best Practices

### 1. Listener Architecture Validation

```php
<?php

// Validate listener architecture patterns
$listeners = Atlas::scan('listeners', [
    'include_queued_listeners' => true,
    'include_handled_events' => true,
]);

$recommendations = [];

foreach ($listeners['data'] as $listener) {
    $listenerName = $listener['name'];
    
    // Check if heavy operations should be queued
    if (!($listener['is_queued'] ?? false)) {
        if (isset($listener['dependencies']) && count($listener['dependencies']) > 3) {
            $recommendations[] = "{$listenerName}: Consider queuing this listener (has " . count($listener['dependencies']) . " dependencies)";
        }
    }
    
    // Check listener naming conventions
    if (!str_ends_with($listenerName, 'Listener')) {
        $recommendations[] = "{$listenerName}: Consider adding 'Listener' suffix for clarity";
    }
}

if (!empty($recommendations)) {
    echo "Architecture Recommendations:\n";
    foreach ($recommendations as $recommendation) {
        echo "• {$recommendation}\n";
    }
}
```

### 2. Event System Documentation

```bash
# Generate comprehensive event system documentation
php artisan atlas:generate --type=events --format=markdown --output=docs/events.md
php artisan atlas:generate --type=listeners --format=markdown --output=docs/listeners.md

# Create visual representation
php artisan atlas:generate --type=listeners --format=image --output=public/diagrams/event-system.png
```

## 🔗 Related Examples

- [Event Analysis](events.md) - Analyzing events that listeners handle
- [Observer Analysis](observers.md) - Model observers vs event listeners
- [Queue Analysis](../advanced/queue-analysis.md) - Analyzing queued listeners

---

**Need help?** Check our [documentation](../docs/) or open an issue on GitHub.