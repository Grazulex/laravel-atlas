<!-- Commands Page -->
<div id="commands" class="page">
    <div class="card">
        <div class="card-header">
            <h2>⚡ Artisan Commands</h2>
        </div>
        <div class="card-body">
            @if(isset($data['commands']))
                @foreach($data['commands'] as $command)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ $command['name'] ?: class_basename($command['class_name'] ?? 'Unknown Command') }}</h3>
                        @if($command['signature'])
                            <code>{{ $command['signature'] }}</code>
                        @else
                            <small>{{ $command['class_name'] ?? '' }}</small>
                        @endif
                    </div>
                    <div class="card-body">
                        <p>{{ $command['description'] ?? 'No description available' }}</p>
                        
                        @if(isset($command['flows']))
                        <h4>Execution Flow</h4>
                        <div class="flow">
                            @if(isset($command['flows']['synchronous']))
                            <h5>Synchronous Steps</h5>
                            @foreach($command['flows']['synchronous'] as $step)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ is_array($step) ? implode(', ', $step) : $step }}
                            </div>
                            @endforeach
                            @endif
                            
                            @if(isset($command['flows']['asynchronous']))
                            <h5>Asynchronous Events</h5>
                            @foreach($command['flows']['asynchronous'] as $step)
                            <div class="flow-step async">
                                <div class="flow-step-icon">A{{ $loop->iteration }}</div>
                                {{ is_array($step) ? implode(', ', $step) : $step }}
                            </div>
                            @endforeach
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No commands found.</p>
            @endif
        </div>
    </div>
</div>
