<!-- Events Page -->
<div id="events" class="page">
    <div class="card">
        <div class="card-header">
            <h2>📡 Application Events</h2>
        </div>
        <div class="card-body">
            @if(isset($data['events']))
                @foreach($data['events'] as $event)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($event['class_name']) }}</h3>
                        <small>{{ $event['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($event['properties']) && is_array($event['properties']))
                        <p><strong>Properties:</strong></p>
                        <ul>
                            @foreach($event['properties'] as $property)
                                @if(is_array($property) && isset($property['name']))
                                    <li>{{ $property['name'] }} ({{ $property['type'] ?? 'mixed' }})</li>
                                @else
                                    <li>{{ is_array($property) ? implode(', ', $property) : $property }}</li>
                                @endif
                            @endforeach
                        </ul>
                        @elseif(isset($event['properties']))
                        <p><strong>Properties:</strong> {{ implode(', ', $event['properties']) }}</p>
                        @endif
                        
                        @if(isset($event['triggered_by']))
                        <h4>Triggered By</h4>
                        <div class="flow">
                            @foreach($event['triggered_by'] as $trigger)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ is_array($trigger) ? implode(', ', $trigger) : class_basename($trigger) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($event['listeners']))
                        <h4>Event Listeners</h4>
                        <div class="flow">
                            @foreach($event['listeners'] as $listener)
                            <div class="flow-step async">
                                <div class="flow-step-icon">A{{ $loop->iteration }}</div>
                                {{ class_basename($listener) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No events found.</p>
            @endif
        </div>
    </div>
</div>
