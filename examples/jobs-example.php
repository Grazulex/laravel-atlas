<?php

/**
 * Laravel Atlas - Jobs Analysis Example
 *
 * This example demonstrates how to analyze Laravel job classes
 * using Laravel Atlas.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use LaravelAtlas\Facades\Atlas;

echo "🔍 Laravel Atlas - Jobs Analysis Example\n";
echo "========================================\n\n";

// Example 1: Basic jobs scanning
echo "1. Scanning jobs in the application...\n";
try {
    $jobsData = Atlas::scan('jobs');

    echo '   ✅ Found ' . count($jobsData['data']) . " job classes\n";
    echo "   📊 Job analysis completed\n\n";

    // Display some job information
    if (! empty($jobsData['data'])) {
        echo "   📋 Job Classes Found:\n";
        foreach (array_slice($jobsData['data'], 0, 3) as $job) {
            echo '      • ' . ($job['name'] ?? 'Unknown') . "\n";
            if (isset($job['queue'])) {
                echo '        Queue: ' . $job['queue'] . "\n";
            }
            if (isset($job['connection'])) {
                echo '        Connection: ' . $job['connection'] . "\n";
            }
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo '   ❌ Error: ' . $e->getMessage() . "\n\n";
}

// Example 2: Advanced jobs scanning with options
echo "2. Advanced jobs scanning with detailed options...\n";
try {
    $detailedJobsData = Atlas::scan('jobs', [
        'include_dependencies' => true,
        'include_queue_config' => true,
        'include_methods' => true,
    ]);

    echo "   ✅ Detailed jobs analysis completed\n";
    echo '   📊 Total jobs with detailed info: ' . count($detailedJobsData['data']) . "\n\n";
} catch (Exception $e) {
    echo '   ❌ Error: ' . $e->getMessage() . "\n\n";
}

// Example 3: Export jobs to different formats
echo "3. Exporting jobs to different formats...\n";

// JSON Export
try {
    $jsonOutput = Atlas::export('jobs', 'json');
    $jsonFile = __DIR__ . '/../storage/atlas/jobs-analysis.json';

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
    $htmlOutput = Atlas::export('jobs', 'html');
    $htmlFile = __DIR__ . '/../storage/atlas/jobs-analysis.html';
    file_put_contents($htmlFile, $htmlOutput);

    echo "   ✅ HTML export saved to: {$htmlFile}\n";
} catch (Exception $e) {
    echo '   ❌ HTML export error: ' . $e->getMessage() . "\n";
}

// PDF Export
try {
    $pdfOutput = Atlas::export('jobs', 'pdf');
    $pdfFile = __DIR__ . '/../storage/atlas/jobs-analysis.pdf';
    file_put_contents($pdfFile, $pdfOutput);

    echo "   ✅ PDF export saved to: {$pdfFile}\n";
} catch (Exception $e) {
    echo '   ❌ PDF export error: ' . $e->getMessage() . "\n";
}

echo "\n4. Jobs Analysis Information:\n";
echo "   ⚙️ Job handlers and processing logic\n";
echo "   🔄 Queue configuration and connections\n";
echo "   💉 Dependencies and service injection\n";
echo "   📊 Retry policies and failure handling\n\n";

echo "5. Command Line Usage:\n";
echo "   # Export jobs as interactive HTML\n";
echo "   php artisan atlas:export --type=jobs --format=html --output=docs/jobs.html\n\n";
echo "   # Export jobs as JSON for API consumption\n";
echo "   php artisan atlas:export --type=jobs --format=json --output=api/jobs.json\n\n";
echo "   # Export jobs as PDF for documentation\n";
echo "   php artisan atlas:export --type=jobs --format=pdf --output=docs/jobs.pdf\n\n";

echo "✅ Jobs analysis example completed!\n";
