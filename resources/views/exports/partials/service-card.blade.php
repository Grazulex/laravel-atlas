<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
{{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🔧',
        'title' => class_basename($service['class']),
        'badge' => 'Service',
        'badgeColor' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200',
        'namespace' => $service['namespace'],
        'class' => $service['class']
    ])

    {{-- Description --}}
    @if (!empty($service['description']))
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-3">{{ $service['description'] }}</p>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Public Methods --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Public Methods',
            'type' => 'list',
            'items' => !empty($service['methods']) ? collect($service['methods'])->map(function($method) {
                return $method['name'] . '(' . implode(', ', $method['parameters']) . ')';
            })->toArray() : []
        ])

        {{-- Constructor Dependencies --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧩',
            'label' => 'Constructor Dependencies',
            'type' => 'list',
            'items' => !empty($service['dependencies']) ? collect($service['dependencies'])->filter()->map('class_basename')->toArray() : []
        ])
    </div>

    {{-- Méthodes --}}
    @include('atlas::exports.partials.common.collapsible-methods', [
        'methods' => $service['methods'] ?? [],
        'componentId' => 'service-' . md5($service['class']),
        'title' => 'Méthodes',
        'icon' => '⚙️',
        'collapsed' => true
    ])

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $service['flow'] ?? [],
        'type' => 'service'
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $service['class'],
        'file' => $service['file']
    ])
</div>