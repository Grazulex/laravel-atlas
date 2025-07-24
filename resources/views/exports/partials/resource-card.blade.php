<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🔗',
        'title' => class_basename($resource['class']),
        'badge' => 'API Resource',
        'badgeColor' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200',
        'namespace' => $resource['namespace'],
        'class' => $resource['class']
    ])

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Traits --}}
        @if (!empty($resource['traits']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🧩',
                'label' => 'Traits',
                'type' => 'list',
                'items' => $resource['traits']
            ])
        @endif

        {{-- Methods --}}
        @if (!empty($resource['methods']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '⚙️',
                'label' => 'Custom Methods',
                'type' => 'methods',
                'items' => $resource['methods']
            ])
        @endif

        {{-- Relationships --}}
        @if (!empty($resource['relationships']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔗',
                'label' => 'Relationships',
                'type' => 'list',
                'items' => $resource['relationships']
            ])
        @endif

        {{-- Conditional Fields --}}
        @if (!empty($resource['conditionals']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔀',
                'label' => 'Conditional Fields',
                'type' => 'list',
                'items' => $resource['conditionals']
            ])
        @endif

        {{-- Transformations --}}
        @if (!empty($resource['transformations']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔄',
                'label' => 'Data Transformations',
                'type' => 'transformations',
                'items' => $resource['transformations']
            ])
        @endif

        {{-- Full Class Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🏷️',
            'label' => 'Full Class Name',
            'value' => $resource['class'],
            'type' => 'code'
        ])
    </div>

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $resource['flow'] ?? [],
        'type' => 'resource'
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $resource['class'],
        'file' => $resource['file']
    ])
</div>
