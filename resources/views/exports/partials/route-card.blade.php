<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🛣️',
        'title' => $route['uri'],
        'badge' => strtoupper($route['type']),
        'badgeColor' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200',
        'namespace' => $route['namespace'] ?? null,
        'class' => $route['class'] ?? null
    ])

    {{-- Description --}}
    @if (!empty($route['description']))
        <div class="mb-4">
            <p class="text-xs text-gray-600 dark:text-gray-300 italic bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                {{ $route['description'] }}
            </p>
        </div>
    @endif

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Route Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔖',
            'label' => 'Route Name',
            'value' => $route['name'] ?? 'Not Named',
            'type' => 'simple'
        ])

        {{-- HTTP Methods --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧭',
            'label' => 'HTTP Methods',
            'value' => !empty($route['methods']) ? implode(', ', $route['methods']) : 'GET',
            'type' => 'simple'
        ])

        {{-- Middleware Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🛡️',
            'label' => 'Middlewares',
            'value' => !empty($route['middleware']) ? count($route['middleware']) . ' middlewares' : '0 middlewares',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Handler Details --}}
        <div>
            <div class="flex items-center mb-3">
                <span class="text-sm mr-2">⚙️</span>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Handler Details
                </h4>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                @if ($route['is_closure'])
                    <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 font-medium">
                        Closure Function
                    </span>
                @else
                    <div class="space-y-2">
                        @if (!empty($route['controller']))
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Controller:</span>
                                <code class="ml-2 text-blue-600 dark:text-blue-400">{{ class_basename($route['controller']) }}</code>
                            </div>
                        @endif
                        @if (!empty($route['uses']))
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Method:</span>
                                <code class="ml-2 text-purple-600 dark:text-purple-400">{{ $route['uses'] }}</code>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Middleware --}}
        @if (!empty($route['middleware']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🛡️</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Applied Middlewares ({{ count($route['middleware']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($route['middleware'] as $middleware)
                            <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200 font-medium">
                                {{ $middleware }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Additional Properties --}}
        @if (!empty($route['prefix']) || !empty($route['domain']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🌐</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Additional Properties
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @if (!empty($route['prefix']))
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Prefix:</span>
                                <code class="ml-2 text-green-600 dark:text-green-400">{{ $route['prefix'] }}</code>
                            </div>
                        @endif
                        @if (!empty($route['domain']))
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Domain:</span>
                                <code class="ml-2 text-blue-600 dark:text-blue-400">{{ $route['domain'] }}</code>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Interactive Sections --}}
    <div class="mt-6 space-y-4">
        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $route['flow'] ?? [],
            'type' => 'route'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $route['class'] ?? 'N/A',
        'file' => $route['file'] ?? 'N/A'
    ])
</div>