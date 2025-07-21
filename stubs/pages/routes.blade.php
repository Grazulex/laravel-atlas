<!-- Routes Page -->
<div id="routes" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🛣️ Application Routes</h2>
        </div>
        <div class="card-body">
            @if(isset($data['routes']))
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>URI</th>
                            <th>Name</th>
                            <th>Controller</th>
                            <th>Action</th>
                            <th>Middleware</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['routes'] as $route)
                        <tr>
                            <td><span class="badge badge-success">{{ is_array($route['method'] ?? '') ? implode('|', $route['method']) : ($route['method'] ?? 'GET') }}</span></td>
                            <td><code>{{ $route['uri'] }}</code></td>
                            <td>{{ $route['name'] ?? '-' }}</td>
                            <td>
                                @if(is_array($route['controller'] ?? ''))
                                    @if(isset($route['controller']['short_name']))
                                        {{ $route['controller']['short_name'] }}
                                    @elseif(isset($route['controller']['class']))
                                        {{ class_basename($route['controller']['class']) }}
                                    @else
                                        [Complex Controller]
                                    @endif
                                @else
                                    {{ $route['controller'] ? class_basename($route['controller']) : 'Closure' }}
                                @endif
                            </td>
                            <td>
                                @if(is_array($route['action'] ?? 'handle'))
                                    @if(isset($route['action']['uses']) && strpos($route['action']['uses'], '@') !== false)
                                        {{ explode('@', $route['action']['uses'])[1] ?? 'handle' }}
                                    @elseif(isset($route['controller']['method']))
                                        {{ $route['controller']['method'] }}
                                    @else
                                        [Complex Action]
                                    @endif
                                @else
                                    {{ $route['action'] ?? 'handle' }}
                                @endif
                            </td>
                            <td>
                                @if(isset($route['middleware']) && !empty($route['middleware']))
                                    @if(is_array($route['middleware']))
                                        @foreach($route['middleware'] as $middleware)
                                            <span class="badge badge-secondary">{{ $middleware }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge badge-secondary">{{ $route['middleware'] }}</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No routes found.</p>
            @endif
        </div>
    </div>
</div>
