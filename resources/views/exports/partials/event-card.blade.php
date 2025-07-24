<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '⚡',
        'title' => class_basename($event['class']),
        'badge' => 'Event',
        'badgeColor' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200'
    ])

    {{-- Broadcastable Badge --}}
    @if ($event['broadcastable'])
        <div class="mb-3">
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200">
                📡 Broadcastable
            </span>
        </div>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Traits --}}
        @if (!empty($event['traits']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🧩',
                'label' => 'Traits',
                'type' => 'list',
                'items' => $event['traits']
            ])
        @endif

        {{-- Properties --}}
        @if (!empty($event['properties']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🏗️',
                'label' => 'Properties',
                'type' => 'properties',
                'items' => $event['properties']
            ])
        @endif

        {{-- Broadcast Channels --}}
        @if (!empty($event['channels']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '📡',
                'label' => 'Broadcast Channels',
                'type' => 'list',
                'items' => $event['channels']
            ])
        @endif

        {{-- Listeners --}}
        @if (!empty($event['listeners']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '👂',
                'label' => 'Listeners',
                'type' => 'list',
                'items' => $event['listeners']
            ])
        @endif

        {{-- Full Class Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🏷️',
            'label' => 'Full Class Name',
            'value' => $event['class'],
            'type' => 'code'
        ])
    </div>

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $event['flow'] ?? [],
        'type' => 'event'
    ])
</div>
