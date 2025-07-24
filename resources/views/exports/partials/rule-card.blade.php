<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '📏',
        'title' => $rule['name'],
        'badge' => 'Rule',
        'badgeColor' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300'
    ])

    {{-- Namespace --}}
    @include('atlas::exports.partials.common.property-item', [
        'icon' => '📦',
        'label' => 'Namespace',
        'value' => $rule['namespace'],
        'type' => 'code'
    ])

    {{-- Constructor Parameters --}}
    @if (!empty($rule['constructor']['parameters']))
        <div class="mb-3">
            <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                🔧 Constructor Parameters
            </h4>
            <div class="space-y-1">
                @foreach ($rule['constructor']['parameters'] as $param)
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

    {{-- Methods --}}
    @if (!empty($rule['methods']))
        <div class="mb-3">
            <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                🔧 Methods ({{ count($rule['methods']) }})
            </h4>
            <div class="space-y-2">
                @foreach ($rule['methods'] as $method)
                    <div class="text-xs bg-yellow-50 dark:bg-yellow-900/20 rounded p-2">
                        <div class="font-mono">
                            <span class="text-purple-600 dark:text-purple-400">{{ $method['name'] }}</span>
                            <span class="text-gray-500">(</span>
                            @foreach ($method['parameters'] as $index => $param)
                                @if ($index > 0), @endif
                                @if ($param['type'])
                                    <span class="text-gray-600 dark:text-gray-400">{{ class_basename($param['type']) }}</span>
                                @endif
                                <span class="text-blue-600 dark:text-blue-400">${{ $param['name'] }}</span>
                            @endforeach
                            <span class="text-gray-500">)</span>
                            @if ($method['return_type'])
                                : <span class="text-green-600 dark:text-green-400">{{ class_basename($method['return_type']) }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Message --}}
    @if (!empty($rule['message']))
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '💬',
            'label' => 'Message',
            'value' => $rule['message'],
            'type' => 'simple'
        ])
    @endif

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $rule['class'],
        'file' => $rule['file']
    ])
</div>
