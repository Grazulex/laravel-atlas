<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-sm font-semibold text-indigo-700 truncate max-w-[80%]">
            🔧 <code>{{ class_basename($service['class']) }}</code>
        </h2>
        <span class="text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200 font-bold px-2 py-0.5 rounded-full">
            Service
        </span>
    </div>

    {{-- Public Methods --}}
    @if (!empty($service['methods']))
        <div>
            <span class="block text-xs text-gray-400 dark:text-gray-500 font-semibold mb-1">⚙️ Public Methods</span>
            <ul class="text-xs space-y-0.5">
                @foreach ($service['methods'] as $method)
                    <li>
                        <code>{{ $method['name'] }}({{ implode(', ', $method['parameters']) }})</code>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Constructor Dependencies --}}
    @if (!empty($service['dependencies']))
        <div>
            <span class="block text-xs text-gray-400 dark:text-gray-500 font-semibold mb-1">🧩 Constructor Dependencies</span>
            <ul class="text-xs space-y-0.5">
                @foreach ($service['dependencies'] as $dep)
                    @if ($dep)
                        <li><code>{{ class_basename($dep) }}</code></li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Flow --}}
    @php $flow = $service['flow']; @endphp
    @if (!empty($flow['jobs']) || !empty($flow['events']) || !empty($flow['notifications']) || !empty($flow['mails']) || !empty($flow['logs']) || !empty($flow['dependencies']))
        <div class="grid sm:grid-cols-2 gap-4">

            {{-- 📬 Jobs --}}
            @if (!empty($flow['jobs']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📬 Jobs</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['jobs'] as $job)
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

            {{-- 💬 Notifications --}}
            @if (!empty($flow['notifications']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">💬 Notifications</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['notifications'] as $notif)
                            <li><code>{{ class_basename($notif) }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 📧 Mail --}}
            @if (!empty($flow['mails']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📧 Mail</span>
                    <ul class="text-xs text-gray-600 dark:text-gray-300">
                        @foreach ($flow['mails'] as $mail)
                            <li><code>{{ $mail }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 📜 Logs --}}
            @if (!empty($flow['logs']))
                <div>
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📜 Logs</span>
                    <ul class="text-xs text-gray-600 dark:text-gray-300">
                        @foreach ($flow['logs'] as $log)
                            <li><code>{{ $log }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🧩 Detected Dependencies --}}
            @if (!empty($flow['dependencies']))
                <div class="sm:col-span-2">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🧩 Detected Dependencies</span>
                    <div class="text-xs bg-gray-50 dark:bg-gray-700 rounded p-2 text-gray-800 dark:text-gray-200 leading-tight">
                        {{ implode(', ', array_map('class_basename', $flow['dependencies'])) }}
                    </div>
                </div>
            @endif

        </div>
    @endif
</div>