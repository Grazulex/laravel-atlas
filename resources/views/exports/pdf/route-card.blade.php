{{-- Route Card for PDF --}}
<div class="card no-break">
    <div class="card-header">
        <div class="card-title">{{ $item['uri'] }}</div>
        <div class="card-subtitle">{{ strtoupper($item['type'] ?? 'WEB') }}</div>
    </div>
    
    <div class="card-content">
        {{-- Basic Properties --}}
        <div class="property-item">
            <div class="property-label">🔖 Route Name:</div>
            <div class="property-value">{{ $item['name'] ?? 'Not Named' }}</div>
        </div>
        
        <div class="property-item">
            <div class="property-label">🧭 HTTP Methods:</div>
            <div class="property-value">{{ !empty($item['methods']) ? implode(', ', $item['methods']) : 'GET' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">🛡️ Middlewares:</div>
            <div class="property-value">{{ !empty($item['middleware']) ? count($item['middleware']) . ' middlewares' : '0 middlewares' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">⚙️ Handler:</div>
            <div class="property-value">
                @if ($item['is_closure'])
                    Closure Function
                @elseif (!empty($item['controller']))
                    {{ class_basename($item['controller']) }}@{{ $item['uses'] ?? 'handle' }}
                @else
                    {{ $item['action'] ?? 'Unknown' }}
                @endif
            </div>
        </div>

        {{-- Middlewares Table --}}
        @if (!empty($item['middleware']) && count($item['middleware']) <= 10)
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Middleware</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['middleware'] as $middleware)
                        <tr>
                            <td>{{ $middleware }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card-footer">
        <div class="footer-info">
            <div>
                @if ($item['is_closure'])
                    <strong>Type:</strong> Closure
                @else
                    <strong>Controller:</strong> {{ $item['controller'] ?? 'N/A' }}
                @endif
            </div>
            <div>
                <strong>File:</strong> {{ basename($item['file'] ?? 'N/A') }}
            </div>
        </div>
    </div>
</div>
