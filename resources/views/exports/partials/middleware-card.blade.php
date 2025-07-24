<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🛡️',
        'title' => class_basename($middleware['class']),
        'badge' => $middleware['has_terminate'] ? 'Terminable' : 'Standard',
        'badgeColor' => $middleware['has_terminate'] ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        'namespace' => $middleware['namespace'],
        'class' => $middleware['class']
    ])

    {{-- Description --}}
    @if (!empty($middleware['description']))
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-3">{{ $middleware['description'] }}</p>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Constructor Dependencies --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧩',
            'label' => 'Constructor Dependencies',
            'type' => 'list',
            'items' => !empty($middleware['dependencies']) ? collect($middleware['dependencies'])->filter()->map('class_basename')->toArray() : []
        ])

        {{-- Handle Parameters --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Handle Parameters',
            'type' => 'list',
            'items' => !empty($middleware['parameters']) ? collect($middleware['parameters'])->map(function($param) {
                $default = $param['has_default'] ? ' = ' . (is_string($param['default']) ? "'{$param['default']}'" : var_export($param['default'], true)) : '';
                return $param['type'] . ' ' . $param['name'] . $default;
            })->toArray() : []
        ])

        {{-- Full Class Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🏷️',
            'label' => 'Full Class Name',
            'value' => $middleware['class'],
            'type' => 'code'
        ])
    </div>

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $middleware['flow'] ?? [],
        'type' => 'middleware'
    ])

    {{-- Méthodes --}}
    @include('atlas::exports.partials.common.collapsible-methods', [
        'methods' => $middleware['methods'] ?? [],
        'componentId' => 'middleware-' . md5($middleware['class']),
        'title' => 'Méthodes',
        'icon' => '⚙️',
        'collapsed' => true
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $middleware['class'],
        'file' => $middleware['file']
    ])
</div>
