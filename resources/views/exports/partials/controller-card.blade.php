<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🎮',
        'title' => class_basename($controller['class']),
        'badge' => 'Controller',
        'badgeColor' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200'
    ])

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Traits --}}
        @if (!empty($controller['traits']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🧩',
                'label' => 'Traits',
                'type' => 'list',
                'items' => $controller['traits']
            ])
        @endif

        {{-- Constructor Dependencies --}}
        @if (!empty($controller['constructor']['parameters']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🏗️',
                'label' => 'Constructor Dependencies',
                'type' => 'properties',
                'items' => $controller['constructor']['parameters']
            ])
        @endif

        {{-- Middlewares --}}
        @if (!empty($controller['middlewares']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🛡️',
                'label' => 'Applied Middlewares',
                'type' => 'list',
                'items' => $controller['middlewares']
            ])
        @endif

        {{-- Public Methods --}}
        @if (!empty($controller['methods']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '⚙️',
                'label' => 'Public Methods',
                'type' => 'methods',
                'items' => $controller['methods']
            ])
        @endif

        {{-- Dependencies Summary --}}
        @if (!empty(array_filter($controller['dependencies'])))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '📦',
                'label' => 'Dependencies Summary',
                'type' => 'dependencies',
                'items' => $controller['dependencies']
            ])
        @endif

        {{-- Full Class Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🏷️',
            'label' => 'Full Class Name',
            'value' => $controller['class'],
            'type' => 'code'
        ])
    </div>

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $controller['flow'] ?? [],
        'type' => 'controller'
    ])
</div>
