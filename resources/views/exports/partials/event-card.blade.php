<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header avec nom de classe --}}
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-2">
            <span class="text-lg">⚡</span>
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                {{ class_basename($event['class']) }}
            </h2>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200">
                Event
            </span>
            @if ($event['broadcastable'])
                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200">
                    📡 Broadcastable
                </span>
            @endif
        </div>
    </div>

    {{-- Nom complet de classe --}}
    <div class="mb-4 p-2 bg-gray-50 dark:bg-gray-700 rounded border-l-4 border-purple-400">
        <code class="text-xs text-gray-600 dark:text-gray-300">{{ $event['class'] }}</code>
    </div>

    {{-- Section Traits --}}
    @if (!empty($event['traits']))
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                <span class="mr-1">🧩</span> Traits
            </h3>
            <div class="flex flex-wrap gap-1">
                @foreach ($event['traits'] as $trait)
                    <span class="text-xs bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded">
                        {{ $trait }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Section Properties --}}
    @if (!empty($event['properties']))
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                <span class="mr-1">🏗️</span> Constructor Properties
            </h3>
            <div class="space-y-1">
                @foreach ($event['properties'] as $property)
                    <div class="flex items-center justify-between text-xs bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded">
                        <span class="font-mono text-gray-800 dark:text-gray-200">
                            ${{ $property['name'] }}
                        </span>
                        <div class="flex items-center space-x-2">
                            <span class="text-indigo-600 dark:text-indigo-400 font-mono">
                                {{ $property['type'] }}
                            </span>
                            @if ($property['hasDefault'])
                                <span class="text-green-600 dark:text-green-400 text-xs">optional</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Section Broadcasting --}}
    @if (!empty($event['channels']))
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                <span class="mr-1">📡</span> Broadcast Channels
            </h3>
            <div class="flex flex-wrap gap-1">
                @foreach ($event['channels'] as $channel)
                    <span class="text-xs bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 px-2 py-1 rounded font-mono">
                        {{ $channel }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Section Listeners --}}
    @if (!empty($event['listeners']))
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                <span class="mr-1">👂</span> Event Listeners
            </h3>
            <div class="space-y-1">
                @foreach ($event['listeners'] as $listener)
                    <div class="text-xs bg-yellow-50 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-2 py-1 rounded font-mono">
                        {{ $listener }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $event['flow'] ?? [],
        'type' => 'event'
    ])
</div>
