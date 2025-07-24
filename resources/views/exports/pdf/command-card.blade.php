{{-- Command Card for PDF --}}
<div class="card no-break">
    <div class="card-header">
        <div class="card-title">{{ $item['signature'] ?? $item['name'] ?? 'N/A' }}</div>
        <div class="card-subtitle">{{ class_basename($item['class'] ?? 'Unknown') }}</div>
    </div>
    
    <div class="card-content">
        <div class="property-item">
            <div class="property-label">📝 Description:</div>
            <div class="property-value">{{ $item['description'] ?? 'No description' }}</div>
        </div>
        
        <div class="property-item">
            <div class="property-label">🎯 Arguments:</div>
            <div class="property-value">{{ !empty($item['arguments']) ? count($item['arguments']) . ' arguments' : 'None' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">⚙️ Options:</div>
            <div class="property-value">{{ !empty($item['options']) ? count($item['options']) . ' options' : 'None' }}</div>
        </div>
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
