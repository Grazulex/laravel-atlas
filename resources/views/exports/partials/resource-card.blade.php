{{-- Resource Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🗃️',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'API Resource',
                'class' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300',
                'icon' => '🗃️'
            ],
            [
                'text' => $item['is_collection'] ? 'Collection' : 'Single Resource',
                'class' => $item['is_collection'] ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                'icon' => $item['is_collection'] ? '📚' : '📄'
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
                'label' => 'Resource Type',
                'value' => $item['is_collection'] ? 'Resource Collection' : 'Single Resource',
                'type' => 'default'
            ])
        </div>

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('atlas::exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'resource'
            ])
        @endif
    </div>
</div>