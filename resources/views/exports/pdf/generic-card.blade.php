{{-- Generic Card Template for PDF --}}
@php
    $cardTitle = '';
    $cardSubtitle = '';
    $cardType = $cardType ?? 'Component';
    
    if (isset($item['class'])) {
        $cardTitle = is_array($item['class']) ? class_basename(implode('\\', $item['class'])) : class_basename($item['class']);
        $cardSubtitle = is_array($item['class']) ? implode('\\', array_slice($item['class'], 0, -1)) : dirname(str_replace('\\', '/', $item['class']));
    } elseif (isset($item['name'])) {
        $cardTitle = $item['name'];
    } else {
        $cardTitle = 'Unknown ' . $cardType;
    }
@endphp

<div class="card no-break">
    <div class="card-header">
        <div class="card-title">{{ $cardTitle }}</div>
        @if ($cardSubtitle)
            <div class="card-subtitle">{{ $cardSubtitle }}</div>
        @endif
    </div>
    
    <div class="card-content">
        @if (isset($item['class']))
            <div class="property-item">
                <div class="property-label">📁 Class:</div>
                <div class="property-value">{{ is_array($item['class']) ? implode('\\', $item['class']) : $item['class'] }}</div>
            </div>
        @endif
        
        @if (isset($item['methods']) && !empty($item['methods']))
            <div class="property-item">
                <div class="property-label">🎯 Methods:</div>
                <div class="property-value">{{ count($item['methods']) }} methods</div>
            </div>
        @endif

        @if (isset($item['dependencies']) && !empty($item['dependencies']))
            <div class="property-item">
                <div class="property-label">🔧 Dependencies:</div>
                <div class="property-value">{{ count($item['dependencies']) }} dependencies</div>
            </div>
        @endif

        @if (isset($item['properties']) && !empty($item['properties']))
            <div class="property-item">
                <div class="property-label">⚙️ Properties:</div>
                <div class="property-value">{{ count($item['properties']) }} properties</div>
            </div>
        @endif
    </div>

    <div class="card-footer">
        <div class="footer-info">
            <div>
                <strong>Type:</strong> {{ $cardType }}
            </div>
            <div>
                <strong>File:</strong> {{ basename($item['file'] ?? 'N/A') }}
            </div>
        </div>
    </div>
</div>
