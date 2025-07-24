<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
{{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '📬',
        'title' => class_basename($item['class']),
        'badge' => 'Notification',
        'badgeColor' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200',
        'namespace' => $item['namespace'] ?? null,
        'class' => $item['class']
    ])

    {{-- Description --}}
    @if (!empty($item['description']))
        <div class="mb-4">
            <p class="text-xs text-gray-600 dark:text-gray-300 italic bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                {{ $item['description'] }}
            </p>
        </div>
    @endif

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Channels Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📡',
            'label' => 'Channels',
            'value' => !empty($item['channels']) ? count($item['channels']) . ' channels' : '0 channels',
            'type' => 'simple'
        ])

        {{-- Methods Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Methods',
            'value' => !empty($item['methods']) ? count($item['methods']) . ' methods' : '0 methods',
            'type' => 'simple'
        ])

        {{-- Via Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🚀',
            'label' => 'Delivery Via',
            'value' => !empty($item['via']) ? count($item['via']) . ' methods' : '0 methods',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Channels --}}
        @if (!empty($item['channels']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">📡</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Notification Channels ({{ count($item['channels']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($item['channels'] as $channel)
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 font-medium">
                                {{ $channel }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Via Methods --}}
        @if (!empty($item['via']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🚀</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Delivery Via ({{ count($item['via']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($item['via'] as $via)
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 font-medium">
                                {{ $via }}
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
            'methods' => $item['methods'] ?? [],
            'componentId' => 'notification-' . md5($item['class']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])

        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $item['flow'] ?? [],
            'type' => 'notification'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $item['class'],
        'file' => $item['file'] ?? 'N/A'
    ])
</div>
        'file' => $item['file']
    ])
</div>