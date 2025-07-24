<?php

/**
 * Laravel Atlas - Commands Analysis Example
 *
 * This example demonstrates how to analyze Artisan commands:
 * - Command signatures and descriptions
 * - Arguments and options
 * - Command dependencies
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - Commands Analysis Example ===\n\n";

// 1. Basic command scanning
echo "1. Basic command scanning:\n";
$commands = Atlas::scan('commands');

echo 'Total commands found: ' . ($commands['count'] ?? 0) . "\n";
if (isset($commands['data']) && is_array($commands['data'])) {
    echo "Command classes:\n";
    foreach ($commands['data'] as $command) {
        if (isset($command['class'])) {
            echo '- ' . class_basename($command['class']) . " ({$command['class']})\n";
        }
    }
}
echo "\n";

// 2. Commands with signatures and descriptions
echo "2. Commands with detailed information:\n";
$commandsWithDetails = Atlas::scan('commands', [
    'include_signatures' => true,
    'include_descriptions' => true,
]);

if (isset($commandsWithDetails['data']) && is_array($commandsWithDetails['data'])) {
    foreach ($commandsWithDetails['data'] as $command) {
        if (isset($command['class'])) {
            $className = class_basename($command['class']);
            echo "Command: {$className}\n";

            // Show signature
            if (isset($command['signature'])) {
                echo "  Signature: {$command['signature']}\n";
            }

            // Show description
            if (isset($command['description'])) {
                echo "  Description: {$command['description']}\n";
            }

            // Show arguments
            if (isset($command['arguments']) && is_array($command['arguments'])) {
                echo '  Arguments: ' . count($command['arguments']) . "\n";
                foreach ($command['arguments'] as $arg => $details) {
                    $required = isset($details['required']) && $details['required'] ? ' (required)' : ' (optional)';
                    echo "    - {$arg}{$required}\n";
                }
            }

            // Show options
            if (isset($command['options']) && is_array($command['options'])) {
                echo '  Options: ' . count($command['options']) . "\n";
                foreach (array_slice($command['options'], 0, 3) as $option => $details) { // Show first 3
                    $shortcut = isset($details['shortcut']) ? " (-{$details['shortcut']})" : '';
                    echo "    - --{$option}{$shortcut}\n";
                }
                if (count($command['options']) > 3) {
                    echo '    ... and ' . (count($command['options']) - 3) . " more options\n";
                }
            }

            echo "\n";
        }
    }
}

// 3. Command categorization
echo "3. Command analysis by type:\n";
if (isset($commandsWithDetails['data']) && is_array($commandsWithDetails['data'])) {
    $makeCommands = 0;
    $migrateCommands = 0;
    $customCommands = 0;

    foreach ($commandsWithDetails['data'] as $command) {
        if (isset($command['signature'])) {
            $signature = $command['signature'];
            if (str_starts_with($signature, 'make:')) {
                $makeCommands++;
            } elseif (str_contains($signature, 'migrate')) {
                $migrateCommands++;
            } else {
                $customCommands++;
            }
        }
    }

    echo "- Make commands: {$makeCommands}\n";
    echo "- Migration commands: {$migrateCommands}\n";
    echo "- Custom commands: {$customCommands}\n";
}
echo "\n";

// 4. Export commands to different formats
echo "4. Exporting commands:\n";

// JSON export
$jsonExport = Atlas::export('commands', 'json');
echo '- JSON export ready (length: ' . strlen($jsonExport) . " characters)\n";

// Markdown export
$markdownExport = Atlas::export('commands', 'markdown');
echo '- Markdown export ready (length: ' . strlen($markdownExport) . " characters)\n";

// HTML export
$htmlExport = Atlas::export('commands', 'html');
echo '- HTML export ready (length: ' . strlen($htmlExport) . " characters)\n";

// 5. Custom command analysis
echo "\n5. Custom command analysis:\n";
$customCommands = Atlas::scan('commands', [
    'paths' => [app_path('Console/Commands')],
    'include_signatures' => true,
    'include_descriptions' => true,
]);

echo 'Custom commands found: ' . ($customCommands['count'] ?? 0) . "\n";

if (isset($customCommands['data']) && is_array($customCommands['data'])) {
    foreach ($customCommands['data'] as $command) {
        if (isset($command['class'], $command['signature'])) {
            echo "- {$command['signature']} (" . class_basename($command['class']) . ")\n";
        }
    }
}

echo "\nCommands analysis example completed successfully!\n";
