{{-- PDF Model Card - Complete Model on one or few pages --}}
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

        {{-- Casts Table - Same card --}}
        @if (!empty($item['casts']))
            <div style="margin-bottom: 8px; border-top: 2px solid #4f46e5; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    CASTS ({{ count($item['casts']) }})
                </div>
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
        @endif

        {{-- Relations Table - Same card --}}
        @if (!empty($item['relations']))
            <div style="margin-bottom: 8px; border-top: 2px solid #059669; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    RELATIONS ({{ count($item['relations']) }})
                </div>
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
        @endif

        {{-- Scopes - Same card --}}
        @if (!empty($item['scopes']))
            <div style="margin-bottom: 8px; border-top: 2px solid #dc2626; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    QUERY SCOPES ({{ count($item['scopes']) }})
                </div>
                @foreach($item['scopes'] as $scope)
                    <div style="background: #f8fafc; padding: 4px 6px; border-radius: 2px; margin-bottom: 3px; font-size: 8px; border: 1px solid #e5e7eb;">
                        <span style="font-family: monospace; color: #7c3aed; font-weight: bold;">{{ $scope['name'] }}({{ implode(', ', $scope['parameters']) }})</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Boot Hooks - Same card --}}
        @if (!empty($item['booted_hooks']))
            <div style="margin-bottom: 8px; border-top: 2px solid #f59e0b; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    BOOT HOOKS ({{ count($item['booted_hooks']) }})
                </div>
                <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                    @foreach ($item['booted_hooks'] as $index => $hook)
                        <span style="background: #fed7aa; color: #c2410c; padding: 1px 4px; border-radius: 1px; margin-right: 3px; margin-bottom: 2px; display: inline-block; font-weight: bold;">{{ $hook }}</span>@if($index < count($item['booted_hooks']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Methods - Same card, limited --}}
        @if (!empty($item['methods']))
            <div style="margin-bottom: 8px; border-top: 2px solid #8b5cf6; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    METHODS ({{ count($item['methods']) }})
                </div>
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
                        @foreach (array_slice($item['methods'], 0, 15) as $method)
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
                        @if (count($item['methods']) > 15)
                            <tr>
                                <td colspan="4" style="text-align: center; font-style: italic; color: #6b7280; font-size: 7px;">
                                    ... and {{ count($item['methods']) - 15 }} more methods
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
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