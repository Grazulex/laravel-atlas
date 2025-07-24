{{-- PDF Command Card - Complete Command on one or few pages --}}
<div class="card">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title">
            COMMAND: {{ $item['signature'] ?? $item['name'] ?? 'N/A' }}
            @if (!empty($item['arguments']) || !empty($item['options']))
                <span style="font-size: 8px; background: #f3f4f6; color: #6b7280; padding: 1px 4px; border-radius: 2px; margin-left: 6px;">
                    {{ count($item['arguments'] ?? []) }} args, {{ count($item['options'] ?? []) }} opts
                </span>
            @endif
        </div>
        <div class="card-subtitle">{{ class_basename($item['class'] ?? 'Unknown') }}</div>
    </div>

    <div class="card-content">
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

        {{-- Key Properties (Summary) - Compact --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-bottom: 8px; padding: 6px; background: #f8fafc; border-radius: 3px; border: 1px solid #e5e7eb;">
            <div class="property-item">
                <div class="property-label">Arguments</div>
                <div class="property-value">{{ !empty($item['arguments']) ? count($item['arguments']) : 0 }} args</div>
            </div>
            <div class="property-item">
                <div class="property-label">Options</div>
                <div class="property-value">{{ !empty($item['options']) ? count($item['options']) : 0 }} options</div>
            </div>
            <div class="property-item">
                <div class="property-label">Hidden</div>
                <div class="property-value">{{ !empty($item['hidden']) ? 'Yes' : 'No' }}</div>
            </div>
        </div>

        {{-- Arguments Table - Same card --}}
        @if (!empty($item['arguments']))
            <div style="margin-bottom: 8px; border-top: 2px solid #4f46e5; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    ARGUMENTS ({{ count($item['arguments']) }})
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc;">Name</th>
                            <th style="background: #f8fafc;">Required</th>
                            <th style="background: #f8fafc;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item['arguments'] as $arg)
                            <tr>
                                <td style="font-family: monospace; color: #1d4ed8; font-weight: bold; font-size: 7px;">{{ $arg['name'] ?? 'N/A' }}</td>
                                <td style="font-weight: bold; font-size: 7px;">{{ !empty($arg['required']) ? 'Yes' : 'No' }}</td>
                                <td style="font-size: 7px;">{{ $arg['description'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Options Table - Same card --}}
        @if (!empty($item['options']))
            <div style="margin-bottom: 8px; border-top: 2px solid #059669; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    OPTIONS ({{ count($item['options']) }})
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc;">Name</th>
                            <th style="background: #f8fafc;">Shortcut</th>
                            <th style="background: #f8fafc;">Required</th>
                            <th style="background: #f8fafc;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item['options'] as $option)
                            <tr>
                                <td style="font-family: monospace; color: #1d4ed8; font-weight: bold; font-size: 7px;">{{ $option['name'] ?? 'N/A' }}</td>
                                <td style="font-family: monospace; color: #7c3aed; font-weight: bold; font-size: 7px;">{{ $option['shortcut'] ?? '-' }}</td>
                                <td style="font-weight: bold; font-size: 7px;">{{ !empty($option['required']) ? 'Yes' : 'No' }}</td>
                                <td style="font-size: 7px;">{{ $option['description'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Usage Examples - Same card --}}
        @if (!empty($item['usage_examples']))
            <div style="margin-bottom: 8px; border-top: 2px solid #dc2626; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    USAGE EXAMPLES
                </div>
                @foreach($item['usage_examples'] as $example)
                    <div style="background: #f8fafc; padding: 4px 6px; border-radius: 2px; margin-bottom: 3px; font-size: 7px; border: 1px solid #e5e7eb;">
                        <span style="font-family: monospace; color: #dc2626; font-weight: bold;">{{ $example }}</span>
                    </div>
                @endforeach
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
