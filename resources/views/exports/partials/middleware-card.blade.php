{{-- Middleware Card Component --}}
@include('exports.partials.common.card-wrapper', ['class' => ''])
    @include('exports.partials.common.card-header', [
        'icon' => '🔄',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Middleware',
                'class' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-300',
                'icon' => '🔄'
            ],
            [
                'text' => $item['global'] ? 'Global' : 'Route-specific',
                'class' => $item['global'] ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                'icon' => $item['global'] ? '🌍' : '🎯'
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

            @if (isset($item['alias']))
                @include('exports.partials.common.property-item', [
                    'label' => 'Alias',
                    'value' => $item['alias'],
                    'type' => 'code'
                ])
            @endif

            @include('exports.partials.common.property-item', [
                'label' => 'Scope',
                'value' => $item['global'] ? 'Applied to all requests' : 'Applied only to specific routes',
                'type' => 'default'
            ])
        </div>

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'middleware'
            ])
        @endif
    </div>
@endinclude