<?php

/**
 * Laravel Atlas - Services Analysis Example
 *
 * This example demonstrates how to analyze application service classes:
 * - Service methods and dependencies
 * - Constructor injection patterns
 * - Service flow analysis
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - Services Analysis Example ===\n\n";

// 1. Basic service scanning
echo "1. Basic service scanning:\n";
$services = Atlas::scan('services');

echo 'Total services found: ' . ($services['count'] ?? 0) . "\n";
if (isset($services['data']) && is_array($services['data'])) {
    echo "Service classes:\n";
    foreach ($services['data'] as $service) {
        if (isset($service['class'])) {
            echo '- ' . class_basename($service['class']) . " ({$service['class']})\n";
        }
    }
}
echo "\n";

// 2. Services with methods and dependencies
echo "2. Services with detailed information:\n";
$servicesWithDetails = Atlas::scan('services', [
    'include_dependencies' => true,
    'include_methods' => true,
]);

if (isset($servicesWithDetails['data']) && is_array($servicesWithDetails['data'])) {
    foreach ($servicesWithDetails['data'] as $service) {
        if (isset($service['class'])) {
            $className = class_basename($service['class']);
            echo "Service: {$className}\n";

            // Show public methods
            if (isset($service['methods']) && is_array($service['methods'])) {
                echo '  Public methods: ' . count($service['methods']) . "\n";
                foreach (array_slice($service['methods'], 0, 3) as $method) { // Show first 3
                    if (isset($method['name'])) {
                        $params = isset($method['parameters']) && is_array($method['parameters'])
                            ? '(' . implode(', ', $method['parameters']) . ')'
                            : '()';
                        echo "    - {$method['name']}{$params}\n";
                    }
                }
                if (count($service['methods']) > 3) {
                    echo '    ... and ' . (count($service['methods']) - 3) . " more methods\n";
                }
            }

            // Show constructor dependencies
            if (isset($service['dependencies']) && is_array($service['dependencies'])) {
                $filteredDeps = array_filter($service['dependencies']);
                if (! empty($filteredDeps)) {
                    echo '  Dependencies: ' . count($filteredDeps) . "\n";
                    foreach (array_slice($filteredDeps, 0, 3) as $dependency) { // Show first 3
                        echo '    - ' . class_basename($dependency) . "\n";
                    }
                    if (count($filteredDeps) > 3) {
                        echo '    ... and ' . (count($filteredDeps) - 3) . " more dependencies\n";
                    }
                }
            }

            // Show flow analysis
            if (isset($service['flow']) && is_array($service['flow'])) {
                $flowTypes = array_keys(array_filter($service['flow'], fn ($items) => ! empty($items)));
                if (! empty($flowTypes)) {
                    echo '  Flow patterns: ' . implode(', ', $flowTypes) . "\n";
                }
            }

            echo "\n";
        }
    }
}

// 3. Service pattern analysis
echo "3. Service patterns analysis:\n";
if (isset($servicesWithDetails['data']) && is_array($servicesWithDetails['data'])) {
    $servicesWithDependencies = 0;
    $servicesWithJobs = 0;
    $servicesWithEvents = 0;

    foreach ($servicesWithDetails['data'] as $service) {
        if (isset($service['dependencies']) && is_array($service['dependencies'])) {
            $filteredDeps = array_filter($service['dependencies']);
            if (! empty($filteredDeps)) {
                $servicesWithDependencies++;
            }
        }

        if (isset($service['flow']['jobs']) && ! empty($service['flow']['jobs'])) {
            $servicesWithJobs++;
        }

        if (isset($service['flow']['events']) && ! empty($service['flow']['events'])) {
            $servicesWithEvents++;
        }
    }

    echo "- Services with dependencies: {$servicesWithDependencies}\n";
    echo "- Services dispatching jobs: {$servicesWithJobs}\n";
    echo "- Services firing events: {$servicesWithEvents}\n";
}
echo "\n";

// 4. Export services to different formats
echo "4. Exporting services:\n";

// JSON export
$jsonExport = Atlas::export('services', 'json');
echo '- JSON export ready (length: ' . strlen($jsonExport) . " characters)\n";

// Markdown export
$markdownExport = Atlas::export('services', 'markdown');
echo '- Markdown export ready (length: ' . strlen($markdownExport) . " characters)\n";

// HTML export
$htmlExport = Atlas::export('services', 'html');
echo '- HTML export ready (length: ' . strlen($htmlExport) . " characters)\n";

// 5. Custom service analysis with specific paths
echo "\n5. Custom service analysis:\n";
$customServices = Atlas::scan('services', [
    'paths' => [app_path('Services'), app_path('Domain')],
    'recursive' => true,
]);

echo 'Services found in custom paths: ' . ($customServices['count'] ?? 0) . "\n";

if (isset($customServices['data']) && is_array($customServices['data'])) {
    foreach ($customServices['data'] as $service) {
        if (isset($service['class'])) {
            echo '- ' . class_basename($service['class']) . "\n";
        }
    }
}

echo "\nServices analysis example completed successfully!\n";
