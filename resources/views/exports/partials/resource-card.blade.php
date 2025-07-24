<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
{{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🔗',
        'title' => class_basename($item['class']),
        'badge' => 'API Resource',
        'badgeColor' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200',
        'namespace' => $item['namespace'],
        'class' => $item['class']
    ])

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Traits --}}
        @if (!empty($item['traits']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🧩',
                'label' => 'Traits',
                'type' => 'list',
                'items' => $item['traits']
            ])
        @endif

        {{-- Methods --}}
        @if (!empty($item['methods']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '⚙️',
                'label' => 'Custom Methods',
                'type' => 'methods',
                'items' => $item['methods']
            ])
        @endif

        {{-- Relationships --}}
        @if (!empty($item['relationships']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔗',
                'label' => 'Relationships',
                'type' => 'list',
                'items' => $item['relationships']
            ])
        @endif

        {{-- Conditional Fields --}}
        @if (!empty($item['conditionals']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔀',
                'label' => 'Conditional Fields',
                'type' => 'list',
                'items' => $item['conditionals']
            ])
        @endif

        {{-- Transformations --}}
        @if (!empty($item['transformations']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔄',
                'label' => 'Data Transformations',
                'type' => 'transformations',
                'items' => $item['transformations']
            ])
        @endif

        {{-- Full Class Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🏷️',
            'label' => 'Full Class Name',
            'value' => $item['class'],
            'type' => 'code'
        ])
    </div>

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $item['flow'] ?? [],
        'type' => 'resource'
    ])

    {{-- Méthodes --}}
    @include('atlas::exports.partials.common.collapsible-methods', [
        'methods' => $item['methods'] ?? [],
        'componentId' => 'resource-' . md5($item['class']),
        'title' => 'Méthodes',
        'icon' => '⚙️',
        'collapsed' => true
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $item['class'],
        'file' => $item['file']
    ])
</div>