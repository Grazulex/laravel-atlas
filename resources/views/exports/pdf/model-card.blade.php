{{-- Model Card for PDF --}}
<div class="card no-break">
    <div class="card-header">
        <div class="card-title">{{ is_array($item['class']) ? implode('\\', $item['class']) : $item['class'] }}</div>
        <div class="card-subtitle">Table: {{ $item['table'] ?? 'N/A' }}</div>
    </div>
    
    <div class="card-content">
        {{-- Basic Properties --}}
        <div class="property-item">
            <div class="property-label">TABLE:</div>
            <div class="property-value">{{ $item['table'] ?? 'N/A' }}</div>
        </div>
        
        <div class="property-item">
            <div class="property-label">PRIMARY KEY:</div>
            <div class="property-value">{{ $item['primary_key'] ?? 'id' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">FILLABLE FIELDS:</div>
            <div class="property-value">{{ !empty($item['fillable']) ? count($item['fillable']) . ' fields: ' . implode(', ', array_slice($item['fillable'], 0, 10)) : 'None' }}</div>
        </div>

        <div class="property-item">
            <div class="property-label">HIDDEN FIELDS:</div>
            <div class="property-value">{{ !empty($item['hidden']) ? count($item['hidden']) . ' fields: ' . implode(', ', $item['hidden']) : 'None' }}</div>
        </div>

        @if (!empty($item['casts']))
            <div class="property-item">
                <div class="property-label">CASTS:</div>
                <div class="property-value">{{ count($item['casts']) }} casts</div>
            </div>
        @endif

        {{-- Relationships --}}
        @if (!empty($item['relationships']))
            <div class="property-item">
                <div class="property-label">RELATIONSHIPS ({{ count($item['relationships']) }}):</div>
            </div>
            
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Type</th>
                        <th>Related Model</th>
                        <th>Foreign Key</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (array_slice($item['relationships'], 0, 15) as $rel)
                        <tr>
                            <td>{{ $rel['method'] ?? 'N/A' }}</td>
                            <td>{{ $rel['type'] ?? 'N/A' }}</td>
                            <td>{{ $rel['related'] ?? 'N/A' }}</td>
                            <td>{{ $rel['foreign_key'] ?? $rel['local_key'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Scopes --}}
        @if (!empty($item['scopes']))
            <div class="property-item">
                <div class="property-label">SCOPES:</div>
                <div class="property-value">{{ implode(', ', array_slice($item['scopes'], 0, 10)) }}</div>
            </div>
        @endif

        {{-- Accessors/Mutators --}}
        @if (!empty($item['accessors']) || !empty($item['mutators']))
            <div class="property-item">
                <div class="property-label">ACCESSORS/MUTATORS:</div>
                <div class="property-value">
                    @if (!empty($item['accessors']))
                        Accessors: {{ implode(', ', array_slice($item['accessors'], 0, 5)) }}
                    @endif
                    @if (!empty($item['mutators']))
                        @if (!empty($item['accessors'])) | @endif
                        Mutators: {{ implode(', ', array_slice($item['mutators'], 0, 5)) }}
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="card-footer">
        <div class="footer-info">
            <div>
                <strong>Full Class:</strong> {{ is_array($item['class']) ? implode('\\', $item['class']) : $item['class'] }}
            </div>
            <div>
                <strong>File:</strong> {{ basename($item['file'] ?? 'N/A') }}
            </div>
        </div>
    </div>
</div>
