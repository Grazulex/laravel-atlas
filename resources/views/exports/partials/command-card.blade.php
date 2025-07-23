<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-sm font-semibold text-indigo-700 truncate max-w-[80%]">
            💬 {{ class_basename($command['class']) }}
        </h2>
        @if (!empty($command['aliases']))
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                {{ implode(', ', $command['aliases']) }}
            </span>
        @endif
    </div>

    {{-- Description --}}
    @if (!empty($command['description']))
        <p class="text-xs text-gray-600 italic mb-2">{{ $command['description'] }}</p>
    @endif

    {{-- Signature breakdown --}}
    @if (!empty($command['parsed_signature']))
        <div class="mt-2">
            <h4 class="text-xs text-gray-400 font-semibold mb-1">🧾 Signature</h4>
            <table class="w-full text-xs border rounded overflow-hidden">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-1">Name</th>
                        <th class="p-1">Type</th>
                        <th class="p-1">Details</th>
                        <th class="p-1">Modifier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($command['parsed_signature'] as $sig)
                        <tr class="border-t">
                            <td class="p-1"><code>{{ $sig['name'] }}</code></td>
                            <td class="p-1">{{ ucfirst($sig['type']) }}</td>
                            <td class="p-1 text-gray-600">{{ $sig['description'] ?? '-' }}</td>
                            <td class="p-1">
                                @if ($sig['modifier'] === '*')
                                    <span class="text-[10px] text-blue-600">[array]</span>
                                @elseif ($sig['modifier'] === '=')
                                    <span class="text-[10px] text-yellow-600">[default]</span>
                                @elseif ($sig['modifier'] === '')
                                    <span class="text-[10px] text-gray-500">[required]</span>
                                @else
                                    <span class="text-[10px] text-gray-400">{{ $sig['modifier'] }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Flow --}}
    @if (!empty($command['flow']))
        <div class="mt-4 grid sm:grid-cols-2 gap-4">

            {{-- 📬 Jobs --}}
            @if (!empty($command['flow']['jobs']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📬 Jobs</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($command['flow']['jobs'] as $job)
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
            @if (!empty($command['flow']['events']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔔 Events</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($command['flow']['events'] as $event)
                            <li><code>{{ class_basename($event['class']) }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🔁 Calls --}}
            @if (!empty($command['flow']['calls']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔁 Calls other commands</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($command['flow']['calls'] as $call)
                            <li><code>{{ $call }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🔗 Dependencies --}}
            @if (!empty($command['flow']['dependencies']))
                <div class="sm:col-span-2">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔗 Dependencies</span>
                    <div class="text-xs bg-gray-50 rounded p-2 text-gray-800 leading-tight">
                        {{ implode(', ', array_map('class_basename', $command['flow']['dependencies'])) }}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
