<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-indigo-800 mb-1">🔧 {{ $service['class'] }}</h2>
    <p class="text-sm text-gray-600 mb-3">
        @if (!empty($service['namespace']))
            Namespace: <code class="bg-gray-100 px-2 py-1 rounded">{{ $service['namespace'] }}</code>
        @endif
        @if (!empty($service['file']))
            <br>File: <code class="bg-gray-100 px-2 py-1 rounded">{{ $service['file'] }}</code>
        @endif
    </p>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Interfaces --}}
        @if (!empty($service['interfaces']))
            <div>
                <h3 class="font-semibold text-sm text-gray-500 mb-1">Implements</h3>
                <div class="bg-gray-100 rounded p-2 text-sm text-gray-800">
                    @foreach ($service['interfaces'] as $interface)
                        <code>{{ $interface }}</code>@if (!$loop->last)<br>@endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Traits --}}
        @if (!empty($service['traits']))
            <div>
                <h3 class="font-semibold text-sm text-gray-500 mb-1">Uses Traits</h3>
                <div class="bg-gray-100 rounded p-2 text-sm text-gray-800">
                    @foreach ($service['traits'] as $trait)
                        <code>{{ $trait }}</code>@if (!$loop->last)<br>@endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Methods --}}
    @if (!empty($service['methods']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Methods ({{ count($service['methods']) }})</h3>
            <table class="w-full text-sm border rounded overflow-hidden">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="p-2">Method</th>
                        <th class="p-2">Visibility</th>
                        <th class="p-2">Parameters</th>
                        <th class="p-2">Return Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($service['methods'] as $method)
                        <tr class="border-t">
                            <td class="p-2"><code>{{ $method['name'] }}</code></td>
                            <td class="p-2">
                                <span class="px-2 py-1 text-xs rounded font-medium
                                    @if($method['visibility'] === 'public') bg-green-100 text-green-800
                                    @elseif($method['visibility'] === 'protected') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $method['visibility'] }}
                                </span>
                            </td>
                            <td class="p-2">{{ count($method['parameters'] ?? []) }}</td>
                            <td class="p-2">{{ $method['return_type'] ?? 'mixed' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Dependencies --}}
    @if (!empty($service['dependencies']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Dependencies</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($service['dependencies'] as $dependency)
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

    {{-- Service Container Bindings --}}
    @if (!empty($service['container_bindings']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">Container Bindings</h3>
            <table class="w-full text-sm border rounded overflow-hidden">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="p-2">Abstract</th>
                        <th class="p-2">Concrete</th>
                        <th class="p-2">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($service['container_bindings'] as $binding)
                        <tr class="border-t">
                            <td class="p-2"><code>{{ $binding['abstract'] }}</code></td>
                            <td class="p-2"><code>{{ $binding['concrete'] }}</code></td>
                            <td class="p-2">
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                    {{ $binding['type'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>