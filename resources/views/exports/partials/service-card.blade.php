<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
{{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🔧',
        'title' => class_basename($item['class']),
        'badge' => 'Service',
        'badgeColor' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200',
        'namespace' => $item['namespace'],
        'class' => $item['class']
    ])

    {{-- Description --}}
    @if (!empty($item['description']))
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-3">{{ $item['description'] }}</p>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Public Methods --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Public Methods',
            'type' => 'list',
            'items' => !empty($item['methods']) ? collect($item['methods'])->map(function($method) {
                return $method['name'] . '(' . implode(', ', $method['parameters']) . ')';
            })->toArray() : []
        ])

        {{-- Constructor Dependencies --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧩',
            'label' => 'Constructor Dependencies',
            'type' => 'list',
            'items' => !empty($item['dependencies']) ? collect($item['dependencies'])->filter()->map('class_basename')->toArray() : []
        ])
    </div>

    {{-- Méthodes --}}
    @include('atlas::exports.partials.common.collapsible-methods', [
        'methods' => $item['methods'] ?? [],
        'componentId' => 'service-' . md5($item['class']),
        'title' => 'Méthodes',
        'icon' => '⚙️',
        'collapsed' => true
    ])

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $item['flow'] ?? [],
        'type' => 'service'
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $item['class'],
        'file' => $item['file']
    ])
</div>