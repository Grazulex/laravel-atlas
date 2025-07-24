<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '⚡',
        'title' => class_basename($event['class']),
        'badge' => 'Event',
        'badgeColor' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200',
        'namespace' => $event['namespace'],
        'class' => $event['class']
    ])

    {{-- Description --}}
    @if (!empty($event['description']))
        <div class="mb-4">
            <p class="text-xs text-gray-600 dark:text-gray-300 italic bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                {{ $event['description'] }}
            </p>
        </div>
    @endif

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Broadcastable Status --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📡',
            'label' => 'Broadcastable',
            'value' => $event['broadcastable'] ? 'Yes' : 'No',
            'type' => 'simple'
        ])

        {{-- Traits Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧩',
            'label' => 'Traits',
            'value' => !empty($event['traits']) ? count($event['traits']) . ' traits' : '0 traits',
            'type' => 'simple'
        ])

        {{-- Properties Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🏗️',
            'label' => 'Properties',
            'value' => !empty($event['properties']) ? count($event['properties']) . ' properties' : '0 properties',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Traits --}}
        @if (!empty($event['traits']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🧩</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Traits ({{ count($event['traits']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($event['traits'] as $trait)
                            <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200 font-medium">
                                {{ class_basename($trait) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Properties --}}
        @if (!empty($event['properties']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🏗️</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Constructor Properties ({{ count($event['properties']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach ($event['properties'] as $property)
                            <div class="text-xs bg-white dark:bg-gray-800 rounded p-2 border">
                                @if (isset($property['type']))
                                    <span class="text-purple-600 dark:text-purple-400">{{ $property['type'] }}</span>
                                @endif
                                <code class="text-blue-600 dark:text-blue-400">${{ $property['name'] ?? 'property' }}</code>
                                @if (isset($property['visibility']))
                                    <span class="ml-2 text-xs px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                        {{ $property['visibility'] }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Broadcast Channels --}}
        @if (!empty($event['channels']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">📡</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Broadcast Channels ({{ count($event['channels']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($event['channels'] as $channel)
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 font-medium">
                                {{ $channel }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Event Listeners --}}
        @if (!empty($event['listeners']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">👂</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Event Listeners ({{ count($event['listeners']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($event['listeners'] as $listener)
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 font-medium">
                                {{ class_basename($listener) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Interactive Sections --}}
    <div class="mt-6 space-y-4">
        {{-- Methods Section --}}
        @include('atlas::exports.partials.common.collapsible-methods', [
            'methods' => $event['methods'] ?? [],
            'componentId' => 'event-' . md5($event['class']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])

        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $event['flow'] ?? [],
            'type' => 'event'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $event['class'],
        'file' => $event['file']
    ])
</div>
