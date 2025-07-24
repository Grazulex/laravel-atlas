<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
{{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '⚡',
        'title' => $item['name'],
        'badge' => 'Action',
        'badgeColor' => 'bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-300',
        'namespace' => $item['namespace'],
        'class' => $item['class']
    ])

    {{-- Constructor Dependencies --}}
    @if (!empty($item['dependencies']))
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔗',
            'label' => 'Dependencies',
            'value' => implode(', ', array_map('class_basename', $item['dependencies'])),
            'type' => 'code'
        ])
    @endif

    {{-- Constructor Parameters --}}
    @if (!empty($item['constructor']['parameters']))
        <div class="mb-3">
            <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                🔧 Constructor Parameters
            </h4>
            <div class="space-y-1">
                @foreach ($item['constructor']['parameters'] as $param)
                    <div class="text-xs bg-gray-50 dark:bg-gray-700 rounded p-2">
                        <span class="font-mono text-blue-600 dark:text-blue-400">${{ $param['name'] }}</span>
                        @if ($param['type'])
                            : <span class="text-gray-600 dark:text-gray-400">{{ class_basename($param['type']) }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Méthodes --}}
    @include('atlas::exports.partials.common.collapsible-methods', [
        'methods' => $item['methods'] ?? [],
        'componentId' => 'action-' . md5($item['class']),
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