<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:border-gray-700">
    {{-- Header --}}
    <x-atlas::card-header icon="📢" :title="class_basename($notification['class'])" />

    {{-- Channels --}}
    @if (!empty($notification['channels']))
        <x-atlas::property-item label="Channels">
            <div class="flex flex-wrap gap-1">
                @foreach ($notification['channels'] as $channel)
                    <span class="text-xs bg-indigo-100 dark:bg-indigo-900 dark:text-white text-indigo-800 font-mono px-2 py-0.5 rounded">{{ $channel }}</span>
                @endforeach
            </div>
        </x-atlas::property-item>
    @endif

    {{-- Methods --}}
    @if (!empty($notification['methods']))
        <x-atlas::property-item label="Defined methods">
            <ul class="text-xs space-y-0.5">
                @foreach ($notification['methods'] as $method)
                    <li><code>{{ $method }}</code></li>
                @endforeach
            </ul>
        </x-atlas::property-item>
    @endif

    {{-- Queues --}}
    @if (!empty($notification['queues']))
        <x-atlas::property-item label="Queue per channel">
            <ul class="text-xs space-y-0.5">
                @foreach ($notification['queues'] as $channel => $queue)
                    <li><span class="font-mono text-indigo-600 dark:text-indigo-300">{{ $channel }}</span> → <code>{{ $queue }}</code></li>
                @endforeach
            </ul>
        </x-atlas::property-item>
    @endif

    {{-- Flow --}}
    @if (!empty($notification['flow']))
        <x-atlas::flow-section :flow="$notification['flow']" />
    @endif
</div>
