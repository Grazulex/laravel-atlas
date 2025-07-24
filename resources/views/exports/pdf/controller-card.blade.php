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
            <div class="property-label">METHODS:</div>
            <div class="property-value">{{ !empty($item['methods']) ? count($item['methods']) . ' methods' : 'None' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">TRAITS:</div>
            <div class="property-value">{{ !empty($item['traits']) ? count($item['traits']) . ' traits' : 'None' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">MIDDLEWARES:</div>
            <div class="property-value">{{ !empty($item['middlewares']) ? count($item['middlewares']) . ' middlewares' : 'None' }}</div>
        </div>

        {{-- Methods Table --}}
        @if (!empty($item['methods']) && count($item['methods']) <= 15)
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Visibility</th>
                        <th>Parameters</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['methods'] as $method)
                        <tr>
                            <td>{{ $method['name'] ?? 'N/A' }}</td>
                            <td>{{ $method['visibility'] ?? 'public' }}</td>
                            <td>{{ !empty($method['parameters']) ? count($method['parameters']) : '0' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Actions Table --}}
        @if (!empty($item['actions']) && count($item['actions']) <= 10)
            <div class="property-item">
                <div class="property-label">ACTIONS:</div>
            </div>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Route</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['actions'] as $action)
                        <tr>
                            <td>{{ $action['method'] ?? 'N/A' }}</td>
                            <td>{{ $action['route'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card-footer">
        <div class="footer-info">
            <div>
                <strong>Class:</strong> {{ $item['class'] ?? 'Unknown' }}
            </div>
            <div>
                <strong>File:</strong> {{ basename($item['file'] ?? 'N/A') }}
            </div>
        </div>
    </div>
</div>