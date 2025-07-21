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
                        <p><strong>Triggered by:</strong></p>
                        <ul>
                            @foreach($job['triggered_by'] as $trigger)
                            <li>{{ class_basename($trigger) }}</li>
                            @endforeach
                        </ul>
                        @endif
                        
                        @if(isset($job['dependencies']))
                        <p><strong>Dependencies:</strong></p>
                        <ul>
                            @foreach($job['dependencies'] as $dep)
                            <li>{{ class_basename($dep) }}</li>
                            @endforeach
                        </ul>
                        @endif
                        
                        @if(isset($job['events']))
                        <p><strong>Events fired:</strong></p>
                        <ul>
                            @foreach($job['events'] as $event)
                            <li>{{ class_basename($event) }}</li>
                            @endforeach
                        </ul>
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
