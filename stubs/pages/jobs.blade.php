<!-- Jobs Page -->
<div id="jobs" class="page">
    <div class="card">
        <div class="card-header">
            <h2>⚡ Background Jobs</h2>
        </div>
        <div class="card-body">
            @if(isset($data['jobs']))
                @foreach($data['jobs'] as $job)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($job['class_name']) }}</h3>
                        <div>
                            <span class="badge badge-info">Queue: {{ $job['queue'] ?? 'default' }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($job['triggered_by']))
                        <h4>Job Execution Flow</h4>
                        <div class="flow">
                            <h5>Triggered By</h5>
                            @foreach($job['triggered_by'] as $trigger)
                            <div class="flow-step">
                                <div class="flow-step-icon">T{{ $loop->iteration }}</div>
                                <span class="flow-step-text">{{ class_basename($trigger) }}</span>
                                <span style="background: #ffc107; color: #212529; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; margin-left: 8px;">Trigger</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($job['dependencies']))
                        <h4>Dependency Injection</h4>
                        <div class="flow">
                            <h5>Required Services</h5>
                            @foreach($job['dependencies'] as $dep)
                            <div class="flow-step dependency">
                                <div class="flow-step-icon">D{{ $loop->iteration }}</div>
                                <span class="flow-step-text">{{ class_basename($dep) }}</span>
                                <span style="background: #17a2b8; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; margin-left: 8px;">Service</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($job['events']))
                        <h4>Events Dispatched</h4>
                        <div class="flow">
                            <h5>Job Events</h5>
                            @foreach($job['events'] as $event)
                            <div class="flow-step async">
                                <div class="flow-step-icon">E{{ $loop->iteration }}</div>
                                <span class="flow-step-text">{{ class_basename($event) }}</span>
                                <span style="background: #6f42c1; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; margin-left: 8px;">Event</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No jobs found.</p>
            @endif
        </div>
    </div>
</div>
