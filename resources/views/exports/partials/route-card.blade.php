<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-sm font-semibold text-indigo-700 truncate max-w-[80%]">
            🛣️ <code>{{ $route['uri'] }}</code>
        </h2>
        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
            {{ strtoupper($route['type']) }}
        </span>
    </div>

    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm text-gray-700">

        {{-- Name --}}
        @if (!empty($route['name']))
            <div>
                <span class="block text-xs text-gray-400 font-semibold">🔖 Name</span>
                <code>{{ $route['name'] }}</code>
            </div>
        @endif

        {{-- Controller / Closure --}}
        <div>
            <span class="block text-xs text-gray-400 font-semibold">⚙️ Handler</span>
            @if ($route['is_closure'])
                <span class="italic text-gray-500 text-sm">Closure</span>
            @elseif (!empty($route['controller']) && !empty($route['uses']))
                <code class="text-sm">{{ class_basename($route['controller']) }}|{{ $route['uses'] }}</code>
            @elseif (!empty($route['controller']))
                <code class="text-sm">{{ class_basename($route['controller']) }}</code>
            @else
                <span class="text-xs text-red-500">Unknown</span>
            @endif
        </div>

        {{-- Methods --}}
        <div>
            <span class="block text-xs text-gray-400 font-semibold">🧭 Methods</span>
            <div class="flex flex-wrap gap-1">
                @foreach ($route['methods'] as $method)
                    <span class="text-xs font-mono bg-gray-200 px-2 py-0.5 rounded text-gray-800">
                        {{ $method }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Middleware --}}
        @if (!empty($route['middleware']))
            <div class="sm:col-span-2 md:col-span-1">
                <span class="block text-xs text-gray-400 font-semibold">🛡️ Middleware</span>
                <ul class="list-disc ml-5 text-xs text-gray-600 space-y-0.5">
                    @foreach ($route['middleware'] as $mw)
                        <li><code>{{ $mw }}</code></li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Prefix --}}
        @if (!empty($route['prefix']))
            <div>
                <span class="block text-xs text-gray-400 font-semibold">📁 Prefix</span>
                <code>{{ $route['prefix'] }}</code>
            </div>
        @endif

        {{-- Domain --}}
        @if (!empty($route['domain']))
            <div>
                <span class="block text-xs text-gray-400 font-semibold">🌐 Domain</span>
                <code>{{ $route['domain'] }}</code>
            </div>
        @endif
    </div>

    {{-- Flow --}}
    @if (!empty($route['flow']['jobs']) || !empty($route['flow']['events']) || !empty($route['flow']['dependencies']))
        <div class="grid sm:grid-cols-2 gap-4">
            {{-- Jobs --}}
            @if (!empty($route['flow']['jobs']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📬 Jobs</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($route['flow']['jobs'] as $job)
                            <li>
                                <code>{{ $job['class'] }}</code>
                                @if ($job['async']) <span class="text-[10px] text-purple-500">(async)</span> @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Events --}}
            @if (!empty($route['flow']['events']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔔 Events</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($route['flow']['events'] as $event)
                            <li><code>{{ $event['class'] }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Dependencies --}}
            @if (!empty($route['flow']['dependencies']))
                <div class="sm:col-span-2">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🧩 Dependencies</span>
                    <div class="text-xs bg-gray-50 rounded p-2 text-gray-800 leading-tight">
                        {{ implode(', ', $route['flow']['dependencies']) }}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>