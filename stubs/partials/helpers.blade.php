{{-- Helper functions for generating internal links --}}

@php
/**
 * Generate a link to a component if it exists in the data
 */
function componentLink($className, $componentType, $allData, $displayText = null) {
    if (!$className) return $displayText ?? 'N/A';
    
    $displayText = $displayText ?? class_basename($className);
    
    // Check if component exists in the data
    $exists = false;
    if (isset($allData[$componentType])) {
        foreach ($allData[$componentType] as $component) {
            if (isset($component['class_name']) && $component['class_name'] === $className) {
                $exists = true;
                break;
            }
        }
    }
    
    if ($exists) {
        return '<a href="#' . $componentType . '" class="component-link" data-component="' . htmlspecialchars($className) . '">' . htmlspecialchars($displayText) . '</a>';
    }
    
    return htmlspecialchars($displayText);
}

/**
 * Get component type mapping for different class patterns
 */
function getComponentTypeFromClass($className) {
    $basename = class_basename($className);
    
    if (str_ends_with($basename, 'Controller')) return 'controllers';
    if (str_ends_with($basename, 'Service')) return 'services';
    if (str_ends_with($basename, 'Job')) return 'jobs';
    if (str_ends_with($basename, 'Event')) return 'events';
    if (str_ends_with($basename, 'Listener')) return 'listeners';
    if (str_ends_with($basename, 'Observer')) return 'observers';
    if (str_ends_with($basename, 'Action')) return 'actions';
    if (str_ends_with($basename, 'Middleware')) return 'middleware';
    if (str_ends_with($basename, 'Policy')) return 'policies';
    if (str_ends_with($basename, 'Resource')) return 'resources';
    if (str_ends_with($basename, 'Request')) return 'requests';
    if (str_ends_with($basename, 'Rule')) return 'rules';
    if (str_ends_with($basename, 'Notification')) return 'notifications';
    
    // For models, check common model patterns
    if (preg_match('/^[A-Z][a-zA-Z]*$/', $basename) && !str_contains($className, 'Controller')) {
        return 'models';
    }
    
    return null;
}
@endphp
