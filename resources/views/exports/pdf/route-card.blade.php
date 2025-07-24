{{-- Route Card for PDF --}}
<div class="card no-break">
    <div class="card-header">
        <div class="card-title">{{ $item['uri'] }}</div>
        <div class="card-subtitle">{{ strtoupper($item['type'] ?? 'WEB') }} - {{ !empty($item['methods']) ? implode(', ', $item['methods']) : 'GET' }}</div>
    </div>
    
    <div class="card-content">
        {{-- Basic Properties --}}
        <div class="property-item">
            <div class="property-label">ROUTE NAME:</div>
            <div class="property-value">{{ $item['name'] ?? 'Not Named' }}</div>
        </div>
        
        <div class="property-item">
            <div class="property-label">HTTP METHODS:</div>
            <div class="property-value">{{ !empty($item['methods']) ? implode(', ', $item['methods']) : 'GET' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">HANDLER:</div>
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

        @if (!empty($item['prefix']))
            <div class="property-item">
                <div class="property-label">PREFIX:</div>
                <div class="property-value">{{ $item['prefix'] }}</div>
            </div>
        @endif

        @if (!empty($item['domain']))
            <div class="property-item">
                <div class="property-label">DOMAIN:</div>
                <div class="property-value">{{ $item['domain'] }}</div>
            </div>
        @endif

        {{-- Middlewares --}}
        @if (!empty($item['middleware']))
            <div class="property-item">
                <div class="property-label">MIDDLEWARES ({{ count($item['middleware']) }}):</div>
                <div class="property-value">{{ implode(', ', array_slice($item['middleware'], 0, 10)) }}</div>
            </div>
        @else
            <div class="property-item">
                <div class="property-label">MIDDLEWARES:</div>
                <div class="property-value">None</div>
            </div>
        @endif

        {{-- Flow Information --}}
        @if (!empty($item['flow']['jobs']) || !empty($item['flow']['events']))
            <div class="property-item">
                <div class="property-label">FLOW:</div>
                <div class="property-value">
                    @if (!empty($item['flow']['jobs']))
                        Jobs: {{ count($item['flow']['jobs']) }}
                    @endif
                    @if (!empty($item['flow']['events']))
                        @if (!empty($item['flow']['jobs'])) | @endif
                        Events: {{ count($item['flow']['events']) }}
                    @endif
                </div>
            </div>
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
