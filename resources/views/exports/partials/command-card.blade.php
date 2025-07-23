<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 truncate max-w-[80%]">
            💬 {{ class_basename($command['class']) }}
        </h2>
        @if (!empty($command['aliases']))
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                {{ implode(', ', $command['aliases']) }}
            </span>
        @endif
    </div>

    @if (!empty($command['description']))
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-2">{{ $command['description'] }}</p>
    @endif

    {{-- Signature --}}
    @if (!empty($command['parsed_signature']))
        <div class="mt-2">
            <h4 class="text-xs text-gray-400 font-semibold mb-1">🧾 Signature</h4>
            <table class="w-full text-xs border rounded overflow-hidden">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
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
                            <td class="p-1 text-gray-600 dark:text-gray-300">{{ $sig['description'] ?? '-' }}</td>
                            <td class="p-1">
                                @if ($sig['modifier'] === '*')
                                    <span class="text-[10px] text-blue-600">[array]</span>
                                @elseif ($sig['modifier'] === '=')
                                    <span class="text-[10px] text-yellow-600">[default]</span>
                                @else
                                    <span class="text-[10px] text-gray-500">[required]</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Flow --}}
    @php $flow = $command['flow'] ?? []; @endphp
    @if (!empty($flow['jobs']) || !empty($flow['events']) || !empty($flow['calls']) || array_filter($flow['dependencies']))
        <div class="mt-4 grid sm:grid-cols-2 gap-4">
            {{-- Jobs --}}
            @if (!empty($flow['jobs']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📬 Jobs</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['jobs'] as $job)
                            <li>
                                <code>{{ class_basename($job['class']) }}</code>
                                <span class="text-[10px] {{ $job['async'] ? 'text-green-600' : 'text-red-500' }} ml-1">
                                    ({{ $job['async'] ? 'queued' : 'sync' }})
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Events --}}
            @if (!empty($flow['events']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔔 Events</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['events'] as $event)
                            <li><code>{{ class_basename($event['class']) }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Command Calls --}}
            @if (!empty($flow['calls']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔁 Calls other commands</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['calls'] as $call)
                            <li><code>{{ $call }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Notifications --}}
            @if (!empty($flow['notifications']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📣 Notifications</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['notifications'] as $notif)
                            <li><code>{{ class_basename($notif['class']) }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Dependencies --}}
            @if (array_filter($flow['dependencies']))
                <div class="sm:col-span-2">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔗 Dependencies</span>
                    <div class="text-xs bg-gray-50 dark:bg-gray-700 rounded p-2 text-gray-800 dark:text-gray-200 leading-tight">
                        @foreach ($flow['dependencies'] as $type => $deps)
                            @if (!empty($deps))
                                <div class="mb-1">
                                    <span class="font-semibold capitalize text-indigo-500">{{ $type }}:</span>
                                    {{ implode(', ', array_map('class_basename', $deps)) }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
