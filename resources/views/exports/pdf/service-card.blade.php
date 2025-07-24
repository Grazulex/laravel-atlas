{{-- PDF Service Card - Complete Service on one or few pages --}}
<div class="card">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title">
            SERVICE: {{ class_basename($item['class'] ?? 'Unknown') }}
            @if (!empty($item['dependencies']))
                <span style="font-size: 8px; background: #f3f4f6; color: #6b7280; padding: 1px 4px; border-radius: 2px; margin-left: 6px;">
                    {{ count($item['dependencies']) }} dependencies
                </span>
            @endif
        </div>
        <div class="card-subtitle">{{ $item['namespace'] ?? dirname(str_replace('\\', '/', $item['class'] ?? '')) }}</div>
    </div>

    <div class="card-content">
        {{-- Key Properties (Summary) - Compact --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-bottom: 8px; padding: 6px; background: #f8fafc; border-radius: 3px; border: 1px solid #e5e7eb;">
            <div class="property-item">
                <div class="property-label">Methods</div>
                <div class="property-value">{{ !empty($item['methods']) ? count($item['methods']) : 0 }} methods</div>
            </div>
            <div class="property-item">
                <div class="property-label">Dependencies</div>
                <div class="property-value">{{ !empty($item['dependencies']) ? count($item['dependencies']) : 0 }} deps</div>
            </div>
            <div class="property-item">
                <div class="property-label">Interfaces</div>
                <div class="property-value">{{ !empty($item['interfaces']) ? count($item['interfaces']) : 0 }} interfaces</div>
            </div>
        </div>

        {{-- Dependencies - Compact --}}
        @if (!empty($item['dependencies']))
            <div style="margin-bottom: 8px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    DEPENDENCIES ({{ count($item['dependencies']) }})
                </div>
                <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                    @foreach ($item['dependencies'] as $index => $dependency)
                        <span style="background: #fef3c7; color: #92400e; padding: 1px 3px; border-radius: 1px; margin-right: 2px; margin-bottom: 1px; display: inline-block; font-weight: bold;">{{ class_basename($dependency) }}</span>@if($index < count($item['dependencies']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Interfaces - Compact --}}
        @if (!empty($item['interfaces']))
            <div style="margin-bottom: 8px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    INTERFACES ({{ count($item['interfaces']) }})
                </div>
                <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                    @foreach ($item['interfaces'] as $index => $interface)
                        <span style="background: #ddd6fe; color: #5b21b6; padding: 1px 3px; border-radius: 1px; margin-right: 2px; margin-bottom: 1px; display: inline-block; font-weight: bold;">{{ class_basename($interface) }}</span>@if($index < count($item['interfaces']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Methods Table - Same card --}}
        @if (!empty($item['methods']))
            <div style="margin-bottom: 8px; border-top: 2px solid #4f46e5; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    METHODS ({{ count($item['methods']) }})
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc;">Method</th>
                            <th style="background: #f8fafc;">Visibility</th>
                            <th style="background: #f8fafc;">Parameters</th>
                            <th style="background: #f8fafc;">Return Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (array_slice($item['methods'], 0, 15) as $method)
                            <tr>
                                <td style="font-family: monospace; color: #1d4ed8; font-weight: bold; font-size: 7px;">{{ $method['name'] ?? 'N/A' }}</td>
                                <td style="font-weight: bold; font-size: 7px;">{{ substr($method['visibility'] ?? 'public', 0, 3) }}</td>
                                <td style="font-size: 6px;">
                                    @if (!empty($method['parameters']))
                                        {{ count($method['parameters']) }}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td style="font-size: 6px; font-family: monospace;">{{ $method['return_type'] ?? 'mixed' }}</td>
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
            <span style="font-weight: bold;">FILE: {{ basename($item['file'] ?? 'N/A') }}</span>
            <span style="font-weight: bold;">CLASS: {{ $item['class'] ?? 'Unknown' }}</span>
        </div>
    </div>
</div>
