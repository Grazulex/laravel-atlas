<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-indigo-800 mb-1">💬 {{ $command['class'] }}</h2>
    <p class="text-sm text-gray-600 mb-3">
        Signature: <code>{{ $command['signature'] }}</code><br>
        @if (!empty($command['description']))
            Description: <span class="text-gray-800 italic">{{ $command['description'] }}</span><br>
        @endif
        @if (!empty($command['aliases']))
            Aliases:
            <code>{{ implode(', ', $command['aliases']) }}</code>
        @endif
    </p>

    @if (!empty($command['flow']))
        <div class="grid md:grid-cols-2 gap-6">

            {{-- 📬 Jobs --}}
            @if (!empty($command['flow']['jobs']))
                <div>
                    <h4 class="font-semibold text-xs text-gray-400 mb-1">📬 Jobs</h4>
                    <ul class="text-sm bg-gray-100 rounded p-2 space-y-1">
                        @foreach ($command['flow']['jobs'] as $job)
                            <li>
                                <code>{{ $job['class'] }}</code>
                                @if (!empty($job['async']) && !$job['async'])
                                    <span class="text-red-500 text-xs ml-2">(sync)</span>
                                @else
                                    <span class="text-green-600 text-xs ml-2">(queued)</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🔔 Events --}}
            @if (!empty($command['flow']['events']))
                <div>
                    <h4 class="font-semibold text-xs text-gray-400 mb-1">🔔 Events</h4>
                    <ul class="text-sm bg-gray-100 rounded p-2 space-y-1">
                        @foreach ($command['flow']['events'] as $event)
                            <li><code>{{ $event['class'] }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🔁 Command Calls --}}
            @if (!empty($command['flow']['calls']))
                <div>
                    <h4 class="font-semibold text-xs text-gray-400 mb-1">🔁 Calls other commands</h4>
                    <ul class="text-sm bg-gray-100 rounded p-2 space-y-1">
                        @foreach ($command['flow']['calls'] as $call)
                            <li><code>{{ $call }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 🔗 Dependencies --}}
            @if (!empty($command['flow']['dependencies']))
                <div>
                    <h4 class="font-semibold text-xs text-gray-400 mb-1">🔗 Dependencies</h4>
                    <ul class="text-sm bg-gray-100 rounded p-2 space-y-1">
                        @foreach ($command['flow']['dependencies'] as $dep)
                            <li><code>{{ $dep }}</code></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
</div>
