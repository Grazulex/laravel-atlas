<?php

/**
 * Laravel Atlas - Form Requests Analysis Example
 *
 * This example demonstrates how to analyze Form Request validation classes:
 * - Validation rules and authorization
 * - Custom attributes and messages
 * - Dependencies and flow patterns
 */

use LaravelAtlas\Facades\Atlas;

echo "=== Laravel Atlas - Form Requests Analysis Example ===\n\n";

// 1. Basic form request scanning
echo "1. Basic form request scanning:\n";
$formRequests = Atlas::scan('form_requests');

echo 'Total form requests found: ' . ($formRequests['count'] ?? 0) . "\n";
if (isset($formRequests['data']) && is_array($formRequests['data'])) {
    echo "Form request classes:\n";
    foreach ($formRequests['data'] as $formRequest) {
        if (isset($formRequest['class'])) {
            echo '- ' . class_basename($formRequest['class']) . " ({$formRequest['class']})\n";
        }
    }
}
echo "\n";

// 2. Form requests with rules and authorization
echo "2. Form requests with detailed information:\n";
$formRequestsWithDetails = Atlas::scan('form_requests', [
    'include_rules' => true,
    'include_authorization' => true,
    'include_attributes' => true,
]);

if (isset($formRequestsWithDetails['data']) && is_array($formRequestsWithDetails['data'])) {
    foreach ($formRequestsWithDetails['data'] as $formRequest) {
        if (isset($formRequest['class'])) {
            $className = class_basename($formRequest['class']);
            echo "Form Request: {$className}\n";

            // Show validation rules
            if (isset($formRequest['rules']) && is_array($formRequest['rules'])) {
                echo '  Validation rules: ' . count($formRequest['rules']) . " fields\n";
                foreach (array_slice($formRequest['rules'], 0, 3, true) as $field => $rules) { // Show first 3
                    $rulesStr = is_array($rules) ? implode(', ', $rules) : (string) $rules;
                    echo "    - {$field}: {$rulesStr}\n";
                }
                if (count($formRequest['rules']) > 3) {
                    echo '    ... and ' . (count($formRequest['rules']) - 3) . " more fields\n";
                }
            }

            // Show authorization
            if (isset($formRequest['authorization']) && is_array($formRequest['authorization'])) {
                $auth = $formRequest['authorization'];
                if (isset($auth['method_exists']) && $auth['method_exists']) {
                    $returnType = 'unknown';
                    if (isset($auth['always_true']) && $auth['always_true']) {
                        $returnType = 'always allows';
                    } elseif (isset($auth['always_false']) && $auth['always_false']) {
                        $returnType = 'always denies';
                    } elseif (isset($auth['uses_auth']) && $auth['uses_auth']) {
                        $returnType = 'uses authentication';
                    }
                    echo "  Authorization: {$returnType}\n";
                }
            }

            // Show custom attributes
            if (isset($formRequest['attributes']) && is_array($formRequest['attributes']) && ! empty($formRequest['attributes'])) {
                echo '  Custom attributes: ' . count($formRequest['attributes']) . "\n";
                foreach (array_slice($formRequest['attributes'], 0, 2, true) as $field => $label) { // Show first 2
                    echo "    - {$field}: {$label}\n";
                }
                if (count($formRequest['attributes']) > 2) {
                    echo '    ... and ' . (count($formRequest['attributes']) - 2) . " more\n";
                }
            }

            // Show custom messages
            if (isset($formRequest['messages']) && is_array($formRequest['messages']) && ! empty($formRequest['messages'])) {
                echo '  Custom messages: ' . count($formRequest['messages']) . "\n";
            }

            echo "\n";
        }
    }
}

// 3. Validation rules analysis
echo "3. Validation rules analysis:\n";
if (isset($formRequestsWithDetails['data']) && is_array($formRequestsWithDetails['data'])) {
    $ruleCounts = [];
    $authPatterns = [];

    foreach ($formRequestsWithDetails['data'] as $formRequest) {
        // Count validation rules
        if (isset($formRequest['rules']) && is_array($formRequest['rules'])) {
            foreach ($formRequest['rules'] as $field => $rules) {
                if (is_array($rules)) {
                    foreach ($rules as $rule) {
                        // Extract base rule name (before any parameters)
                        $baseRule = explode(':', $rule)[0];
                        $baseRule = explode('|', $baseRule)[0];
                        $ruleCounts[$baseRule] = ($ruleCounts[$baseRule] ?? 0) + 1;
                    }
                }
            }
        }

        // Analyze authorization patterns
        if (isset($formRequest['authorization']) && is_array($formRequest['authorization']) && isset($formRequest['authorization']['method_exists'])) {
            $auth = $formRequest['authorization'];
            if (isset($auth['always_true']) && $auth['always_true']) {
                $authPatterns['always_true'] = ($authPatterns['always_true'] ?? 0) + 1;
            } elseif (isset($auth['uses_auth']) && $auth['uses_auth']) {
                $authPatterns['uses_auth'] = ($authPatterns['uses_auth'] ?? 0) + 1;
            } elseif (isset($auth['uses_can']) && $auth['uses_can']) {
                $authPatterns['uses_policies'] = ($authPatterns['uses_policies'] ?? 0) + 1;
            }
        }
    }

    if (! empty($ruleCounts)) {
        echo "Most common validation rules:\n";
        arsort($ruleCounts);
        foreach (array_slice($ruleCounts, 0, 5, true) as $rule => $count) {
            echo "- {$rule}: used {$count} times\n";
        }
    }

    if (! empty($authPatterns)) {
        echo "Authorization patterns:\n";
        foreach ($authPatterns as $pattern => $count) {
            echo "- {$pattern}: {$count} form requests\n";
        }
    }
}
echo "\n";

// 4. Export form requests to different formats
echo "4. Exporting form requests:\n";

// JSON export
$jsonExport = Atlas::export('form_requests', 'json');
echo '- JSON export ready (length: ' . strlen($jsonExport) . " characters)\n";

// Markdown export
$markdownExport = Atlas::export('form_requests', 'markdown');
echo '- Markdown export ready (length: ' . strlen($markdownExport) . " characters)\n";

// HTML export
$htmlExport = Atlas::export('form_requests', 'html');
echo '- HTML export ready (length: ' . strlen($htmlExport) . " characters)\n";

// 5. Custom form request analysis
echo "\n5. Custom form request analysis:\n";
$customFormRequests = Atlas::scan('form_requests', [
    'paths' => [app_path('Http/Requests')],
]);

echo 'Form requests found in custom path: ' . ($customFormRequests['count'] ?? 0) . "\n";

if (isset($customFormRequests['data']) && is_array($customFormRequests['data'])) {
    foreach ($customFormRequests['data'] as $formRequest) {
        if (isset($formRequest['class'], $formRequest['rules'])) {
            $ruleCount = is_array($formRequest['rules']) ? count($formRequest['rules']) : 0;
            echo '- ' . class_basename($formRequest['class']) . " ({$ruleCount} validation rules)\n";
        }
    }
}

echo "\nForm requests analysis example completed successfully!\n";
