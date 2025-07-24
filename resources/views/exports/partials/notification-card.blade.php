<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '📬',
        'title' => class_basename($notification['class']),
        'badge' => 'Notification',
        'badgeColor' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200',
        'class' => $notification['class']
    ])

    {{-- Description --}}
    @if (!empty($notification['description']))
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-3">{{ $notification['description'] }}</p>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Channels --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📡',
            'label' => 'Channels',
            'type' => 'list',
            'items' => !empty($notification['channels']) ? $notification['channels'] : []
        ])

        {{-- Methods --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Defined Methods',
            'type' => 'list',
            'items' => !empty($notification['methods']) ? $notification['methods'] : []
        ])

        {{-- Full Class Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🏷️',
            'label' => 'Full Class Name',
            'value' => $notification['class'],
            'type' => 'code'
        ])
    </div>

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $notification['flow'] ?? [],
        'type' => 'notification'
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $notification['class'],
        'file' => $notification['file']
    ])
</div>
