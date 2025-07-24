{{-- Notification Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '📨',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Notification',
                'class' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300',
                'icon' => '📨'
            ],
            [
                'text' => $item['should_queue'] ? 'Queued' : 'Immediate',
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
        </div>

        {{-- Delivery Channels --}}
        @if (!empty($item['channels']))
            <div>
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Delivery Channels',
                    'value' => $item['channels'],
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('atlas::exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'notification'
            ])
        @endif
    </div>
</div>