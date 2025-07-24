<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '📏',
        'title' => $rule['name'],
        'badge' => 'Rule',
        'badgeColor' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300',
        'namespace' => $rule['namespace'],
        'class' => $rule['class'] ?? $rule['name']
    ])

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Namespace --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📦',
            'label' => 'Namespace',
            'value' => $rule['namespace'] ?? 'Not Set',
            'type' => 'simple'
        ])

        {{-- Constructor Parameters --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📝',
            'label' => 'Parameters',
            'value' => !empty($rule['constructor_parameters']) ? count($rule['constructor_parameters']) . ' parameters' : '0 parameters',
            'type' => 'simple'
        ])

        {{-- Methods Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Methods',
            'value' => !empty($rule['methods']) ? count($rule['methods']) . ' methods' : '0 methods',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Constructor Parameters --}}
        @if (!empty($rule['constructor_parameters']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">📝</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Constructor Parameters ({{ count($rule['constructor_parameters']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($rule['constructor_parameters'] as $param)
                            <div class="text-xs bg-white dark:bg-gray-800 rounded p-2 border">
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($param['type'])
                                            <span class="text-purple-600 dark:text-purple-400">{{ $param['type'] }}</span>
                                        @endif
                                        <code class="text-blue-600 dark:text-blue-400">${{ $param['name'] }}</code>
                                    </div>
                                    @if($param['has_default'])
                                        <span class="text-green-600 dark:text-green-400">= {{ $param['default_value'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Interactive Sections --}}
    <div class="mt-6 space-y-4">
        {{-- Methods Section --}}
        @include('atlas::exports.partials.common.collapsible-methods', [
            'methods' => $rule['methods'] ?? [],
            'componentId' => 'rule-' . md5($rule['name']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $rule['class'] ?? $rule['name'],
        'file' => $rule['file'] ?? 'N/A'
    ])
</div>
                    <div class="text-xs bg-purple-50 dark:bg-purple-900/20 rounded p-2">
                        <span class="font-mono text-purple-600 dark:text-purple-400">{{ class_basename($interface) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Has Message Method --}}
    @include('atlas::exports.partials.common.property-item', [
        'icon' => '💬',
        'label' => 'Has Message Method',
        'value' => $rule['message_method'] ? 'Yes' : 'No',
        'type' => 'simple'
    ])

    {{-- Méthodes --}}
    @include('atlas::exports.partials.common.collapsible-methods', [
        'methods' => $rule['methods'] ?? [],
        'componentId' => 'rule-' . md5($rule['class']),
        'title' => 'Méthodes',
        'icon' => '⚙️',
        'collapsed' => true
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $rule['class'],
        'file' => $rule['file']
    ])
</div>
