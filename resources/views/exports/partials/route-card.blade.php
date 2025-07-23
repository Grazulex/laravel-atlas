<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-lg font-semibold text-indigo-800">
            🛣️ {{ $route['uri'] }}
        </h2>
        <span class="text-xs uppercase px-2 py-1 rounded bg-indigo-100 text-indigo-800">
            {{ strtoupper($route['type']) }}
        </span>
    </div>

    @if ($route['is_closure'])
        <p class="text-sm text-gray-600 italic mb-2">🔒 Closure-based route</p>
    @else
        <div class="text-sm text-gray-600 mb-2">
            <span class="font-semibold">Controller:</span>
            <code>{{ $route['controller'] }}@{{ $route['uses'] }}</code>
        </div>
    @endif

    @if (!empty($route['name']))
        <div class="text-sm text-gray-600 mb-2">
            <span class="font-semibold">Name:</span>
            <code>{{ $route['name'] }}</code>
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-4 mt-4">
        {{-- Methods --}}
        <div>
            <h4 class="text-xs text-gray-400 mb-1 font-semibold">HTTP Methods</h4>
            <div class="flex flex-wrap gap-2">
                @foreach ($route['methods'] as $method)
                    <span class="bg-gray-200 text-xs px-2 py-1 rounded">{{ $method }}</span>
                @endforeach
            </div>
        </div>

        {{-- Middleware --}}
        @if (!empty($route['middleware']))
            <div>
                <h4 class="text-xs text-gray-400 mb-1 font-semibold">Middleware</h4>
                <ul class="text-sm bg-gray-100 rounded p-2 space-y-1">
                    @foreach ($route['middleware'] as $mw)
                        <li><code>{{ $mw }}</code></li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Prefix --}}
        @if (!empty($route['prefix']))
            <div>
                <h4 class="text-xs text-gray-400 mb-1 font-semibold">Prefix</h4>
                <div class="text-sm bg-gray-100 rounded p-2 text-gray-800">
                    {{ $route['prefix'] }}
                </div>
            </div>
        @endif

        {{-- Domain --}}
        @if (!empty($route['domain']))
            <div>
                <h4 class="text-xs text-gray-400 mb-1 font-semibold">Domain</h4>
                <div class="text-sm bg-gray-100 rounded p-2 text-gray-800">
                    {{ $route['domain'] }}
                </div>
            </div>
        @endif
    </div>
    {{-- Flow (jobs, events, dependencies) --}}
    @if (!empty($route['flow']['jobs']) || !empty($route['flow']['events']) || !empty($route['flow']['dependencies']))
        <div class="mt-6">
            <h3 class="font-semibold text-sm text-gray-500 mb-1">📦 Flow</h3>

            @if (!empty($route['flow']['jobs']))
                <div class="mb-2">
                    <h4 class="text-xs text-gray-400 font-semibold">📬 Jobs</h4>
                    <ul class="text-sm bg-indigo-50 rounded p-2 space-y-1">
                        @foreach ($route['flow']['jobs'] as $job)
                            <li>
                                <code>{{ $job['class'] }}</code>
                                @if ($job['async']) <span class="text-xs text-gray-500">(async)</span> @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($route['flow']['events']))
                <div class="mb-2">
                    <h4 class="text-xs text-gray-400 font-semibold">🔔 Events</h4>
                    <ul class="text-sm bg-blue-50 rounded p-2 space-y-1">
                        @foreach ($route['flow']['events'] as $event)
                            <li><code>{{ $event['class'] }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($route['flow']['dependencies']))
                <div class="mb-2">
                    <h4 class="text-xs text-gray-400 font-semibold">🧩 Dependencies</h4>
                    <div class="text-sm bg-gray-100 rounded p-2 text-gray-800">
                        {{ implode(', ', $route['flow']['dependencies']) }}
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
