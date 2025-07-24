<?php

/**
 * Laravel Atlas - Notifications Analysis Example
 *
 * This example demonstrates how to analyze Laravel notification classes:
 * - Notification channels and methods
 * - Flow patterns and dependencies
 * - Channel-specific implementations
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - Notifications Analysis Example ===\n\n";

// 1. Basic notification scanning
echo "1. Basic notification scanning:\n";
$notifications = Atlas::scan('notifications');

echo 'Total notifications found: ' . ($notifications['count'] ?? 0) . "\n";
if (isset($notifications['data']) && is_array($notifications['data'])) {
    echo "Notification classes:\n";
    foreach ($notifications['data'] as $notification) {
        if (isset($notification['class'])) {
            echo '- ' . class_basename($notification['class']) . " ({$notification['class']})\n";
        }
    }
}
echo "\n";

// 2. Notifications with channels and methods
echo "2. Notifications with detailed information:\n";
$notificationsWithDetails = Atlas::scan('notifications', [
    'include_channels' => true,
    'include_flow' => true,
]);

if (isset($notificationsWithDetails['data']) && is_array($notificationsWithDetails['data'])) {
    foreach ($notificationsWithDetails['data'] as $notification) {
        if (isset($notification['class'])) {
            $className = class_basename($notification['class']);
            echo "Notification: {$className}\n";

            // Show channels
            if (isset($notification['channels']) && is_array($notification['channels'])) {
                echo '  Channels: ' . (empty($notification['channels']) ? 'none detected' : implode(', ', $notification['channels'])) . "\n";
            }

            // Show defined methods (toMail, toDatabase, etc.)
            if (isset($notification['methods']) && is_array($notification['methods'])) {
                echo '  Methods: ' . (empty($notification['methods']) ? 'none detected' : implode(', ', $notification['methods'])) . "\n";
            }

            // Show flow dependencies
            if (isset($notification['flow']) && is_array($notification['flow'])) {
                $dependencies = $notification['flow']['dependencies'] ?? [];
                if (! empty($dependencies)) {
                    echo "  Dependencies:\n";
                    foreach ($dependencies as $type => $deps) {
                        if (! empty($deps)) {
                            echo "    - {$type}: " . implode(', ', array_map('class_basename', $deps)) . "\n";
                        }
                    }
                }
            }

            echo "\n";
        }
    }
}

// 3. Notification channel analysis
echo "3. Notification channels analysis:\n";
if (isset($notificationsWithDetails['data']) && is_array($notificationsWithDetails['data'])) {
    $channelCounts = [];
    $methodCounts = [];

    foreach ($notificationsWithDetails['data'] as $notification) {
        // Count channels
        if (isset($notification['channels']) && is_array($notification['channels'])) {
            foreach ($notification['channels'] as $channel) {
                $channelCounts[$channel] = ($channelCounts[$channel] ?? 0) + 1;
            }
        }

        // Count methods
        if (isset($notification['methods']) && is_array($notification['methods'])) {
            foreach ($notification['methods'] as $method) {
                $methodCounts[$method] = ($methodCounts[$method] ?? 0) + 1;
            }
        }
    }

    if (! empty($channelCounts)) {
        echo "Channel usage:\n";
        foreach ($channelCounts as $channel => $count) {
            echo "- {$channel}: {$count} notifications\n";
        }
    } else {
        echo "No channels detected in notifications\n";
    }

    if (! empty($methodCounts)) {
        echo "Method implementations:\n";
        foreach ($methodCounts as $method => $count) {
            echo "- {$method}: {$count} notifications\n";
        }
    }
}
echo "\n";

// 4. Notification flow analysis
echo "4. Notification flow patterns:\n";
if (isset($notificationsWithDetails['data']) && is_array($notificationsWithDetails['data'])) {
    $notificationsWithModels = 0;
    $notificationsWithServices = 0;
    $notificationsWithFacades = 0;

    foreach ($notificationsWithDetails['data'] as $notification) {
        if (isset($notification['flow']['dependencies']) && is_array($notification['flow']['dependencies'])) {
            $deps = $notification['flow']['dependencies'];

            if (! empty($deps['models'])) {
                $notificationsWithModels++;
            }
            if (! empty($deps['services'])) {
                $notificationsWithServices++;
            }
            if (! empty($deps['facades'])) {
                $notificationsWithFacades++;
            }
        }
    }

    echo "- Notifications using models: {$notificationsWithModels}\n";
    echo "- Notifications using services: {$notificationsWithServices}\n";
    echo "- Notifications using facades: {$notificationsWithFacades}\n";
}
echo "\n";

// 5. Export notifications to different formats
echo "5. Exporting notifications:\n";

// JSON export
$jsonExport = Atlas::export('notifications', 'json');
echo '- JSON export ready (length: ' . strlen($jsonExport) . " characters)\n";

// Markdown export
$markdownExport = Atlas::export('notifications', 'markdown');
echo '- Markdown export ready (length: ' . strlen($markdownExport) . " characters)\n";

// HTML export
$htmlExport = Atlas::export('notifications', 'html');
echo '- HTML export ready (length: ' . strlen($htmlExport) . " characters)\n";

// 6. Custom notification analysis
echo "\n6. Custom notification analysis:\n";
$customNotifications = Atlas::scan('notifications', [
    'paths' => [app_path('Notifications')],
    'recursive' => true,
]);

echo 'Notifications found in custom path: ' . ($customNotifications['count'] ?? 0) . "\n";

if (isset($customNotifications['data']) && is_array($customNotifications['data'])) {
    foreach ($customNotifications['data'] as $notification) {
        if (isset($notification['class'])) {
            echo '- ' . class_basename($notification['class']) . "\n";
        }
    }
}

echo "\nNotifications analysis example completed successfully!\n";
