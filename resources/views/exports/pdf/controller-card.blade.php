{{-- Controller Card for PDF --}}
<div class="card no-break">
    <div class="card-header">
        <div class="card-title">{{ class_basename($item['class'] ?? 'Unknown') }}</div>
        <div class="card-subtitle">{{ !empty($item['methods']) ? count($item['methods']) . ' methods' : 'No methods' }}</div>
    </div>
    
    <div class="card-content">
        {{-- Basic Properties --}}
        <div class="property-item">
            <div class="property-label">NAMESPACE:</div>
            <div class="property-value">{{ dirname(str_replace('\\', '/', $item['class'] ?? '')) }}</div>
        </div>
        
        <div class="property-item">
            <div class="property-label">EXTENDS:</div>
            <div class="property-value">{{ $item['extends'] ?? 'Controller' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">TRAITS:</div>
            <div class="property-value">{{ !empty($item['traits']) ? implode(', ', array_slice($item['traits'], 0, 5)) : 'None' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">MIDDLEWARES:</div>
            <div class="property-value">{{ !empty($item['middlewares']) ? implode(', ', array_slice($item['middlewares'], 0, 5)) : 'None' }}</div>
        </div>

        {{-- Methods Table --}}
        @if (!empty($item['methods']))
            <div class="property-item">
                <div class="property-label">METHODS ({{ count($item['methods']) }}):</div>
            </div>
            
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Visibility</th>
                        <th>Parameters</th>
                        <th>Return Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (array_slice($item['methods'], 0, 20) as $method)
                        <tr>
                            <td>{{ $method['name'] ?? 'N/A' }}</td>
                            <td>{{ $method['visibility'] ?? 'public' }}</td>
                            <td>{{ !empty($method['parameters']) ? count($method['parameters']) : '0' }}</td>
                            <td>{{ $method['return_type'] ?? 'mixed' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Dependencies --}}
        @if (!empty($item['dependencies']))
            <div class="property-item">
                <div class="property-label">DEPENDENCIES:</div>
                <div class="property-value">{{ implode(', ', array_slice($item['dependencies'], 0, 8)) }}</div>
            </div>
        @endif

        {{-- Routes/Actions --}}
        @if (!empty($item['actions']))
            <div class="property-item">
                <div class="property-label">ROUTES/ACTIONS ({{ count($item['actions']) }}):</div>
            </div>
            
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Route</th>
                        <th>HTTP Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (array_slice($item['actions'], 0, 15) as $action)
                        <tr>
                            <td>{{ $action['method'] ?? 'N/A' }}</td>
                            <td>{{ $action['route'] ?? 'N/A' }}</td>
                            <td>{{ $action['http_method'] ?? 'GET' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card-footer">
        <div class="footer-info">
            <div>
                <strong>Full Class:</strong> {{ $item['class'] ?? 'Unknown' }}
            </div>
            <div>
                <strong>File:</strong> {{ basename($item['file'] ?? 'N/A') }}
            </div>
        </div>
    </div>
</div>
