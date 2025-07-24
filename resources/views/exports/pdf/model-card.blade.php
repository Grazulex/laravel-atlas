{{-- PDF Model Card with intelligent page breaks --}}
<div class="card no-break">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title">
            MODEL: {{ class_basename($item['class']) }}
            @if (!empty($item['table']))
                <span style="font-size: 9px; background: #f3f4f6; color: #6b7280; padding: 2px 6px; border-radius: 3px; margin-left: 8px;">
                    Table: {{ $item['table'] }}
                </span>
            @endif
        </div>
        <div class="card-subtitle">{{ $item['namespace'] }}</div>
        @if (!empty($item['description']))
            <div style="margin-top: 4px; font-size: 9px; font-style: italic; color: #6b7280; background: #f8fafc; padding: 4px 6px; border-radius: 3px;">
                {{ $item['description'] }}
            </div>
        @endif
    </div>

    <div class="card-content">
        {{-- Key Properties (Summary) --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 12px; padding: 8px; background: #f8fafc; border-radius: 4px; border: 1px solid #e5e7eb;">
            <div class="property-item">
                <div class="property-label">Primary Key</div>
                <div class="property-value">{{ $item['primary_key'] ?? 'id' }}</div>
            </div>
            <div class="property-item">
                <div class="property-label">Fillable Fields</div>
                <div class="property-value">{{ !empty($item['fillable']) ? count($item['fillable']) : 0 }} fields</div>
            </div>
            <div class="property-item">
                <div class="property-label">Relations</div>
                <div class="property-value">{{ !empty($item['relations']) ? count($item['relations']) : 0 }} relations</div>
            </div>
        </div>

        {{-- Fillable Fields --}}
        @if (!empty($item['fillable']))
            <div style="margin-bottom: 10px; page-break-inside: avoid;">
                <div style="font-weight: bold; font-size: 10px; color: #374151; margin-bottom: 4px; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px;">
                    FILLABLE FIELDS ({{ count($item['fillable']) }})
                </div>
                <div style="background: #f8fafc; padding: 6px; border-radius: 3px; font-size: 8px; border: 1px solid #e5e7eb;">
                    @foreach ($item['fillable'] as $index => $field)
                        <span style="background: #dbeafe; color: #1d4ed8; padding: 1px 4px; border-radius: 2px; margin-right: 3px; margin-bottom: 2px; display: inline-block; font-weight: bold;">{{ $field }}</span>@if($index < count($item['fillable']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Guarded Fields --}}
        @if (!empty($item['guarded']))
            <div style="margin-bottom: 10px; page-break-inside: avoid;">
                <div style="font-weight: bold; font-size: 10px; color: #374151; margin-bottom: 4px; border-bottom: 1px solid #e5e7eb; padding-bottom: 2px;">
                    GUARDED FIELDS ({{ count($item['guarded']) }})
                </div>
                <div style="background: #f8fafc; padding: 6px; border-radius: 3px; font-size: 8px; border: 1px solid #e5e7eb;">
                    @foreach ($item['guarded'] as $index => $field)
                        <span style="background: #fecaca; color: #b91c1c; padding: 1px 4px; border-radius: 2px; margin-right: 3px; margin-bottom: 2px; display: inline-block; font-weight: bold;">{{ $field }}</span>@if($index < count($item['guarded']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Casts Table (Separate card with clear separation) --}}
@if (!empty($item['casts']))
    <div class="card no-break" style="margin-top: 12px; border-top: 3px solid #4f46e5;">
        <div class="card-header" style="background: #eef2ff;">
            <div class="card-title">CASTS ({{ count($item['casts']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }} - Type Casting</div>
        </div>
        <div class="card-content">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="background: #f8fafc;">Field</th>
                        <th style="background: #f8fafc;">Cast Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['casts'] as $field => $type)
                        <tr>
                            <td style="font-family: monospace; color: #1d4ed8; font-weight: bold;">{{ $field }}</td>
                            <td style="font-weight: bold;">{{ $type }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Relations Table (Separate card with clear separation) --}}
@if (!empty($item['relations']))
    <div class="card no-break" style="margin-top: 12px; border-top: 3px solid #059669;">
        <div class="card-header" style="background: #ecfdf5;">
            <div class="card-title">RELATIONS ({{ count($item['relations']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }} - Model Relationships</div>
        </div>
        <div class="card-content">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="background: #f8fafc;">Relation Name</th>
                        <th style="background: #f8fafc;">Type</th>
                        <th style="background: #f8fafc;">Target Model</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['relations'] as $name => $rel)
                        <tr>
                            <td style="font-family: monospace; color: #1d4ed8; font-weight: bold;">{{ $name }}</td>
                            <td style="font-weight: bold;">{{ $rel['type'] }}</td>
                            <td style="font-family: monospace; color: #7c3aed; font-weight: bold;">{{ class_basename($rel['related']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Scopes (Separate card with clear separation) --}}
@if (!empty($item['scopes']))
    <div class="card no-break" style="margin-top: 12px; border-top: 3px solid #dc2626;">
        <div class="card-header" style="background: #fef2f2;">
            <div class="card-title">QUERY SCOPES ({{ count($item['scopes']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }} - Custom Query Scopes</div>
        </div>
        <div class="card-content">
            @foreach($item['scopes'] as $scope)
                <div style="background: #f8fafc; padding: 6px 8px; border-radius: 3px; margin-bottom: 4px; font-size: 9px; border: 1px solid #e5e7eb;">
                    <span style="font-family: monospace; color: #7c3aed; font-weight: bold;">{{ $scope['name'] }}({{ implode(', ', $scope['parameters']) }})</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Boot Hooks (Separate card with clear separation) --}}
@if (!empty($item['booted_hooks']))
    <div class="card no-break" style="margin-top: 12px; border-top: 3px solid #f59e0b;">
        <div class="card-header" style="background: #fffbeb;">
            <div class="card-title">BOOT HOOKS ({{ count($item['booted_hooks']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }} - Model Event Hooks</div>
        </div>
        <div class="card-content">
            <div style="background: #f8fafc; padding: 6px; border-radius: 3px; font-size: 8px; border: 1px solid #e5e7eb;">
                @foreach ($item['booted_hooks'] as $index => $hook)
                    <span style="background: #fed7aa; color: #c2410c; padding: 2px 6px; border-radius: 2px; margin-right: 4px; margin-bottom: 3px; display: inline-block; font-weight: bold;">{{ $hook }}</span>@if($index < count($item['booted_hooks']) - 1) @endif
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Methods (Separate card with clear separation) --}}
@if (!empty($item['methods']))
    <div class="card no-break" style="margin-top: 12px; border-top: 3px solid #8b5cf6;">
        <div class="card-header" style="background: #f5f3ff;">
            <div class="card-title">METHODS ({{ count($item['methods']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }} - Class Methods</div>
        </div>
        <div class="card-content">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="background: #f8fafc;">Method Name</th>
                        <th style="background: #f8fafc;">Visibility</th>
                        <th style="background: #f8fafc;">Source</th>
                        <th style="background: #f8fafc;">Parameters</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['methods'] as $method)
                        <tr>
                            <td style="font-family: monospace; color: #1d4ed8; font-weight: bold;">{{ $method['name'] }}</td>
                            <td style="font-weight: bold;">{{ $method['visibility'] ?? 'public' }}</td>
                            <td style="font-weight: bold;">{{ $method['source'] ?? 'class' }}</td>
                            <td style="font-size: 8px;">
                                @if (!empty($method['parameters']))
                                    {{ implode(', ', array_map(function($p) { return $p['name']; }, $method['parameters'])) }}
                                @else
                                    (none)
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Footer with file info (separate clear footer) --}}
<div class="card-footer" style="margin-top: 8px; border-top: 2px solid #e5e7eb; background: #f8fafc;">
    <div class="footer-info">
        <span style="font-weight: bold;">FILE: {{ basename($item['file'] ?? '') }}</span>
        <span style="font-weight: bold;">CLASS: {{ $item['class'] }}</span>
    </div>
</div>