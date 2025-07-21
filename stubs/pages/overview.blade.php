<!-- Overview Page -->
<div id="overview" class="page active">
    <div class="card">
        <div class="card-header">
            <h2>🎯 Application Entry Points</h2>
        </div>
        <div class="card-body">
            <p>Your Laravel application has <strong>{{ count($data['routes'] ?? []) + count($data['commands'] ?? []) }}</strong> entry points. Click on any entry point to explore its execution flow.</p>
            
            <div class="entry-points">
                @if(isset($data['routes']))
                    @foreach($data['routes'] as $route)
                    <div class="entry-point route" onclick="showRouteDetails('{{ $route['name'] ?? $route['uri'] }}')">
                        <div class="entry-point-title">
                            🛣️ {{ $route['name'] ?? $route['uri'] }}
                        </div>
                        <div class="entry-point-details">
                            <span class="badge badge-success">{{ is_array($route['method'] ?? '') ? implode('|', $route['method']) : ($route['method'] ?? 'GET') }}</span>
                            {{ $route['uri'] }}
                        </div>
                    </div>
                    @endforeach
                @endif
                
                @if(isset($data['commands']))
                    @foreach($data['commands'] as $command)
                    <div class="entry-point command" onclick="showCommandDetails('{{ $command['name'] ?: $command['class_name'] }}')">
                        <div class="entry-point-title">
                            ⚡ {{ $command['name'] ?: class_basename($command['class_name'] ?? 'Unknown Command') }}
                        </div>
                        <div class="entry-point-details">
                            <span class="badge badge-warning">CMD</span>
                            {{ $command['description'] ?? 'No description available' }}
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
