{{-- Listener Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '👂',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Listener',
                'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'icon' => '👂'
            ],
            [
                'text' => $item['should_queue'] ? 'Queued' : 'Synchronous',
                'class' => $item['should_queue'] ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                'icon' => $item['should_queue'] ? '⏳' : '⚡'
            ]
        ]
    ])

    <div class="p-6 space-y-6">
        {{-- Basic Information --}}
        <div class="grid md:grid-cols-2 gap-4">
            @include('atlas::exports.partials.common.property-item', [
                'label' => 'File Location',
                'value' => str_replace(base_path() . '/', '', $item['file']),
                'type' => 'code'
            ])

            @include('atlas::exports.partials.common.property-item', [
                'label' => 'Class',
                'value' => $item['class'],
                'type' => 'code'
            ])

            @include('atlas::exports.partials.common.property-item', [
                'label' => 'Execution Type',
                'value' => $item['should_queue'] ? 'Queued (Background)' : 'Synchronous (Immediate)',
                'type' => 'default'
            ])
        </div>

        {{-- Handled Events --}}
        @if (!empty($item['events']))
            <div>
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Handled Events',
                    'value' => array_map('class_basename', $item['events']),
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Constructor Dependencies --}}
        @if (!empty($item['constructor']['parameters']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Constructor Dependencies</dt>
                <dd>
                    <div class="space-y-2">
                        @foreach ($item['constructor']['parameters'] as $param)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <code class="text-sm font-medium">${{ $param['name'] }}</code>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">: {{ $param['type'] }}</span>
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
                            </div>
                        @endforeach
                    </div>
                </dd>
            </div>
        @endif

        {{-- Queue Configuration --}}
        @if ($item['should_queue'] && !empty($item['queue_config']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Queue Configuration</dt>
                <dd>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 space-y-2">
                        @if (!empty($item['queue_config']['connection']))
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Connection:</span>
                                <code class="ml-2">{{ $item['queue_config']['connection'] }}</code>
                            </div>
                        @endif
                        @if (!empty($item['queue_config']['queue']))
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Queue:</span>
                                <code class="ml-2">{{ $item['queue_config']['queue'] }}</code>
                            </div>
                        @endif
                        @if (!empty($item['queue_config']['delay']))
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Delay:</span>
                                <code class="ml-2">{{ $item['queue_config']['delay'] }}</code>
                            </div>
                        @endif
                    </div>
                </dd>
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('atlas::exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'listener'
            ])
        @endif
    </div>
</div>