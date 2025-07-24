{{-- 
    Flow section component for jobs, events, dependencies, etc.
    @param array $flow - Flow data
    @param string $type - Component type for conditional rendering
--}}
@if (!empty($flow['jobs']) || !empty($flow['events']) || !empty($flow['notifications']) || !empty($flow['mails']) || !empty($flow['logs']) || !empty($flow['dependencies']) || !empty($flow['calls']) || !empty($flow['observers']) || !empty($flow['facades']) || !empty($flow['cache']) || !empty($flow['auth']) || !empty($flow['exceptions']) || !empty($flow['services']) || !empty($flow['uses']) || !empty($flow['models']) || !empty($flow['rules']) || !empty($flow['policies']) || !empty($flow['validations']))
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <span class="mr-2">🔄</span>
            Flow & Dependencies
        </h4>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            
            {{-- Jobs --}}
            @if (!empty($flow['jobs']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">📬</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Jobs</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['jobs'] as $job)
                            <div class="text-xs">
                                @if (is_array($job))
                                    <code>{{ class_basename($job['class']) }}</code>
                                    @if (isset($job['async']))
                                        <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] {{ $job['async'] ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                            {{ $job['async'] ? 'queued' : 'sync' }}
                                        </span>
                                    @endif
                                @else
                                    <code>{{ class_basename($job) }}</code>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Events --}}
            @if (!empty($flow['events']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">🔔</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Events</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['events'] as $event)
                            <div class="text-xs">
                                @if (is_array($event))
                                    <code>{{ class_basename($event['class']) }}</code>
                                @else
                                    <code>{{ class_basename($event) }}</code>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Observers (Models only) --}}
            @if ($type === 'model' && !empty($flow['observers']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">👁</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Observers</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['observers'] as $observer)
                            <div class="text-xs">
                                <code>{{ class_basename($observer) }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Command Calls (Commands only) --}}
            @if ($type === 'command' && !empty($flow['calls']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">🔁</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Calls</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['calls'] as $call)
                            <div class="text-xs">
                                <code>{{ $call }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Notifications --}}
            @if (!empty($flow['notifications']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">💬</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Notifications</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['notifications'] as $notif)
                            <div class="text-xs">
                                @if (is_array($notif))
                                    <code>{{ class_basename($notif['class']) }}</code>
                                @else
                                    <code>{{ class_basename($notif) }}</code>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Mail --}}
            @if (!empty($flow['mails']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">📧</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Mail</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['mails'] as $mail)
                            <div class="text-xs">
                                <code>{{ $mail }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Logs --}}
            @if (!empty($flow['logs']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">📜</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Logs</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['logs'] as $log)
                            <div class="text-xs">
                                <code>{{ $log }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Facades --}}
            @if (!empty($flow['facades']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">🏛️</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Facades</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['facades'] as $facade)
                            <div class="text-xs">
                                <code>{{ $facade }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Cache --}}
            @if (!empty($flow['cache']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">💾</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cache</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['cache'] as $cacheOp)
                            <div class="text-xs">
                                <code>{{ $cacheOp }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Auth --}}
            @if (!empty($flow['auth']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">🔐</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Auth</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['auth'] as $authOp)
                            <div class="text-xs">
                                <code>{{ $authOp }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Exceptions --}}
            @if (!empty($flow['exceptions']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">⚠️</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Exceptions</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['exceptions'] as $exception)
                            <div class="text-xs">
                                <code>{{ $exception }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Services --}}
            @if (!empty($flow['services']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">🔧</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Services</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['services'] as $service)
                            <div class="text-xs">
                                <code>{{ $service }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Models --}}
            @if (!empty($flow['models']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">🧱</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Models</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['models'] as $model)
                            <div class="text-xs">
                                <code>{{ $model }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Custom Rules --}}
            @if (!empty($flow['rules']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">📏</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Custom Rules</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['rules'] as $rule)
                            <div class="text-xs">
                                <code>{{ $rule }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Policies --}}
            @if (!empty($flow['policies']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">🛡️</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Policies</span>
                    </div>
                    <div class="space-y-1">
                        @foreach ($flow['policies'] as $policy)
                            <div class="text-xs">
                                <code>{{ $policy }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Validation Types --}}
            @if (!empty($flow['validations']))
                <div class="min-h-[4rem] bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <span class="text-sm mr-2">✅</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Validation Types</span>
                    </div>
                    <div class="text-xs">
                        <div class="bg-white dark:bg-gray-700 rounded p-2 text-gray-800 dark:text-gray-200 leading-tight">
                            {{ implode(', ', $flow['validations']) }}
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Dependencies (Full width) --}}
        @if (!empty($flow['dependencies']))
            <div class="mt-4">
                <div class="flex items-center mb-2">
                    <span class="text-sm mr-2">🧩</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Dependencies</span>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed">
                        @if (is_array($flow['dependencies']) && isset($flow['dependencies'][0]) && is_string($flow['dependencies'][0]))
                            {{-- Simple array of dependencies --}}
                            {{ implode(', ', array_map('class_basename', $flow['dependencies'])) }}
                        @else
                            {{-- Grouped dependencies --}}
                            @foreach ($flow['dependencies'] as $type => $deps)
                                @if (!empty($deps))
                                    <div class="mb-2">
                                        <span class="font-semibold capitalize text-indigo-600 dark:text-indigo-400">{{ $type }}:</span>
                                        {{ implode(', ', array_map('class_basename', $deps)) }}
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
