{{-- Observer Card Component --}}
@include('exports.partials.common.card-wrapper', ['class' => ''])
    @include('exports.partials.common.card-header', [
        'icon' => '👁️',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Observer',
                'class' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                'icon' => '👁️'
            ],
            [
                'text' => count($item['events'] ?? []) . ' Event' . (count($item['events'] ?? []) !== 1 ? 's' : ''),
                'class' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                'icon' => '📡'
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

            @if (!empty($item['model']))
                @include('exports.partials.common.property-item', [
                    'label' => 'Observed Model',
                    'value' => $item['model'],
                    'type' => 'code'
                ])
            @endif
        </div>

        {{-- Observer Events --}}
        @if (!empty($item['events']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Observed Events</dt>
                <dd>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                        @foreach ($item['events'] as $event)
                            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-3 text-center">
                                <div class="text-xs font-semibold text-purple-800 dark:text-purple-300 uppercase tracking-wide">{{ $event }}</div>
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

        {{-- Observer Methods --}}
        @if (!empty($item['methods']))
            <div>
                @include('exports.partials.common.property-item', [
                    'label' => 'Observer Methods',
                    'value' => $item['methods'],
                    'type' => 'method-list'
                ])
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'observer'
            ])
        @endif
    </div>
@endinclude