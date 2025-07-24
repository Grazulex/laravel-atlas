{{-- Service Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🔧',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Service',
                'class' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                'icon' => '🔧'
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

        {{-- Constructor Dependencies --}}
        @if (!empty($item['dependencies']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Constructor Dependencies</dt>
                <dd>
                    <div class="space-y-2">
                        @foreach ($item['dependencies'] as $dependency)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <code class="text-sm font-medium">${{ $dependency['name'] }}</code>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">: {{ class_basename($dependency['type']) }}</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        @if ($dependency['nullable'])
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">nullable</span>
                                        @endif
                                        @if ($dependency['hasDefault'])
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">default</span>
                                        @endif
                                    </div>
                                </div>
                                @if ($dependency['type'] !== 'mixed')
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Full type: <code>{{ $dependency['type'] }}</code>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </dd>
            </div>
        @endif

        {{-- Public Methods --}}
        @if (!empty($item['methods']))
            <div>
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Public Methods',
                    'value' => $item['methods'],
                    'type' => 'method-list'
                ])
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('atlas::exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'service'
            ])
        @endif
    </div>
</div>