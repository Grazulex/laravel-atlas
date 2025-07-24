{{-- PDF Model Card - Main Info (designed to fit in one page) --}}
<div class="card no-break">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title">
            MODEL: {{ class_basename($item['class']) }}
            @if (!empty($item['table']))
                <span style="font-size: 8px; background: #f3f4f6; color: #6b7280; padding: 1px 4px; border-radius: 2px; margin-left: 6px;">
                    Table: {{ $item['table'] }}
                </span>
            @endif
        </div>
        <div class="card-subtitle">{{ $item['namespace'] }}</div>
    </div>

    <div class="card-content">
        {{-- Key Properties (Summary) - Compact --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-bottom: 8px; padding: 6px; background: #f8fafc; border-radius: 3px; border: 1px solid #e5e7eb;">
            <div class="property-item">
                <div class="property-label">Primary Key</div>
                <div class="property-value">{{ $item['primary_key'] ?? 'id' }}</div>
            </div>
            <div class="property-item">
                <div class="property-label">Fillable</div>
                <div class="property-value">{{ !empty($item['fillable']) ? count($item['fillable']) : 0 }} fields</div>
            </div>
            <div class="property-item">
                <div class="property-label">Relations</div>
                <div class="property-value">{{ !empty($item['relations']) ? count($item['relations']) : 0 }} relations</div>
            </div>
        </div>

        {{-- Fillable Fields - Compact --}}
        @if (!empty($item['fillable']))
            <div style="margin-bottom: 8px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    FILLABLE FIELDS ({{ count($item['fillable']) }})
                </div>
                <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                    @foreach ($item['fillable'] as $index => $field)
                        <span style="background: #dbeafe; color: #1d4ed8; padding: 1px 3px; border-radius: 1px; margin-right: 2px; margin-bottom: 1px; display: inline-block; font-weight: bold;">{{ $field }}</span>@if($index < count($item['fillable']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Guarded Fields - Compact --}}
        @if (!empty($item['guarded']))
            <div style="margin-bottom: 8px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    GUARDED FIELDS ({{ count($item['guarded']) }})
                </div>
                <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                    @foreach ($item['guarded'] as $index => $field)
                        <span style="background: #fecaca; color: #b91c1c; padding: 1px 3px; border-radius: 1px; margin-right: 2px; margin-bottom: 1px; display: inline-block; font-weight: bold;">{{ $field }}</span>@if($index < count($item['guarded']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Footer with file info --}}
    <div class="card-footer">
        <div class="footer-info">
            <span style="font-weight: bold;">FILE: {{ basename($item['file'] ?? '') }}</span>
            <span style="font-weight: bold;">CLASS: {{ $item['class'] }}</span>
        </div>
    </div>
</div>

{{-- Casts Table - Separate page --}}
@if (!empty($item['casts']))
    <div class="card section-break no-break" style="border-top: 3px solid #4f46e5;">
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

{{-- Relations Table - Separate page --}}
@if (!empty($item['relations']))
    <div class="card section-break no-break" style="border-top: 3px solid #059669;">
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

{{-- Scopes - Separate page --}}
@if (!empty($item['scopes']))
    <div class="card section-break no-break" style="border-top: 3px solid #dc2626;">
        <div class="card-header" style="background: #fef2f2;">
            <div class="card-title">QUERY SCOPES ({{ count($item['scopes']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }} - Custom Query Scopes</div>
        </div>
        <div class="card-content">
            @foreach($item['scopes'] as $scope)
                <div style="background: #f8fafc; padding: 4px 6px; border-radius: 2px; margin-bottom: 3px; font-size: 8px; border: 1px solid #e5e7eb;">
                    <span style="font-family: monospace; color: #7c3aed; font-weight: bold;">{{ $scope['name'] }}({{ implode(', ', $scope['parameters']) }})</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Boot Hooks - Separate page --}}
@if (!empty($item['booted_hooks']))
    <div class="card section-break no-break" style="border-top: 3px solid #f59e0b;">
        <div class="card-header" style="background: #fffbeb;">
            <div class="card-title">BOOT HOOKS ({{ count($item['booted_hooks']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }} - Model Event Hooks</div>
        </div>
        <div class="card-content">
            <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                @foreach ($item['booted_hooks'] as $index => $hook)
                    <span style="background: #fed7aa; color: #c2410c; padding: 1px 4px; border-radius: 1px; margin-right: 3px; margin-bottom: 2px; display: inline-block; font-weight: bold;">{{ $hook }}</span>@if($index < count($item['booted_hooks']) - 1) @endif
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Methods - Separate page - Limited to fit --}}
@if (!empty($item['methods']))
    <div class="card section-break no-break" style="border-top: 3px solid #8b5cf6;">
        <div class="card-header" style="background: #f5f3ff;">
            <div class="card-title">METHODS ({{ count($item['methods']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }} - Class Methods</div>
        </div>
        <div class="card-content">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="background: #f8fafc;">Method</th>
                        <th style="background: #f8fafc;">Visibility</th>
                        <th style="background: #f8fafc;">Source</th>
                        <th style="background: #f8fafc;">Params</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (array_slice($item['methods'], 0, 25) as $method)
                        <tr>
                            <td style="font-family: monospace; color: #1d4ed8; font-weight: bold; font-size: 7px;">{{ $method['name'] }}</td>
                            <td style="font-weight: bold; font-size: 7px;">{{ substr($method['visibility'] ?? 'public', 0, 3) }}</td>
                            <td style="font-weight: bold; font-size: 7px;">{{ substr($method['source'] ?? 'class', 0, 5) }}</td>
                            <td style="font-size: 6px;">
                                @if (!empty($method['parameters']))
                                    {{ count($method['parameters']) }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if (count($item['methods']) > 25)
                        <tr>
                            <td colspan="4" style="text-align: center; font-style: italic; color: #6b7280; font-size: 7px;">
                                ... and {{ count($item['methods']) - 25 }} more methods
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endif