<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '👂',
        'title' => $listener['name'],
        'badge' => 'Listener',
        'badgeColor' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300'
    ])

    {{-- Namespace --}}
    @include('atlas::exports.partials.common.property-item', [
        'icon' => '📦',
        'label' => 'Namespace',
        'value' => $listener['namespace'],
        'type' => 'code'
    ])

    {{-- Listened Events --}}
    @if (!empty($listener['events']))
        <div class="mb-3">
            <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                🎯 Listens to Events
            </h4>
            <div class="space-y-1">
                @foreach ($listener['events'] as $event)
                    <div class="text-xs bg-indigo-50 dark:bg-indigo-900/20 rounded p-2">
                        <span class="font-mono text-indigo-600 dark:text-indigo-400">{{ class_basename($event) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Constructor Dependencies --}}
    @if (!empty($listener['dependencies']))
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔗',
            'label' => 'Dependencies',
            'value' => implode(', ', array_map('class_basename', $listener['dependencies'])),
            'type' => 'code'
        ])
    @endif

    {{-- Handle Method --}}
    @if (!empty($listener['handle_method']))
        <div class="mb-3">
            <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                🔧 Handle Method
            </h4>
            <div class="text-xs bg-indigo-50 dark:bg-indigo-900/20 rounded p-2">
                <div class="font-mono">
                    <span class="text-purple-600 dark:text-purple-400">handle</span>
                    <span class="text-gray-500">(</span>
                    @foreach ($listener['handle_method']['parameters'] as $index => $param)
                        @if ($index > 0), @endif
                        @if ($param['type'])
                            <span class="text-gray-600 dark:text-gray-400">{{ class_basename($param['type']) }}</span>
                        @endif
                        <span class="text-blue-600 dark:text-blue-400">${{ $param['name'] }}</span>
                    @endforeach
                    <span class="text-gray-500">)</span>
                    @if ($listener['handle_method']['return_type'])
                        : <span class="text-green-600 dark:text-green-400">{{ class_basename($listener['handle_method']['return_type']) }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Should Queue --}}
    @if (isset($listener['should_queue']))
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⏰',
            'label' => 'Should Queue',
            'value' => $listener['should_queue'] ? 'Yes' : 'No',
            'type' => 'simple'
        ])
    @endif

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $listener['class'],
        'file' => $listener['file']
    ])
</div>
