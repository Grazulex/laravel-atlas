<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🛣️',
        'title' => $route['uri'],
        'badge' => strtoupper($route['type']),
        'badgeColor' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200',
        'namespace' => $route['namespace'] ?? null,
        'class' => $route['class'] ?? null
    ])

    {{-- Description --}}
    @if (!empty($route['description']))
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-3">{{ $route['description'] }}</p>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔖',
            'label' => 'Name',
            'value' => $route['name'] ?? null,
            'type' => 'simple'
        ])

        {{-- Handler --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Handler',
            'value' => $route['is_closure'] ? 'Closure' : 
                (!empty($route['controller']) && !empty($route['uses']) ? 
                    class_basename($route['controller']) . '|' . $route['uses'] : 
                    (!empty($route['controller']) ? class_basename($route['controller']) : 'Unknown')),
            'type' => 'code'
        ])

        {{-- Methods --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧭',
            'label' => 'Methods',
            'value' => !empty($route['methods']) ? implode(', ', $route['methods']) : null,
            'type' => 'code'
        ])

        {{-- Middleware --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🛡️',
            'label' => 'Middleware',
            'type' => 'list',
            'items' => !empty($route['middleware']) ? $route['middleware'] : []
        ])

        {{-- Prefix --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📁',
            'label' => 'Prefix',
            'value' => $route['prefix'] ?? null,
            'type' => 'simple'
        ])

        {{-- Domain --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🌐',
            'label' => 'Domain',
            'value' => $route['domain'] ?? null,
            'type' => 'simple'
        ])
    </div>

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $route['flow'] ?? [],
        'type' => 'route'
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $route['class'] ?? 'N/A',
        'file' => $route['file'] ?? 'N/A'
    ])
</div>