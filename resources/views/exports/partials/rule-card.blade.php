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
    @if (!empty($rule['constructor_parameters']))
        <div class="mb-3">
            <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                📝 Constructor Parameters
            </h4>
            <div class="space-y-1">
                @foreach ($rule['constructor_parameters'] as $param)
                    <div class="text-xs bg-blue-50 dark:bg-blue-900/20 rounded p-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-blue-600 dark:text-blue-400">
                                ${{ $param['name'] }}@if($param['type']): {{ $param['type'] }}@endif
                            </span>
                            @if($param['has_default'])
                                <span class="text-gray-500 dark:text-gray-400">= {{ $param['default_value'] }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Implemented Interfaces --}}
    @if (!empty($rule['implements']))
        <div class="mb-3">
            <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                🔧 Implements
            </h4>
            <div class="space-y-1">
                @foreach ($rule['implements'] as $interface)
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
