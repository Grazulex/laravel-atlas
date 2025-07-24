{{-- Route Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🛣️',
        'title' => $item['uri'] ?: '/',
        'subtitle' => $item['name'] ? 'Route: ' . $item['name'] : 'Unnamed route',
        'badges' => array_merge(
            [
                [
                    'text' => ucfirst($item['type']),
                    'class' => match($item['type']) {
                        'api' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                        'admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                        'webhook' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                        'system' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        default => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                    },
                    'icon' => match($item['type']) {
                        'api' => '🔌',
                        'admin' => '👨‍💼',
                        'webhook' => '🪝',
                        'system' => '⚙️',
                        default => '🌐'
                    }
                ]
            ],
            array_map(fn($method) => [
                'text' => $method,
                'class' => match(strtoupper($method)) {
                    'GET' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                    'POST' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                    'PUT', 'PATCH' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                    'DELETE' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                }
            ], $item['methods'])
        )
    ])

    <div class="p-6 space-y-6">
        {{-- Basic Route Information --}}
        <div class="grid md:grid-cols-2 gap-4">
            @include('atlas::exports.partials.common.property-item', [
                'label' => 'URI Pattern',
                'value' => $item['uri'] ?: '/',
                'type' => 'code'
            ])

            @if ($item['name'])
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Route Name',
                    'value' => $item['name'],
                    'type' => 'code'
                ])
            @endif

            @include('atlas::exports.partials.common.property-item', [
                'label' => 'HTTP Methods',
                'value' => $item['methods'],
                'type' => 'badge-list'
            ])

            @if ($item['prefix'])
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Route Prefix',
                    'value' => $item['prefix'],
                    'type' => 'code'
                ])
            @endif
        </div>

        {{-- Action Information --}}
        <div>
            <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Route Action</dt>
            <dd>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    @if ($item['is_closure'])
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Closure</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Anonymous function</span>
                        </div>
                    @elseif ($item['controller'] && $item['uses'])
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Controller:</span>
                                <code class="ml-2">{{ class_basename($item['controller']) }}</code>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Method:</span>
                                <code class="ml-2">{{ $item['uses'] }}</code>
                            </div>
                        </div>
                    @else
                        <div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Action:</span>
                            <code class="ml-2">{{ $item['action'] }}</code>
                        </div>
                    @endif
                </div>
            </dd>
        </div>

        {{-- Middleware --}}
        @if (!empty($item['middleware']))
            <div>
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Applied Middleware',
                    'value' => array_map(function($middleware) {
                        return is_string($middleware) ? $middleware : class_basename($middleware);
                    }, $item['middleware']),
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('atlas::exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'route'
            ])
        @endif
    </div>
</div>