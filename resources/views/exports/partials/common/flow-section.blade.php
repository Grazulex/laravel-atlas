{{-- 
    Flow section component for jobs, events, dependencies, etc.
    @param array $flow - Flow data
    @param string $type - Component type for conditional rendering
--}}
@if (!empty($flow['jobs']) || !empty($flow['events']) || !empty($flow['notifications']) || !empty($flow['mails']) || !empty($flow['logs']) || !empty($flow['dependencies']) || !empty($flow['calls']) || !empty($flow['observers']))
    <div class="mt-4">
        <h3 class="text-xs text-gray-400 font-semibold mb-2">🔄 Flow & Dependencies</h3>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            
            {{-- Jobs --}}
            @if (!empty($flow['jobs']))
                <div class="min-h-[3rem]">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📬 Jobs</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['jobs'] as $job)
                            <li>
                                <code>{{ class_basename($job['class']) }}</code>
                                @if (isset($job['async']))
                                    <span class="text-[10px] {{ $job['async'] ? 'text-green-600' : 'text-red-500' }} ml-1">
                                        ({{ $job['async'] ? 'queued' : 'sync' }})
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="min-h-[3rem]">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📬 Jobs</span>
                    <span class="text-xs text-gray-500 italic">None</span>
                </div>
            @endif

            {{-- Events --}}
            @if (!empty($flow['events']))
                <div class="min-h-[3rem]">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔔 Events</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['events'] as $event)
                            <li><code>{{ class_basename($event['class']) }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="min-h-[3rem]">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔔 Events</span>
                    <span class="text-xs text-gray-500 italic">None</span>
                </div>
            @endif

            {{-- Observers (Models only) --}}
            @if ($type === 'model')
                @if (!empty($flow['observers']))
                    <div class="min-h-[3rem]">
                        <span class="block text-xs text-gray-400 font-semibold mb-1">👁 Observers</span>
                        <ul class="text-xs space-y-0.5">
                            @foreach ($flow['observers'] as $observer)
                                <li><code>{{ class_basename($observer) }}</code></li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="min-h-[3rem]">
                        <span class="block text-xs text-gray-400 font-semibold mb-1">👁 Observers</span>
                        <span class="text-xs text-gray-500 italic">None</span>
                    </div>
                @endif
            @endif

            {{-- Command Calls (Commands only) --}}
            @if ($type === 'command' && !empty($flow['calls']))
                <div class="min-h-[3rem]">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">🔁 Calls</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['calls'] as $call)
                            <li><code>{{ $call }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Notifications Used (Notification type only) --}}
            @if ($type === 'notification' && !empty($flow['notifications']))
                <div class="min-h-[3rem]">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📬 Notifications Used</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['notifications'] as $notif)
                            <li><code>{{ class_basename($notif['class']) }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Notifications --}}
            @if (!empty($flow['notifications']))
                <div class="min-h-[3rem]">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">💬 Notifications</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['notifications'] as $notif)
                            @if (is_array($notif))
                                <li><code>{{ class_basename($notif['class']) }}</code></li>
                            @else
                                <li><code>{{ class_basename($notif) }}</code></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Mail --}}
            @if (!empty($flow['mails']))
                <div class="min-h-[3rem]">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📧 Mail</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['mails'] as $mail)
                            <li><code>{{ $mail }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Logs --}}
            @if (!empty($flow['logs']))
                <div class="min-h-[3rem]">
                    <span class="block text-xs text-gray-400 font-semibold mb-1">📜 Logs</span>
                    <ul class="text-xs space-y-0.5">
                        @foreach ($flow['logs'] as $log)
                            <li><code>{{ $log }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>

        {{-- Dependencies (Full width) --}}
        @if (!empty($flow['dependencies']))
            <div class="mt-3">
                <span class="block text-xs text-gray-400 font-semibold mb-1">🧩 Dependencies</span>
                <div class="text-xs bg-gray-50 dark:bg-gray-700 rounded p-2 text-gray-800 dark:text-gray-200 leading-tight">
                    @if (is_array($flow['dependencies']) && isset($flow['dependencies'][0]) && is_string($flow['dependencies'][0]))
                        {{-- Simple array of dependencies --}}
                        {{ implode(', ', array_map('class_basename', $flow['dependencies'])) }}
                    @else
                        {{-- Grouped dependencies --}}
                        @foreach ($flow['dependencies'] as $type => $deps)
                            @if (!empty($deps))
                                <div class="mb-1">
                                    <span class="font-semibold capitalize text-indigo-500">{{ $type }}:</span>
                                    {{ implode(', ', array_map('class_basename', $deps)) }}
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        @endif
    </div>
@endif
