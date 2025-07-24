<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '👁️',
        'title' => $observer['name'],
        'badge' => 'Observer',
        'badgeColor' => 'bg-cyan-100 text-cyan-600 dark:bg-cyan-900 dark:text-cyan-300',
        'namespace' => $observer['namespace'],
        'class' => $observer['class']
    ])

    {{-- Observed Model --}}
    @if (!empty($observer['model']))
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧱',
            'label' => 'Observed Model',
            'value' => class_basename($observer['model']),
            'type' => 'simple'
        ])
    @endif

    {{-- Event Methods --}}
    @if (!empty($observer['event_methods']))
        <div class="mb-3">
            <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                🎯 Event Methods ({{ count($observer['event_methods']) }})
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                @foreach ($observer['event_methods'] as $method)
                    <div class="text-xs bg-cyan-50 dark:bg-cyan-900/20 rounded px-2 py-1 text-center">
                        <span class="font-mono text-cyan-600 dark:text-cyan-400">{{ $method }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Constructor Dependencies --}}
    @if (!empty($observer['dependencies']))
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔗',
            'label' => 'Dependencies',
            'value' => implode(', ', array_map('class_basename', $observer['dependencies'])),
            'type' => 'code'
        ])
    @endif

    {{-- Methods Details --}}
    @if (!empty($observer['methods']))
    @endif

    {{-- Méthodes --}}
    @include('atlas::exports.partials.common.collapsible-methods', [
        'methods' => $observer['methods'] ?? [],
        'componentId' => 'observer-' . md5($observer['class']),
        'title' => 'Méthodes',
        'icon' => '⚙️',
        'collapsed' => true
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $observer['class'],
        'file' => $observer['file']
    ])
</div>
