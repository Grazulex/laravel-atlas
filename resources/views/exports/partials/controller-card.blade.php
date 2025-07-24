{{-- Controller Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('exports.partials.common.card-header', [
        'icon' => '🎮',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Controller',
                'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                'icon' => '🎮'
            ],
            [
                'text' => count($item['methods']) . ' methods',
                'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'icon' => '⚡'
            ]
        ]
    ])

    <div class="p-6 space-y-6">
        {{-- Basic Information --}}
        <div class="grid md:grid-cols-2 gap-4">
            @include('exports.partials.common.property-item', [
                'label' => 'File Location',
                'value' => str_replace(base_path() . '/', '', $item['file']),
                'type' => 'code'
            ])

            @include('exports.partials.common.property-item', [
                'label' => 'Class',
                'value' => $item['class'],
                'type' => 'code'
            ])
        </div>

        {{-- Traits --}}
        @if (!empty($item['traits']))
            <div>
                @include('exports.partials.common.property-item', [
                    'label' => 'Used Traits',
                    'value' => $item['traits'],
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Constructor --}}
        @if (!empty($item['constructor']['parameters']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Constructor Dependencies</dt>
                <dd>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <div class="space-y-2">
                            @foreach ($item['constructor']['parameters'] as $param)
                                <div class="flex items-center justify-between">
                                    <div class="text-sm">
                                        <code class="font-medium">${{ $param['name'] }}</code>
                                        <span class="text-gray-600 dark:text-gray-400">: {{ $param['type'] }}</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        @if ($param['nullable'])
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">nullable</span>
                                        @endif
                                        @if ($param['hasDefault'])
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">default</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </dd>
            </div>
        @endif

        {{-- Middlewares --}}
        @if (!empty($item['middlewares']))
            <div>
                @include('exports.partials.common.property-item', [
                    'label' => 'Applied Middlewares',
                    'value' => $item['middlewares'],
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Methods --}}
        @if (!empty($item['methods']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Controller Methods</dt>
                <dd>
                    <div class="space-y-2">
                        @foreach ($item['methods'] as $method)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <code class="text-sm font-medium">{{ $method['name'] }}</code>
                                        @if ($method['isStatic'])
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">static</span>
                                        @endif
                                    </div>
                                    @if ($method['returnType'])
                                        <span class="text-xs text-gray-600 dark:text-gray-400">
                                            → {{ $method['returnType'] }}
                                        </span>
                                    @endif
                                </div>
                                @if (!empty($method['parameters']))
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Parameters:</span>
                                        @foreach ($method['parameters'] as $param)
                                            <span class="ml-1">
                                                <code>${{ $param['name'] }}</code>
                                                @if ($param['type'] !== 'mixed')
                                                    <span class="text-gray-500">: {{ $param['type'] }}</span>
                                                @endif
                                                @if (!$loop->last), @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </dd>
            </div>
        @endif

        {{-- Dependencies --}}
        @if (!empty($item['dependencies']))
            <div>
                @include('exports.partials.common.property-item', [
                    'label' => 'Class Dependencies',
                    'value' => $item['dependencies'],
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'controller'
            ])
        @endif
    </div>
</div>