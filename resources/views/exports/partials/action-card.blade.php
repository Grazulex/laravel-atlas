{{-- Action Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('exports.partials.common.card-header', [
        'icon' => '⚡',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Action',
                'class' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                'icon' => '⚡'
            ],
            @if (!empty($item['invokable']))
                [
                    'text' => 'Invokable',
                    'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                    'icon' => '🎯'
                ]
            @endif
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

            @if (!empty($item['invokable']))
                @include('exports.partials.common.property-item', [
                    'label' => 'Type',
                    'value' => 'Invokable Action (single __invoke method)',
                    'type' => 'default'
                ])
            @endif
        </div>

        {{-- Action Parameters --}}
        @if (!empty($item['parameters']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Action Parameters</dt>
                <dd>
                    <div class="space-y-2">
                        @foreach ($item['parameters'] as $param)
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

        {{-- Constructor Parameters --}}
        @if (!empty($item['constructor']['parameters']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Constructor Parameters</dt>
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

        {{-- Action Methods --}}
        @if (!empty($item['methods']))
            <div>
                @include('exports.partials.common.property-item', [
                    'label' => 'Action Methods',
                    'value' => $item['methods'],
                    'type' => 'method-list'
                ])
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'action'
            ])
        @endif
    </div>
</div>