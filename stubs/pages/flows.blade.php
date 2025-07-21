<!-- Application Flows -->
<div id="flows" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🔄 Application Flows</h2>
        </div>
        <div class="card-body">
            @if(isset($data['flows']))
                @foreach($data['flows'] as $flow)
                <div class="flow">
                    <h3>{{ $flow['name'] }}</h3>
                    <p><strong>Entry Point:</strong> {{ $flow['entry_point'] }}</p>
                    <p><strong>Type:</strong> <span class="badge badge-{{ $flow['type'] == 'mixed' ? 'warning' : ($flow['type'] == 'synchronous' ? 'primary' : 'info') }}">{{ ucfirst($flow['type']) }}</span></p>
                    
                    <div style="margin-top: 15px;">
                        @foreach($flow['steps'] as $step)
                        <div class="flow-step {{ strpos($step, '(async)') !== false ? 'async' : '' }}">
                            <div class="flow-step-icon">{{ $loop->iteration }}</div>
                            {{ str_replace('(async)', '', $step) }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @else
                <p>No application flows defined.</p>
            @endif
        </div>
    </div>
</div>
