<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-indigo-800 mb-1">🔔 {{ $event['class'] }}</h2>
    <p class="text-sm text-gray-600 mb-3">
        @if (!empty($event['namespace']))
            Namespace: <code class="bg-gray-100 px-2 py-1 rounded">{{ $event['namespace'] }}</code>
        @endif
        @if (!empty($event['should_broadcast']))
            <br><span class="inline-flex items-center px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">
                📡 Broadcast Event
            </span>
        @endif
    </p>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Event Properties --}}
        @if (!empty($event['properties']))
            <div>
                <h3 class="font-semibold text-sm text-gray-500 mb-1">Properties</h3>
                <div class="bg-gray-100 rounded p-2 text-sm text-gray-800">
                    @foreach ($event['properties'] as $property)
                        <div class="flex justify-between items-center mb-1">
                            <code>{{ $property['name'] }}</code>
                            <span class="text-xs text-gray-600">{{ $property['type'] ?? 'mixed' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Broadcasting --}}
        @if (!empty($event['broadcasting']))
            <div>
                <h3 class="font-semibold text-sm text-gray-500 mb-1">Broadcasting</h3>
                <div class="bg-purple-50 rounded p-2 text-sm">
                    @if (!empty($event['broadcasting']['channels']))
                        <div class="mb-2">
                            <span class="text-xs font-medium text-purple-700">Channels:</span>
                            @foreach ($event['broadcasting']['channels'] as $channel)
                                <div class="ml-2">
                                    <code class="text-purple-800">{{ $channel }}</code>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if (!empty($event['broadcasting']['as']))
                        <div>
                            <span class="text-xs font-medium text-purple-700">As:</span>
                            <code class="text-purple-800">{{ $event['broadcasting']['as'] }}</code>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Listeners --}}
    @if (!empty($event['listeners']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Event Listeners ({{ count($event['listeners']) }})</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($event['listeners'] as $listener)
                    <div class="bg-blue-50 rounded p-3 border border-blue-100">
                        <div class="font-medium text-blue-800">{{ $listener['class'] }}</div>
                        @if (!empty($listener['method']))
                            <div class="text-xs text-blue-600 mt-1">Method: {{ $listener['method'] }}</div>
                        @endif
                        @if (!empty($listener['should_queue']))
                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Queued</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Event Dispatch Locations --}}
    @if (!empty($event['dispatch_locations']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Dispatched From</h3>
            <table class="w-full text-sm border rounded overflow-hidden">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="p-2">Class</th>
                        <th class="p-2">Method</th>
                        <th class="p-2">Line</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($event['dispatch_locations'] as $location)
                        <tr class="border-t">
                            <td class="p-2"><code>{{ $location['class'] }}</code></td>
                            <td class="p-2">{{ $location['method'] }}</td>
                            <td class="p-2">{{ $location['line'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Constructor Parameters --}}
    @if (!empty($event['constructor_parameters']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Constructor Parameters</h3>
            <table class="w-full text-sm border rounded overflow-hidden">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="p-2">Parameter</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Required</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($event['constructor_parameters'] as $param)
                        <tr class="border-t">
                            <td class="p-2"><code>{{ $param['name'] }}</code></td>
                            <td class="p-2">{{ $param['type'] ?? 'mixed' }}</td>
                            <td class="p-2">
                                <span class="px-2 py-1 text-xs rounded {{ $param['required'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $param['required'] ? 'Required' : 'Optional' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>