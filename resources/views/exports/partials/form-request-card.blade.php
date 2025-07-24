{{-- Form Request Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '📝',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Form Request',
                'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                'icon' => '📝'
            ],
            [
                'text' => count($item['rules']) . ' rules',
                'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'icon' => '📏'
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

        {{-- Validation Rules --}}
        @if (!empty($item['rules']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Validation Rules</dt>
                <dd>
                    <div class="space-y-2">
                        @foreach ($item['rules'] as $field => $rules)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="flex items-start justify-between mb-2">
                                    <code class="text-sm font-medium">{{ $field }}</code>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @if (is_array($rules))
                                        @foreach ($rules as $rule)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ $rule }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $rules }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('atlas::exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'form-request'
            ])
        @endif
    </div>
</div>