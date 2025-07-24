{{-- PDF Policy Card - Complete Policy on one or few pages --}}
<div class="card">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title">
            POLICY: {{ class_basename($item['class'] ?? 'Unknown') }}
            @if (!empty($item['model']))
                <span style="font-size: 8px; background: #f3f4f6; color: #6b7280; padding: 1px 4px; border-radius: 2px; margin-left: 6px;">
                    Model: {{ class_basename($item['model']) }}
                </span>
            @endif
        </div>
        <div class="card-subtitle">{{ $item['namespace'] ?? dirname(str_replace('\\', '/', $item['class'] ?? '')) }}</div>
    </div>

    <div class="card-content">
        {{-- Key Properties (Summary) - Compact --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-bottom: 8px; padding: 6px; background: #f8fafc; border-radius: 3px; border: 1px solid #e5e7eb;">
            <div class="property-item">
                <div class="property-label">Model</div>
                <div class="property-value">{{ class_basename($item['model'] ?? 'N/A') }}</div>
            </div>
            <div class="property-item">
                <div class="property-label">Methods</div>
                <div class="property-value">{{ !empty($item['methods']) ? count($item['methods']) : 0 }} methods</div>
            </div>
            <div class="property-item">
                <div class="property-label">Auto-discovery</div>
                <div class="property-value">{{ !empty($item['auto_discovery']) ? 'Yes' : 'No' }}</div>
            </div>
        </div>

        {{-- Authorization Methods Table - Same card --}}
        @if (!empty($item['methods']))
            <div style="margin-bottom: 8px; border-top: 2px solid #7c3aed; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    AUTHORIZATION METHODS ({{ count($item['methods']) }})
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc;">Method</th>
                            <th style="background: #f8fafc;">Purpose</th>
                            <th style="background: #f8fafc;">Parameters</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item['methods'] as $method)
                            <tr>
                                <td style="font-family: monospace; color: #1d4ed8; font-weight: bold; font-size: 7px;">{{ $method['name'] ?? 'N/A' }}</td>
                                <td style="font-size: 7px;">
                                    @switch($method['name'] ?? '')
                                        @case('viewAny')
                                            List all resources
                                            @break
                                        @case('view')
                                            View specific resource
                                            @break
                                        @case('create')
                                            Create new resource
                                            @break
                                        @case('update')
                                            Update resource
                                            @break
                                        @case('delete')
                                            Delete resource
                                            @break
                                        @case('restore')
                                            Restore deleted resource
                                            @break
                                        @case('forceDelete')
                                            Permanently delete
                                            @break
                                        @default
                                            Custom authorization
                                    @endswitch
                                </td>
                                <td style="font-size: 7px;">
                                    {{ !empty($method['parameters']) ? count($method['parameters']) : '0' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Gates - Same card --}}
        @if (!empty($item['gates']))
            <div style="margin-bottom: 8px; border-top: 2px solid #059669; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    GATES ({{ count($item['gates']) }})
                </div>
                <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                    @foreach ($item['gates'] as $index => $gate)
                        <span style="background: #dcfce7; color: #166534; padding: 1px 3px; border-radius: 1px; margin-right: 2px; margin-bottom: 1px; display: inline-block; font-weight: bold;">{{ $gate }}</span>@if($index < count($item['gates']) - 1) @endif
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
