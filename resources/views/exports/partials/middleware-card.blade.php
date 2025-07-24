<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
{{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🛡️',
        'title' => class_basename($item['class']),
        'badge' => $item['has_terminate'] ? 'Terminable' : 'Standard',
        'badgeColor' => $item['has_terminate'] ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        'namespace' => $item['namespace'],
        'class' => $item['class']
    ])

    {{-- Description --}}
    @if (!empty($item['description']))
        <div class="mb-4">
            <p class="text-xs text-gray-600 dark:text-gray-300 italic bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                {{ $item['description'] }}
            </p>
        </div>
    @endif

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Terminable Status --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚡',
            'label' => 'Terminable',
            'value' => $item['has_terminate'] ? 'Yes' : 'No',
            'type' => 'simple'
        ])

        {{-- Dependencies Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧩',
            'label' => 'Dependencies',
            'value' => !empty($item['dependencies']) ? count($item['dependencies']) . ' dependencies' : '0 dependencies',
            'type' => 'simple'
        ])

        {{-- Parameters Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Parameters',
            'value' => !empty($item['parameters']) ? count($item['parameters']) . ' parameters' : '0 parameters',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Constructor Dependencies --}}
        @if (!empty($item['dependencies']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🧩</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Constructor Dependencies ({{ count($item['dependencies']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($item['dependencies'] as $dependency)
                            @if ($dependency)
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 font-medium">
                                    {{ class_basename($dependency) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Handle Parameters --}}
        @if (!empty($item['parameters']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">⚙️</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Handle Parameters ({{ count($item['parameters']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($item['parameters'] as $param)
                            <div class="text-xs bg-white dark:bg-gray-800 rounded p-2 border">
                                <span class="text-purple-600 dark:text-purple-400">{{ $param['type'] }}</span>
                                <code class="text-blue-600 dark:text-blue-400">${{ $param['name'] }}</code>
                                @if ($param['has_default'])
                                    <span class="text-gray-500"> = </span>
                                    <code class="text-green-600 dark:text-green-400">
                                        {{ is_string($param['default']) ? "'{$param['default']}'" : var_export($param['default'], true) }}
                                    </code>
                                @endif
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
            'methods' => $item['methods'] ?? [],
            'componentId' => 'middleware-' . md5($item['class']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])

        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $item['flow'] ?? [],
            'type' => 'middleware'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $item['class'],
        'file' => $item['file'] ?? 'N/A'
    ])
</div>