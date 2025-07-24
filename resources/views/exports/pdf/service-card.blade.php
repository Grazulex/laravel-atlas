{{-- Service Card for PDF --}}
<div class="card no-break">
    <div class="card-header">
        <div class="card-title">{{ class_basename($item['class'] ?? 'Unknown') }}</div>
        <div class="card-subtitle">{{ !empty($item['methods']) ? count($item['methods']) . ' methods' : 'No methods' }}</div>
    </div>
    
    <div class="card-content">
        <div class="property-item">
            <div class="property-label">📁 Namespace:</div>
            <div class="property-value">{{ dirname(str_replace('\\', '/', $item['class'] ?? '')) }}</div>
        </div>
        
        <div class="property-item">
            <div class="property-label">🎯 Methods:</div>
            <div class="property-value">{{ !empty($item['methods']) ? count($item['methods']) . ' methods' : 'None' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">🔧 Dependencies:</div>
            <div class="property-value">{{ !empty($item['dependencies']) ? count($item['dependencies']) . ' dependencies' : 'None' }}</div>
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
