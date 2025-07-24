{{-- Job Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '📋',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Job',
                'class' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                'icon' => '📋'
            ],
            [
                'text' => $item['queueable'] ? 'Queueable' : 'Synchronous',
                'class' => $item['queueable'] ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                'icon' => $item['queueable'] ? '⏳' : '⚡'
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
                'value' => $item['queueable'] ? 'Queued (Background)' : 'Synchronous (Immediate)',
                'type' => 'default'
            ])
        </div>

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('atlas::exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'job'
            ])
        @endif
    </div>
</div>