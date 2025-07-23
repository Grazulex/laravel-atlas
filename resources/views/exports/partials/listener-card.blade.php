<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-indigo-800 mb-1">👂 {{ $listener['class'] }}</h2>
    <p class="text-sm text-gray-600 mb-3">
        @if (!empty($listener['namespace']))
            Namespace: <code class="bg-gray-100 px-2 py-1 rounded">{{ $listener['namespace'] }}</code>
        @endif
        @if (!empty($listener['should_queue']))
            <br><span class="inline-flex items-center px-2 py-1 text-xs bg-green-100 text-green-800 rounded">
                📬 Queued Listener
            </span>
        @endif
    </p>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Listened Events --}}
        @if (!empty($listener['events']))
            <div>
                <h3 class="font-semibold text-sm text-gray-500 mb-1">Listens To</h3>
                <div class="space-y-2">
                    @foreach ($listener['events'] as $event)
                        <div class="bg-blue-50 rounded p-2 border border-blue-100">
                            <code class="text-blue-800 text-sm">{{ $event['class'] }}</code>
                            @if (!empty($event['method']))
                                <div class="text-xs text-blue-600 mt-1">
                                    Method: <code>{{ $event['method'] }}</code>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Queue Configuration --}}
        @if (!empty($listener['queue_config']))
            <div>
                <h3 class="font-semibold text-sm text-gray-500 mb-1">Queue Configuration</h3>
                <div class="bg-gray-100 rounded p-2 text-sm space-y-1">
                    <div class="flex justify-between">
                        <span>Queue:</span>
                        <code>{{ $listener['queue_config']['queue'] ?? 'default' }}</code>
                    </div>
                    <div class="flex justify-between">
                        <span>Connection:</span>
                        <code>{{ $listener['queue_config']['connection'] ?? 'default' }}</code>
                    </div>
                    <div class="flex justify-between">
                        <span>Delay:</span>
                        <code>{{ $listener['queue_config']['delay'] ?? '0' }}s</code>
                    </div>
                    @if (!empty($listener['queue_config']['tries']))
                        <div class="flex justify-between">
                            <span>Tries:</span>
                            <code>{{ $listener['queue_config']['tries'] }}</code>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Handle Method --}}
    @if (!empty($listener['handle_method']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Handle Method</h3>
            <div class="bg-gray-100 rounded p-3">
                <code class="text-sm">{{ $listener['handle_method']['signature'] }}</code>
                @if (!empty($listener['handle_method']['parameters']))
                    <div class="mt-2">
                        <div class="text-xs text-gray-600 mb-1">Parameters:</div>
                        @foreach ($listener['handle_method']['parameters'] as $param)
                            <div class="ml-2">
                                <code class="text-xs">{{ $param['type'] }} {{ $param['name'] }}</code>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Failed Method --}}
    @if (!empty($listener['failed_method']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Failed Handler</h3>
            <div class="bg-red-50 rounded p-3 border border-red-100">
                <code class="text-sm text-red-800">{{ $listener['failed_method']['signature'] }}</code>
                @if (!empty($listener['failed_method']['parameters']))
                    <div class="mt-2">
                        <div class="text-xs text-red-600 mb-1">Parameters:</div>
                        @foreach ($listener['failed_method']['parameters'] as $param)
                            <div class="ml-2">
                                <code class="text-xs text-red-700">{{ $param['type'] }} {{ $param['name'] }}</code>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Dependencies --}}
    @if (!empty($listener['dependencies']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Dependencies</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($listener['dependencies'] as $dependency)
                    <div class="bg-gray-100 rounded p-2 text-sm">
                        <code class="text-gray-800">{{ $dependency }}</code>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Event Subscriber --}}
    @if (!empty($listener['is_subscriber']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Event Subscriber</h3>
            <div class="bg-purple-50 rounded p-3 border border-purple-100">
                <div class="text-sm text-purple-800">
                    <span class="font-medium">📋 Event Subscriber</span>
                    <div class="mt-2 text-xs">
                        This listener implements the Event Subscriber pattern and can handle multiple events.
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>