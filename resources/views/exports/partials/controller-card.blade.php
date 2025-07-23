<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-indigo-800 mb-1">🎮 {{ $controller['class'] }}</h2>
    <p class="text-sm text-gray-600 mb-3">
        @if (!empty($controller['namespace']))
            Namespace: <code class="bg-gray-100 px-2 py-1 rounded">{{ $controller['namespace'] }}</code>
        @endif
        @if (!empty($controller['extends']))
            <br>Extends: <code class="bg-gray-100 px-2 py-1 rounded">{{ $controller['extends'] }}</code>
        @endif
    </p>

    {{-- Controller Middleware --}}
    @if (!empty($controller['middleware']))
        <div class="mb-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Controller Middleware</h3>
            <div class="flex flex-wrap gap-1">
                @foreach ($controller['middleware'] as $middleware)
                    <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 rounded font-medium">
                        {{ is_string($middleware) ? $middleware : $middleware['name'] }}
                        @if (is_array($middleware) && !empty($middleware['options']))
                            <span class="text-indigo-600">({{ implode(', ', $middleware['options']) }})</span>
                        @endif
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions/Methods --}}
    @if (!empty($controller['actions']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Actions ({{ count($controller['actions']) }})</h3>
            <div class="space-y-3">
                @foreach ($controller['actions'] as $action)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-800">{{ $action['name'] }}</h4>
                            <div class="flex gap-1">
                                @if (!empty($action['routes']))
                                    @foreach ($action['routes'] as $route)
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
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        @if (!empty($action['parameters']))
                            <div class="mb-2">
                                <span class="text-xs font-medium text-gray-500">Parameters:</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach ($action['parameters'] as $param)
                                        <code class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">
                                            {{ $param['type'] ?? '' }} {{ $param['name'] }}
                                        </code>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (!empty($action['routes']))
                            <div class="mt-2">
                                <span class="text-xs font-medium text-gray-500">Routes:</span>
                                @foreach ($action['routes'] as $route)
                                    <div class="mt-1">
                                        <code class="text-xs bg-blue-50 text-blue-800 px-2 py-1 rounded">
                                            {{ $route['uri'] }}
                                        </code>
                                        @if (!empty($route['name']))
                                            <span class="text-xs text-gray-600 ml-2">as {{ $route['name'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if (!empty($action['middleware']))
                            <div class="mt-2">
                                <span class="text-xs font-medium text-gray-500">Action Middleware:</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach ($action['middleware'] as $middleware)
                                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">
                                            {{ $middleware }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Resource Controller --}}
    @if (!empty($controller['is_resource']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Resource Controller</h3>
            <div class="bg-green-50 rounded p-3 border border-green-100">
                <div class="text-sm text-green-800">
                    <span class="font-medium">✅ Resource Controller</span>
                    @if (!empty($controller['resource_methods']))
                        <div class="mt-2">
                            <span class="text-xs">Methods:</span>
                            @foreach ($controller['resource_methods'] as $method)
                                <code class="ml-1 px-1 bg-green-100 text-green-700 rounded text-xs">{{ $method }}</code>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Dependencies --}}
    @if (!empty($controller['dependencies']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Dependencies</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($controller['dependencies'] as $dependency)
                    <div class="bg-gray-100 rounded p-2 text-sm">
                        <code class="text-gray-800">{{ $dependency['class'] }}</code>
                        @if (!empty($dependency['type']))
                            <span class="text-xs text-gray-600 ml-2">({{ $dependency['type'] }})</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>