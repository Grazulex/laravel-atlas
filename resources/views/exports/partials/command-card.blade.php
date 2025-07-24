{{-- Command Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '⚡',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Artisan Command',
                'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                'icon' => '⚡'
            ],
            [
                'text' => $item['signature'] ? 'php artisan ' . explode(' ', $item['signature'])[0] : 'No signature',
                'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                'icon' => '💻'
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
        </div>

        {{-- Command Signature --}}
        @if ($item['signature'])
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Command Signature</dt>
                <dd>
                    <div class="bg-gray-900 dark:bg-gray-950 rounded-lg p-4">
                        <code class="text-green-400 font-mono text-sm">php artisan {{ $item['signature'] }}</code>
                    </div>
                </dd>
            </div>
        @endif

        {{-- Parsed Signature Details --}}
        @if (!empty($item['parsed_signature']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Signature Details</dt>
                <dd>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 space-y-3">
                        @if (!empty($item['parsed_signature']['command']))
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Command Name:</span>
                                <code class="ml-2">{{ $item['parsed_signature']['command'] }}</code>
                            </div>
                        @endif

                        @if (!empty($item['parsed_signature']['arguments']))
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Arguments:</span>
                                <div class="mt-1 space-y-1">
                                    @foreach ($item['parsed_signature']['arguments'] as $arg)
                                        <div class="flex items-center space-x-2">
                                            <code class="text-xs">{{ $arg['name'] }}</code>
                                            @if ($arg['required'])
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">required</span>
                                            @else
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">optional</span>
                                            @endif
                                            @if ($arg['description'])
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $arg['description'] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (!empty($item['parsed_signature']['options']))
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Options:</span>
                                <div class="mt-1 space-y-1">
                                    @foreach ($item['parsed_signature']['options'] as $opt)
                                        <div class="flex items-center space-x-2">
                                            <code class="text-xs">--{{ $opt['name'] }}</code>
                                            @if ($opt['shortcut'])
                                                <code class="text-xs">-{{ $opt['shortcut'] }}</code>
                                            @endif
                                            @if ($opt['required'])
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">required</span>
                                            @endif
                                            @if ($opt['description'])
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $opt['description'] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </dd>
            </div>
        @endif

        {{-- Description --}}
        @if ($item['description'])
            <div>
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Description',
                    'value' => $item['description']
                ])
            </div>
        @endif

        {{-- Aliases --}}
        @if (!empty($item['aliases']))
            <div>
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Command Aliases',
                    'value' => $item['aliases'],
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('atlas::exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'command'
            ])
        @endif
    </div>
</div>