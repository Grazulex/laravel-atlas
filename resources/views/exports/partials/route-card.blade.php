<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-indigo-800 mb-1">🛣️ {{ $route['method'] ?? 'GET' }} {{ $route['uri'] }}</h2>
    <p class="text-sm text-gray-600 mb-3">
        @if (!empty($route['name']))
            Name: <code class="bg-gray-100 px-2 py-1 rounded">{{ $route['name'] }}</code>
        @endif
        @if (!empty($route['controller']))
            <br>Controller: <code class="bg-gray-100 px-2 py-1 rounded">{{ $route['controller'] }}</code>
        @endif
        @if (!empty($route['action']))
            <br>Action: <code class="bg-gray-100 px-2 py-1 rounded">{{ $route['action'] }}</code>
        @endif
    </p>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- HTTP Methods --}}
        @if (!empty($route['methods']))
            <div>
                <h3 class="font-semibold text-sm text-gray-500 mb-1">HTTP Methods</h3>
                <div class="flex flex-wrap gap-1">
                    @foreach ($route['methods'] as $method)
                        <span class="px-2 py-1 text-xs rounded font-semibold
                            @if($method === 'GET') bg-green-100 text-green-800
                            @elseif($method === 'POST') bg-blue-100 text-blue-800
                            @elseif($method === 'PUT') bg-yellow-100 text-yellow-800
                            @elseif($method === 'DELETE') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $method }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Parameters --}}
        @if (!empty($route['parameters']))
            <div>
                <h3 class="font-semibold text-sm text-gray-500 mb-1">Parameters</h3>
                <div class="bg-gray-100 rounded p-2 text-sm text-gray-800">
                    @foreach ($route['parameters'] as $param)
                        <code>{{{ $param }}}</code>@if (!$loop->last), @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Middleware --}}
    @if (!empty($route['middleware']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Middleware</h3>
            <div class="flex flex-wrap gap-1">
                @foreach ($route['middleware'] as $middleware)
                    <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 rounded font-medium">
                        {{ $middleware }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Route Model Binding --}}
    @if (!empty($route['bindings']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Model Bindings</h3>
            <table class="w-full text-sm border rounded overflow-hidden">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="p-2">Parameter</th>
                        <th class="p-2">Model</th>
                        <th class="p-2">Key</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($route['bindings'] as $param => $binding)
                        <tr class="border-t">
                            <td class="p-2"><code>{{ $param }}</code></td>
                            <td class="p-2">{{ $binding['model'] }}</td>
                            <td class="p-2">{{ $binding['key'] ?? 'id' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Security Analysis --}}
    @if (!empty($route['security']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Security Analysis</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <span class="text-xs font-medium {{ $route['security']['protected'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $route['security']['protected'] ? '🔒 Protected' : '🔓 Public' }}
                    </span>
                </div>
                @if (!empty($route['security']['auth_methods']))
                    <div>
                        <span class="text-xs text-gray-600">Auth: {{ implode(', ', $route['security']['auth_methods']) }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>