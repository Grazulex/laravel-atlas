<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-indigo-800 mb-1">📬 {{ $job['class'] }}</h2>
    <p class="text-sm text-gray-600 mb-3">
        @if (!empty($job['queue']))
            Queue: <code class="bg-gray-100 px-2 py-1 rounded">{{ $job['queue'] }}</code>
        @endif
        @if (!empty($job['connection']))
            <br>Connection: <code class="bg-gray-100 px-2 py-1 rounded">{{ $job['connection'] }}</code>
        @endif
        @if (!empty($job['delay']))
            <br>Delay: <code class="bg-gray-100 px-2 py-1 rounded">{{ $job['delay'] }}s</code>
        @endif
    </p>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Job Properties --}}
        <div>
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Properties</h3>
            <div class="bg-gray-100 rounded p-2 text-sm space-y-1">
                <div class="flex justify-between">
                    <span>Queue:</span>
                    <code>{{ $job['queue'] ?? 'default' }}</code>
                </div>
                <div class="flex justify-between">
                    <span>Timeout:</span>
                    <code>{{ $job['timeout'] ?? '60' }}s</code>
                </div>
                <div class="flex justify-between">
                    <span>Tries:</span>
                    <code>{{ $job['tries'] ?? '1' }}</code>
                </div>
                <div class="flex justify-between">
                    <span>Backoff:</span>
                    <code>{{ $job['backoff'] ?? '0' }}s</code>
                </div>
            </div>
        </div>

        {{-- Job Status --}}
        <div>
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Status</h3>
            <div class="space-y-2">
                <div class="flex items-center">
                    <span class="w-3 h-3 rounded-full mr-2 {{ $job['should_queue'] ?? true ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    <span class="text-sm">{{ $job['should_queue'] ?? true ? 'Queued' : 'Synchronous' }}</span>
                </div>
                @if (!empty($job['failed_at']))
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full mr-2 bg-red-500"></span>
                        <span class="text-sm text-red-600">Failed Jobs Detected</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Handle Method --}}
    @if (!empty($job['handle_method']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Handle Method</h3>
            <div class="bg-gray-100 rounded p-2 text-sm">
                <code>{{ $job['handle_method']['signature'] }}</code>
                @if (!empty($job['handle_method']['parameters']))
                    <div class="mt-2">
                        <span class="text-xs text-gray-600">Parameters:</span>
                        @foreach ($job['handle_method']['parameters'] as $param)
                            <code class="ml-2">{{ $param }}</code>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Failed Method --}}
    @if (!empty($job['failed_method']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Failed Handler</h3>
            <div class="bg-red-50 rounded p-2 text-sm">
                <code class="text-red-800">{{ $job['failed_method']['signature'] }}</code>
                @if (!empty($job['failed_method']['parameters']))
                    <div class="mt-2">
                        <span class="text-xs text-red-600">Parameters:</span>
                        @foreach ($job['failed_method']['parameters'] as $param)
                            <code class="ml-2 text-red-700">{{ $param }}</code>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Dependencies --}}
    @if (!empty($job['dependencies']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Dependencies</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($job['dependencies'] as $dependency)
                    <div class="bg-gray-100 rounded p-2 text-sm">
                        <code class="text-gray-800">{{ $dependency }}</code>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Dispatch Analysis --}}
    @if (!empty($job['dispatch_locations']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Dispatched From</h3>
            <table class="w-full text-sm border rounded overflow-hidden">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="p-2">Class</th>
                        <th class="p-2">Method</th>
                        <th class="p-2">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($job['dispatch_locations'] as $location)
                        <tr class="border-t">
                            <td class="p-2"><code>{{ $location['class'] }}</code></td>
                            <td class="p-2">{{ $location['method'] }}</td>
                            <td class="p-2">
                                <span class="px-2 py-1 text-xs rounded font-medium
                                    @if($location['type'] === 'dispatch') bg-blue-100 text-blue-800
                                    @elseif($location['type'] === 'dispatchNow') bg-green-100 text-green-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $location['type'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>