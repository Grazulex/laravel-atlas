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

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Related Model --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧱',
            'label' => 'Related Model',
            'value' => !empty($policy['model']) ? class_basename($policy['model']) : 'Not Set',
            'type' => 'simple'
        ])

        {{-- Abilities Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔑',
            'label' => 'Abilities',
            'value' => !empty($policy['abilities']) ? count($policy['abilities']) . ' abilities' : '0 abilities',
            'type' => 'simple'
        ])

        {{-- Methods Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Methods',
            'value' => !empty($policy['methods']) ? count($policy['methods']) . ' methods' : '0 methods',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Abilities --}}
        @if (!empty($policy['abilities']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🔑</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Abilities ({{ count($policy['abilities']) }})
                    </h4>
                </div>
                <div class="space-y-2">
                    @foreach ($policy['abilities'] as $ability)
                        <div class="text-xs bg-red-50 dark:bg-red-900/20 rounded p-3 border border-red-200 dark:border-red-800">
                            <div class="font-mono">
                                <span class="text-purple-600 dark:text-purple-400 font-semibold">{{ $ability['name'] }}</span>
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
    </div>

    {{-- Interactive Sections --}}
    <div class="mt-6 space-y-4">
        {{-- Methods Section --}}
        @include('atlas::exports.partials.common.collapsible-methods', [
            'methods' => $policy['methods'] ?? [],
            'componentId' => 'policy-' . md5($policy['class']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $policy['class'],
        'file' => $policy['file']
    ])
</div>
