<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-indigo-800 mb-1">🛡️ {{ $middleware['class'] }}</h2>
    <p class="text-sm text-gray-600 mb-3">
        @if (!empty($middleware['alias']))
            Alias: <code class="bg-gray-100 px-2 py-1 rounded">{{ $middleware['alias'] }}</code>
        @endif
        @if (!empty($middleware['namespace']))
            <br>Namespace: <code class="bg-gray-100 px-2 py-1 rounded">{{ $middleware['namespace'] }}</code>
        @endif
    </p>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Middleware Type --}}
        <div>
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Type</h3>
            <div class="bg-gray-100 rounded p-2 text-sm">
                <span class="px-2 py-1 text-xs rounded font-medium
                    @if($middleware['type'] === 'global') bg-red-100 text-red-800
                    @elseif($middleware['type'] === 'route') bg-blue-100 text-blue-800
                    @elseif($middleware['type'] === 'group') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($middleware['type']) }} Middleware
                </span>
            </div>
        </div>

        {{-- Priority --}}
        @if (!empty($middleware['priority']))
            <div>
                <h3 class="font-semibold text-sm text-gray-500 mb-1">Priority</h3>
                <div class="bg-gray-100 rounded p-2 text-sm">
                    <span class="font-medium">{{ $middleware['priority'] }}</span>
                    <span class="text-xs text-gray-600 ml-2">(lower = higher priority)</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Handle Method --}}
    @if (!empty($middleware['handle_method']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Handle Method</h3>
            <div class="bg-gray-100 rounded p-3">
                <code class="text-sm">{{ $middleware['handle_method']['signature'] }}</code>
                @if (!empty($middleware['handle_method']['parameters']))
                    <div class="mt-2">
                        <div class="text-xs text-gray-600 mb-1">Parameters:</div>
                        @foreach ($middleware['handle_method']['parameters'] as $param)
                            <div class="ml-2">
                                <code class="text-xs">{{ $param['type'] }} {{ $param['name'] }}</code>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Terminate Method --}}
    @if (!empty($middleware['terminate_method']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Terminate Method</h3>
            <div class="bg-blue-50 rounded p-3 border border-blue-100">
                <code class="text-sm text-blue-800">{{ $middleware['terminate_method']['signature'] }}</code>
                <div class="mt-2 text-xs text-blue-600">
                    This middleware has a terminate method that runs after the response is sent.
                </div>
            </div>
        </div>
    @endif

    {{-- Used On Routes --}}
    @if (!empty($middleware['used_on_routes']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Used On Routes ({{ count($middleware['used_on_routes']) }})</h3>
            <div class="max-h-64 overflow-y-auto">
                <table class="w-full text-sm border rounded overflow-hidden">
                    <thead class="bg-gray-200 text-left sticky top-0">
                        <tr>
                            <th class="p-2">Method</th>
                            <th class="p-2">URI</th>
                            <th class="p-2">Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($middleware['used_on_routes'] as $route)
                            <tr class="border-t">
                                <td class="p-2">
                                    @foreach ($route['methods'] as $method)
                                        <span class="px-1 py-0.5 text-xs rounded font-semibold mr-1
                                            @if($method === 'GET') bg-green-100 text-green-800
                                            @elseif($method === 'POST') bg-blue-100 text-blue-800
                                            @elseif($method === 'PUT') bg-yellow-100 text-yellow-800
                                            @elseif($method === 'DELETE') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $method }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="p-2"><code class="text-xs">{{ $route['uri'] }}</code></td>
                                <td class="p-2">{{ $route['name'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Route Groups --}}
    @if (!empty($middleware['used_on_groups']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Used On Route Groups</h3>
            <div class="space-y-2">
                @foreach ($middleware['used_on_groups'] as $group)
                    <div class="bg-green-50 rounded p-2 border border-green-100">
                        <div class="text-sm font-medium text-green-800">
                            @if (!empty($group['prefix']))
                                Prefix: <code>/{{ $group['prefix'] }}</code>
                            @endif
                            @if (!empty($group['namespace']))
                                Namespace: <code>{{ $group['namespace'] }}</code>
                            @endif
                        </div>
                        @if (!empty($group['routes_count']))
                            <div class="text-xs text-green-600 mt-1">
                                Applies to {{ $group['routes_count'] }} routes
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Parameters --}}
    @if (!empty($middleware['parameters']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Common Parameters</h3>
            <div class="bg-yellow-50 rounded p-3 border border-yellow-100">
                <div class="text-sm text-yellow-800">
                    <div class="font-medium mb-2">Supported Parameters:</div>
                    @foreach ($middleware['parameters'] as $param)
                        <div class="mb-1">
                            <code class="text-yellow-700">{{ $param['name'] }}</code>
                            @if (!empty($param['description']))
                                <span class="text-xs text-yellow-600 ml-2">{{ $param['description'] }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Dependencies --}}
    @if (!empty($middleware['dependencies']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Dependencies</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($middleware['dependencies'] as $dependency)
                    <div class="bg-gray-100 rounded p-2 text-sm">
                        <code class="text-gray-800">{{ $dependency }}</code>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>