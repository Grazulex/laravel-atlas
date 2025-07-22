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
                        
                        @php
                        // Dynamically find what triggers this event by checking actions
                        $eventName = class_basename($event['class_name']);
                        $triggers = [];
                        if(isset($data['actions'])) {
                            foreach($data['actions'] as $action) {
                                if(isset($action['events']) && is_array($action['events'])) {
                                    foreach($action['events'] as $actionEvent) {
                                        if(class_basename($actionEvent) === $eventName) {
                                            $triggers[] = $action['class_name'];
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        @endphp
                        
                        @if(!empty($triggers))
                        <h4>Triggered By</h4>
                        <div class="flow">
                            @foreach($triggers as $trigger)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ class_basename($trigger) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($event['potential_listeners']) && !empty($event['potential_listeners']))
                        <h4>Event Listeners</h4>
                        <div class="flow">
                            @foreach($event['potential_listeners'] as $listener)
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
