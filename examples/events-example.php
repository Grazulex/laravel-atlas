<?php

/**
 * Laravel Atlas - Events Analysis Example
 *
 * This example demonstrates how to analyze Laravel event classes
 * using Laravel Atlas.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LaravelAtlas\Facades\Atlas;

echo "🔍 Laravel Atlas - Events Analysis Example\n";
echo "==========================================\n\n";

// Example 1: Basic events scanning
echo "1. Scanning events in the application...\n";
try {
    $eventsData = Atlas::scan('events');

    echo '   ✅ Found ' . count($eventsData['data']) . " event classes\n";
    echo "   📊 Event analysis completed\n\n";

    // Display some event information
    if (! empty($eventsData['data'])) {
        echo "   📋 Event Classes Found:\n";
        foreach (array_slice($eventsData['data'], 0, 3) as $event) {
            echo '      • ' . ($event['name'] ?? 'Unknown') . "\n";
            if (isset($event['properties'])) {
                echo '        Properties: ' . count($event['properties']) . "\n";
            }
            if (isset($event['listeners'])) {
                echo '        Listeners: ' . count($event['listeners']) . "\n";
            }
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo '   ❌ Error: ' . $e->getMessage() . "\n\n";
}

// Example 2: Advanced events scanning with options
echo "2. Advanced events scanning with detailed options...\n";
try {
    $detailedEventsData = Atlas::scan('events', [
        'include_listeners' => true,
        'include_properties' => true,
        'include_methods' => true,
    ]);

    echo "   ✅ Detailed events analysis completed\n";
    echo '   📊 Total events with detailed info: ' . count($detailedEventsData['data']) . "\n\n";
} catch (Exception $e) {
    echo '   ❌ Error: ' . $e->getMessage() . "\n\n";
}

// Example 3: Export events to different formats
echo "3. Exporting events to different formats...\n";

// JSON Export
try {
    $jsonOutput = Atlas::export('events', 'json');
    $jsonFile = __DIR__ . '/../storage/atlas/events-analysis.json';

    if (! is_dir(dirname($jsonFile))) {
        mkdir(dirname($jsonFile), 0755, true);
    }
    file_put_contents($jsonFile, $jsonOutput);

    echo "   ✅ JSON export saved to: {$jsonFile}\n";
} catch (Exception $e) {
    echo '   ❌ JSON export error: ' . $e->getMessage() . "\n";
}

// HTML Export
try {
    $htmlOutput = Atlas::export('events', 'html');
    $htmlFile = __DIR__ . '/../storage/atlas/events-analysis.html';
    file_put_contents($htmlFile, $htmlOutput);

    echo "   ✅ HTML export saved to: {$htmlFile}\n";
} catch (Exception $e) {
    echo '   ❌ HTML export error: ' . $e->getMessage() . "\n";
}

// PDF Export
try {
    $pdfOutput = Atlas::export('events', 'pdf');
    $pdfFile = __DIR__ . '/../storage/atlas/events-analysis.pdf';
    file_put_contents($pdfFile, $pdfOutput);

    echo "   ✅ PDF export saved to: {$pdfFile}\n";
} catch (Exception $e) {
    echo '   ❌ PDF export error: ' . $e->getMessage() . "\n";
}

echo "\n4. Events Analysis Information:\n";
echo "   📋 Event properties and public variables\n";
echo "   👂 Associated listeners and handlers\n";
echo "   🔄 Event flow and broadcasting configuration\n";
echo "   📊 Method signatures and dependencies\n\n";

echo "5. Command Line Usage:\n";
echo "   # Export events as interactive HTML\n";
echo "   php artisan atlas:export --type=events --format=html --output=docs/events.html\n\n";
echo "   # Export events as JSON for API consumption\n";
echo "   php artisan atlas:export --type=events --format=json --output=api/events.json\n\n";
echo "   # Export events as PDF for documentation\n";
echo "   php artisan atlas:export --type=events --format=pdf --output=docs/events.pdf\n\n";

echo "✅ Events analysis example completed!\n";
