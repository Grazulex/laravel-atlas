{{-- Event Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('exports.partials.common.card-header', [
        'icon' => '📢',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Event',
                'class' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                'icon' => '📢'
            ],
            [
                'text' => $item['should_broadcast'] ? 'Broadcastable' : 'Standard',
                'class' => $item['should_broadcast'] ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                'icon' => $item['should_broadcast'] ? '📡' : '📋'
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

            @if ($item['should_broadcast'])
                @include('exports.partials.common.property-item', [
                    'label' => 'Broadcasting',
                    'value' => 'This event can be broadcast to client applications',
                    'type' => 'default'
                ])
            @endif
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

        {{-- Constructor Parameters --}}
        @if (!empty($item['constructor']['parameters']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Event Data (Constructor Parameters)</dt>
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

        {{-- Properties --}}
        @if (!empty($item['properties']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Event Properties</dt>
                <dd>
                    <div class="space-y-2">
                        @foreach ($item['properties'] as $property)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <code class="text-sm font-medium">${{ $property['name'] }}</code>
                                    </div>
                                    <div class="flex space-x-1">
                                        <span class="text-xs px-1.5 py-0.5 rounded {{ $property['visibility'] === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : ($property['visibility'] === 'protected' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                            {{ $property['visibility'] }}
                                        </span>
                                        @if ($property['static'])
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">static</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </div>
        @endif

        {{-- Broadcasting Configuration --}}
        @if (!empty($item['broadcast_config']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Broadcasting Configuration</dt>
                <dd>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 space-y-2">
                        @if (!empty($item['broadcast_config']['channels']))
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Channels:</span>
                                <div class="mt-1">
                                    @foreach ($item['broadcast_config']['channels'] as $channel)
                                        <code class="text-xs mr-2 px-1.5 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded">{{ $channel }}</code>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if (!empty($item['broadcast_config']['event_name']))
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Event Name:</span>
                                <code class="ml-2">{{ $item['broadcast_config']['event_name'] }}</code>
                            </div>
                        @endif
                    </div>
                </dd>
            </div>
        @endif

        {{-- Related Listeners --}}
        @if (!empty($item['listeners']))
            <div>
                @include('exports.partials.common.property-item', [
                    'label' => 'Event Listeners',
                    'value' => $item['listeners'],
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'event'
            ])
        @endif
    </div>
</div>