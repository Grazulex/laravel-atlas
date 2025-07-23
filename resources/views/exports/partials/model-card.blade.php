<div class="bg-white mt-4 rounded-lg shadow-sm p-4 mb-4 border border-gray-200">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-sm font-semibold text-indigo-700 truncate max-w-[80%]">
            🧱 {{ class_basename($model['class']) }}
        </h2>
        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
            <code>{{ $model['table'] }}</code>
        </span>
    </div>

    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm text-gray-700">
        <div>
            <span class="block text-xs text-gray-400 font-semibold mb-1">🆔 Primary key</span>
            <code>{{ $model['primary_key'] }}</code>
        </div>

        {{-- Fillable --}}
        <div>
            <span class="block text-xs text-gray-400 font-semibold mb-1">📝 Fillable</span>
            <div class="text-xs bg-gray-50 rounded p-2 text-gray-800 leading-tight">
                {{ implode(', ', $model['fillable']) }}
            </div>
        </div>

        {{-- Guarded --}}
        @if (!empty($model['guarded']))
            <div>
                <span class="block text-xs text-gray-400 font-semibold mb-1">⛔ Guarded</span>
                <div class="text-xs bg-gray-50 rounded p-2 text-gray-800 leading-tight">
                    {{ implode(', ', $model['guarded']) }}
                </div>
            </div>
        @endif

        {{-- Casts --}}
        @if (!empty($model['casts']))
            <div class="sm:col-span-2 md:col-span-1">
                <span class="block text-xs text-gray-400 font-semibold mb-1">🔣 Casts</span>
                <table class="w-full text-xs border rounded overflow-hidden">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="p-1">Field</th>
                            <th class="p-1">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($model['casts'] as $field => $type)
                            <tr class="border-t">
                                <td class="p-1"><code>{{ $field }}</code></td>
                                <td class="p-1 text-gray-600">{{ $type }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Boot Hooks --}}
        @if (!empty($model['booted_hooks']))
            <div>
                <span class="block text-xs text-gray-400 font-semibold mb-1">🧷 Boot Hooks</span>
                <div class="text-xs bg-gray-50 rounded p-2 text-gray-800 leading-tight">
                    {{ implode(', ', $model['booted_hooks']) }}
                </div>
            </div>
        @endif
    </div>

    {{-- Relations --}}
    @if (!empty($model['relations']))
        <div>
            <h3 class="text-xs text-gray-400 font-semibold mb-1">🔗 Relations</h3>
            <table class="w-full text-xs border rounded overflow-hidden">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-1">Name</th>
                        <th class="p-1">Type</th>
                        <th class="p-1">Target</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($model['relations'] as $name => $rel)
                        <tr class="border-t">
                            <td class="p-1"><code>{{ $name }}</code></td>
                            <td class="p-1">{{ $rel['type'] }}</td>
                            <td class="p-1"><code>{{ class_basename($rel['related']) }}</code></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Scopes --}}
    @if (!empty($model['scopes']))
        <div>
            <h3 class="text-xs text-gray-400 font-semibold mb-1">🔍 Scopes</h3>
            <ul class="text-xs space-y-0.5 bg-gray-50 rounded p-2 text-gray-800">
                @foreach ($model['scopes'] as $scope)
                    <li>
                        <code>{{ $scope['name'] }}({{ implode(', ', $scope['parameters']) }})</code>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Flow --}}
    @if (!empty($model['flow']))
        <div class="grid sm:grid-cols-2 gap-4">
            {{-- 📬 Jobs --}}
            @if (!empty($model['flow']['jobs']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📬 Jobs</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($model['flow']['jobs'] as $job)
                            <li>
                                <code>{{ class_basename($job['class']) }}</code>
                                @if (!empty($job['async']) && !$job['async'])
                                    <span class="text-[10px] text-red-500 ml-1">(sync)</span>
                                @else
                                    <span class="text-[10px] text-green-600 ml-1">(queued)</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🔔 Events --}}
            @if (!empty($model['flow']['events']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔔 Events</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($model['flow']['events'] as $event)
                            <li><code>{{ class_basename($event['class']) }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 👁 Observers --}}
            @if (!empty($model['flow']['observers']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">👁 Observers</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($model['flow']['observers'] as $observer)
                            <li><code>{{ class_basename($observer) }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🧩 Dependencies --}}
            @if (!empty($model['flow']['dependencies']))
                <div class="sm:col-span-2">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🧩 Dependencies</span>
                    <div class="text-xs bg-gray-50 rounded p-2 text-gray-800 leading-tight">
                        {{ implode(', ', array_map('class_basename', $model['flow']['dependencies'])) }}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>