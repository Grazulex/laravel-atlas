<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
{{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🛣️',
        'title' => $item['uri'],
        'badge' => strtoupper($item['type']),
        'badgeColor' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200',
        'namespace' => $item['namespace'] ?? null,
        'class' => $item['class'] ?? null
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
        {{-- Route Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔖',
            'label' => 'Route Name',
            'value' => $item['name'] ?? 'Not Named',
            'type' => 'simple'
        ])

        {{-- HTTP Methods --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧭',
            'label' => 'HTTP Methods',
            'value' => !empty($item['methods']) ? implode(', ', $item['methods']) : 'GET',
            'type' => 'simple'
        ])

        {{-- Middleware Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🛡️',
            'label' => 'Middlewares',
            'value' => !empty($item['middleware']) ? count($item['middleware']) . ' middlewares' : '0 middlewares',
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
                @if ($item['is_closure'])
                    <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 font-medium">
                        Closure Function
                    </span>
                @else
                    <div class="space-y-2">
                        @if (!empty($item['controller']))
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Controller:</span>
                                <code class="ml-2 text-blue-600 dark:text-blue-400">{{ class_basename($item['controller']) }}</code>
                            </div>
                        @endif
                        @if (!empty($item['uses']))
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Method:</span>
                                <code class="ml-2 text-purple-600 dark:text-purple-400">{{ $item['uses'] }}</code>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Middleware --}}
        @if (!empty($item['middleware']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🛡️</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Applied Middlewares ({{ count($item['middleware']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($item['middleware'] as $middleware)
                            <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200 font-medium">
                                {{ $middleware }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Additional Properties --}}
        @if (!empty($item['prefix']) || !empty($item['domain']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🌐</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Additional Properties
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @if (!empty($item['prefix']))
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Prefix:</span>
                                <code class="ml-2 text-green-600 dark:text-green-400">{{ $item['prefix'] }}</code>
                            </div>
                        @endif
                        @if (!empty($item['domain']))
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Domain:</span>
                                <code class="ml-2 text-blue-600 dark:text-blue-400">{{ $item['domain'] }}</code>
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
            'flow' => $item['flow'] ?? [],
            'type' => 'route'
        ])
    </div>

    {{-- Footer --}}
    @php
        $footerClass = 'Route';
        $footerFile = 'Route Definition';
        
        if ($item['is_closure']) {
            $footerClass = 'Closure';
            $footerFile = 'Inline Closure';
        } else {
            if (!empty($item['controller'])) {
                $footerClass = $item['controller'];
                $footerFile = $item['controller_file'] ?? ($item['file'] ?? 'Controller File');
            }
        }
    @endphp
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $footerClass,
        'file' => $footerFile
    ])
</div>