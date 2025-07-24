{{-- PDF Action Card - Complete Action on one or few pages --}}
<div class="card">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title">
            ACTION: {{ class_basename($item['class'] ?? 'Unknown') }}
            @if (!empty($item['parameters']))
                <span style="font-size: 8px; background: #f3f4f6; color: #6b7280; padding: 1px 4px; border-radius: 2px; margin-left: 6px;">
                    {{ count($item['parameters']) }} params
                </span>
            @endif
        </div>
        <div class="card-subtitle">{{ $item['namespace'] ?? dirname(str_replace('\\', '/', $item['class'] ?? '')) }}</div>
    </div>

    <div class="card-content">
        {{-- Key Properties (Summary) - Compact --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-bottom: 8px; padding: 6px; background: #f8fafc; border-radius: 3px; border: 1px solid #e5e7eb;">
            <div class="property-item">
                <div class="property-label">Parameters</div>
                <div class="property-value">{{ !empty($item['parameters']) ? count($item['parameters']) : 0 }} params</div>
            </div>
            <div class="property-item">
                <div class="property-label">Return Type</div>
                <div class="property-value">{{ $item['return_type'] ?? 'mixed' }}</div>
            </div>
            <div class="property-item">
                <div class="property-label">Async</div>
                <div class="property-value">{{ !empty($item['is_async']) ? 'Yes' : 'No' }}</div>
            </div>
        </div>

        {{-- Description Section --}}
        @if (!empty($item['description']))
            <div style="margin-bottom: 8px; padding: 6px; background: #f0f9ff; border-radius: 3px; border: 1px solid #0ea5e9;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px;">
                    DESCRIPTION
                </div>
                <div style="font-size: 8px; color: #1e40af; line-height: 1.3;">
                    {{ $item['description'] }}
                </div>
            </div>
        @endif

        {{-- Parameters Table - Same card --}}
        @if (!empty($item['parameters']))
            <div style="margin-bottom: 8px; border-top: 2px solid #4f46e5; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    PARAMETERS ({{ count($item['parameters']) }})
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc;">Name</th>
                            <th style="background: #f8fafc;">Type</th>
                            <th style="background: #f8fafc;">Required</th>
                            <th style="background: #f8fafc;">Default</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item['parameters'] as $param)
                            <tr>
                                <td style="font-family: monospace; color: #1d4ed8; font-weight: bold; font-size: 7px;">{{ $param['name'] ?? 'N/A' }}</td>
                                <td style="font-family: monospace; color: #7c3aed; font-size: 7px;">{{ $param['type'] ?? 'mixed' }}</td>
                                <td style="font-weight: bold; font-size: 7px;">{{ !empty($param['required']) ? 'Yes' : 'No' }}</td>
                                <td style="font-size: 7px;">{{ $param['default'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Validation Rules - Same card --}}
        @if (!empty($item['validation_rules']))
            <div style="margin-bottom: 8px; border-top: 2px solid #059669; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    VALIDATION RULES ({{ count($item['validation_rules']) }})
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc;">Field</th>
                            <th style="background: #f8fafc;">Rules</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item['validation_rules'] as $field => $rules)
                            <tr>
                                <td style="font-family: monospace; color: #1d4ed8; font-weight: bold; font-size: 7px;">{{ $field }}</td>
                                <td style="font-size: 7px;">{{ is_array($rules) ? implode(', ', $rules) : $rules }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Dependencies - Same card --}}
        @if (!empty($item['dependencies']))
            <div style="margin-bottom: 8px; border-top: 2px solid #dc2626; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    DEPENDENCIES ({{ count($item['dependencies']) }})
                </div>
                <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                    @foreach ($item['dependencies'] as $index => $dependency)
                        <span style="background: #fee2e2; color: #991b1b; padding: 1px 3px; border-radius: 1px; margin-right: 2px; margin-bottom: 1px; display: inline-block; font-weight: bold;">{{ class_basename($dependency) }}</span>@if($index < count($item['dependencies']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Footer with file info --}}
    <div class="card-footer">
        <div class="footer-info">
            <span style="font-weight: bold;">FILE: {{ basename($item['file'] ?? 'N/A') }}</span>
            <span style="font-weight: bold;">CLASS: {{ $item['class'] ?? 'Unknown' }}</span>
        </div>
    </div>
</div>
