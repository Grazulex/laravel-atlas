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
                        <h4>Triggered By</h4>
                        <div class="flow">
                            @foreach($job['triggered_by'] as $trigger)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ class_basename($trigger) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($job['dependencies']))
                        <h4>Dependencies</h4>
                        <div class="flow">
                            @foreach($job['dependencies'] as $dep)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ class_basename($dep) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($job['events']))
                        <h4>Events Dispatched</h4>
                        <div class="flow">
                            @foreach($job['events'] as $event)
                            <div class="flow-step async">
                                <div class="flow-step-icon">A{{ $loop->iteration }}</div>
                                {{ class_basename($event) }}
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
