<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🎮',
        'title' => class_basename($item['class']),
        'badge' => 'Controller',
        'badgeColor' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200',
        'namespace' => $item['namespace'] ?? null,
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Methods Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Public Methods',
            'value' => !empty($item['methods']) ? count($item['methods']) . ' methods' : '0 methods',
            'type' => 'simple'
        ])

        {{-- Traits Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧩',
            'label' => 'Traits',
            'value' => !empty($item['traits']) ? count($item['traits']) . ' traits' : '0 traits',
            'type' => 'simple'
        ])

        {{-- Middlewares Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔄',
            'label' => 'Middlewares',
            'value' => !empty($item['middlewares']) ? count($item['middlewares']) . ' middlewares' : '0 middlewares',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Traits --}}
        @if (!empty($item['traits']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🧩</span>
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

        {{-- Middlewares --}}
        @if (!empty($item['middlewares']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🔄</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Middlewares ({{ count($item['middlewares']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($item['middlewares'] as $middleware)
                            <div class="flex items-center justify-between">
                                <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 font-medium">
                                    {{ is_array($middleware) ? $middleware['name'] ?? 'Unknown' : $middleware }}
                                </span>
                                @if (is_array($middleware) && !empty($middleware['parameters']))
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ implode(', ', $middleware['parameters']) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Actions/Routes --}}
        @if (!empty($item['actions']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🎯</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Actions ({{ count($item['actions']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($item['actions'] as $action)
                            <div class="flex items-center justify-between">
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 font-medium">
                                    {{ $action['method'] ?? 'Unknown' }}
                                </span>
                                @if (!empty($action['route']))
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $action['route'] }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Dependencies --}}
        @if (!empty($item['dependencies']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🔗</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Dependencies ({{ count($item['dependencies']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($item['dependencies'] as $dependency)
                            <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200 font-medium">
                                {{ class_basename($dependency) }}
                            </span>
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
            'componentId' => 'controller-' . md5($item['class']),
            'title' => 'Public Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])

        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $item['flow'] ?? [],
            'type' => 'controller'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $item['class'],
        'file' => $item['file'] ?? 'N/A'
    ])
</div>