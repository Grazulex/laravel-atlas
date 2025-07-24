<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
<div class=    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Actions Count --}}-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🎮',
        'title' => class_basename($controller['class']),
        'badge' => 'Controller',
        'badgeColor' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200',
        'namespace' => $controller['namespace'],
        'class' => $controller['class']
    ])

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Methods Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Public Methods',
            'value' => !empty($controller['methods']) ? count($controller['methods']) . ' methods' : '0 methods',
            'type' => 'simple'
        ])

        {{-- Traits Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧩',
            'label' => 'Traits',
            'value' => !empty($controller['traits']) ? count($controller['traits']) . ' traits' : '0 traits',
            'type' => 'simple'
        ])

        {{-- Middlewares Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '�️',
            'label' => 'Middlewares',
            'value' => !empty($controller['middlewares']) ? count($controller['middlewares']) . ' middlewares' : '0 middlewares',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Traits --}}
        @if (!empty($controller['traits']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🧩</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Traits ({{ count($controller['traits']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($controller['traits'] as $trait)
                            <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200 font-medium">
                                {{ class_basename($trait) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Constructor Dependencies --}}
        @if (!empty($controller['constructor']['parameters']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🏗️</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Constructor Dependencies ({{ count($controller['constructor']['parameters']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($controller['constructor']['parameters'] as $param)
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

        {{-- Applied Middlewares --}}
        @if (!empty($controller['middlewares']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🛡️</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Applied Middlewares ({{ count($controller['middlewares']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($controller['middlewares'] as $middleware)
                            <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200 font-medium">
                                {{ $middleware }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Dependencies Summary --}}
        @if (!empty(array_filter($controller['dependencies'])))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">📦</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Dependencies Summary
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($controller['dependencies'] as $type => $deps)
                            @if (!empty($deps))
                                <div class="text-xs">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($type) }}:</span>
                                    <div class="ml-2 flex flex-wrap gap-1 mt-1">
                                        @foreach ($deps as $dep)
                                            <span class="px-2 py-1 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
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
            'methods' => $controller['methods'] ?? [],
            'componentId' => 'controller-' . md5($controller['class']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])

        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $controller['flow'] ?? [],
            'type' => 'controller'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $controller['class'],
        'file' => $controller['file']
    ])
</div>
</div>