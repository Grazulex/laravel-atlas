<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-lg font-semibold text-indigo-800 mb-2">🛣️ {{ $route['uri'] }}</h2>

    <div class="text-sm text-gray-600 mb-2">
        <span class="font-semibold">Methods:</span>
        <code>{{ implode(', ', array_diff($route['methods'], ['HEAD'])) }}</code>
    </div>

    @if (!empty($route['name']))
        <div class="text-sm text-gray-600 mb-2">
            <span class="font-semibold">Name:</span>
            <code>{{ $route['name'] }}</code>
        </div>
    @endif

    @if (!empty($route['controller']))
        <div class="text-sm text-gray-600 mb-2">
            <span class="font-semibold">Controller:</span>
            <code>{{ $route['controller'] }}@{{ $route['uses'] }}</code>
        </div>
    @else
        <div class="text-sm text-gray-600 mb-2">
            <span class="font-semibold">Action:</span>
            <span class="italic text-gray-500">closure</span>
        </div>
    @endif

    @if (!empty($route['middleware']))
        <div class="text-sm text-gray-600 mb-2">
            <span class="font-semibold">Middleware:</span>
            <code>{{ implode(', ', $route['middleware']) }}</code>
        </div>
    @endif
</div>
