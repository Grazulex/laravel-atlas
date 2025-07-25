<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
{{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '⚡',
        'title' => class_basename($item['class']),
        'badge' => 'Job',
        'badgeColor' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200',
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
        {{-- Queueable Status --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '�',
            'label' => 'Queueable',
            'value' => $item['queueable'] ? 'Yes' : 'No',
            'type' => 'simple'
        ])

        {{-- Traits Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔧',
            'label' => 'Traits',
            'value' => !empty($item['traits']) ? count($item['traits']) . ' traits' : '0 traits',
            'type' => 'simple'
        ])

        {{-- Properties Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📋',
            'label' => 'Properties',
            'value' => !empty($item['properties']) ? count($item['properties']) . ' properties' : '0 properties',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Traits --}}
        @if (!empty($item['traits']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🔧</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Traits ({{ count($item['traits']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($item['traits'] as $trait)
                            <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200 font-medium">
                                {{ class_basename($trait) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Queue Configuration --}}
        @if (!empty($item['queue_config']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">⚙️</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Queue Configuration
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($item['queue_config'] as $key => $value)
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($key) }}:</span>
                                <code class="ml-2 text-blue-600 dark:text-blue-400">{{ $value }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Properties --}}
        @if (!empty($item['properties']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">📋</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Properties ({{ count($item['properties']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($item['properties'] as $property)
                            <div class="text-xs bg-white dark:bg-gray-800 rounded p-2 border">
                                @if (isset($property['type']))
                                    <span class="text-purple-600 dark:text-purple-400">{{ $property['type'] }}</span>
                                @endif
                                <code class="text-blue-600 dark:text-blue-400">${{ $property['name'] ?? 'property' }}</code>
                                @if (isset($property['visibility']))
                                    <span class="ml-2 text-xs px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                        {{ $property['visibility'] }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Constructor Parameters --}}
        @if (!empty($item['constructor']['parameters']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🏗️</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Constructor Parameters ({{ count($item['constructor']['parameters']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($item['constructor']['parameters'] as $param)
                            <div class="text-xs bg-white dark:bg-gray-800 rounded p-2 border">
                                @if (isset($param['type']))
                                    <span class="text-purple-600 dark:text-purple-400">{{ class_basename($param['type']) }}</span>
                                @endif
                                <code class="text-blue-600 dark:text-blue-400">${{ $param['name'] ?? 'param' }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Flow Dependencies --}}
        @if (!empty($item['flow']['dependencies']) && array_filter($item['flow']['dependencies']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">📦</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Dependencies
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-3">
                        @foreach(['models' => '🗃️', 'services' => '🔧', 'facades' => '🏛️', 'classes' => '📦'] as $depType => $icon)
                            @if (!empty($item['flow']['dependencies'][$depType]))
                                <div>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $icon }} {{ ucfirst($depType) }}:</span>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach ($item['flow']['dependencies'][$depType] as $dep)
                                            <span class="text-xs px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                                {{ class_basename($dep) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
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
            'componentId' => 'job-' . md5($item['class']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])

        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $item['flow'] ?? [],
            'type' => 'job'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $item['class'],
        'file' => $item['file']
    ])
</div>