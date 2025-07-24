<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🛡️',
        'title' => $policy['name'],
        'badge' => 'Policy',
        'badgeColor' => 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300',
        'namespace' => $policy['namespace'],
        'class' => $policy['class']
    ])

    {{-- Related Model --}}
    @if (!empty($policy['model']))
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧱',
            'label' => 'Related Model',
            'value' => class_basename($policy['model']),
            'type' => 'simple'
        ])
    @endif

    {{-- Abilities --}}
    @if (!empty($policy['abilities']))
        <div class="mb-3">
            <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                🔑 Abilities ({{ count($policy['abilities']) }})
            </h4>
            <div class="space-y-2">
                @foreach ($policy['abilities'] as $ability)
                    <div class="text-xs bg-red-50 dark:bg-red-900/20 rounded p-2">
                        <div class="font-mono">
                            <span class="text-purple-600 dark:text-purple-400">{{ $ability['name'] }}</span>
                            <span class="text-gray-500">(</span>
                            @foreach ($ability['parameters'] as $index => $param)
                                @if ($index > 0), @endif
                                @if ($param['type'])
                                    <span class="text-gray-600 dark:text-gray-400">{{ class_basename($param['type']) }}</span>
                                @endif
                                <span class="text-blue-600 dark:text-blue-400">${{ $param['name'] }}</span>
                            @endforeach
                            <span class="text-gray-500">)</span>
                            : <span class="text-green-600 dark:text-green-400">{{ $ability['return_type'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $policy['class'],
        'file' => $policy['file']
    ])
</div>
