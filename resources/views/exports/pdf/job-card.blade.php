{{-- PDF Job Card - Complete Job on one or few pages --}}
<div class="card">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title">
            JOB: {{ class_basename($item['class'] ?? 'Unknown') }}
            @if (!empty($item['queue']))
                <span style="font-size: 8px; background: #f3f4f6; color: #6b7280; padding: 1px 4px; border-radius: 2px; margin-left: 6px;">
                    Queue: {{ $item['queue'] }}
                </span>
            @endif
        </div>
        <div class="card-subtitle">{{ $item['namespace'] ?? dirname(str_replace('\\', '/', $item['class'] ?? '')) }}</div>
    </div>

    <div class="card-content">
        {{-- Key Properties (Summary) - Compact --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin-bottom: 8px; padding: 6px; background: #f8fafc; border-radius: 3px; border: 1px solid #e5e7eb;">
            <div class="property-item">
                <div class="property-label">Queue</div>
                <div class="property-value">{{ $item['queue'] ?? 'default' }}</div>
            </div>
            <div class="property-item">
                <div class="property-label">Tries</div>
                <div class="property-value">{{ $item['tries'] ?? '3' }}</div>
            </div>
            <div class="property-item">
                <div class="property-label">Timeout</div>
                <div class="property-value">{{ $item['timeout'] ?? '60s' }}</div>
            </div>
        </div>

        {{-- Job Configuration - Compact --}}
        @if (!empty($item['delay']) || !empty($item['max_exceptions']) || !empty($item['backoff']))
            <div style="margin-bottom: 8px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    CONFIGURATION
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 4px; font-size: 7px;">
                    @if (!empty($item['delay']))
                        <div><strong>Delay:</strong> {{ $item['delay'] }}</div>
                    @endif
                    @if (!empty($item['max_exceptions']))
                        <div><strong>Max Exceptions:</strong> {{ $item['max_exceptions'] }}</div>
                    @endif
                    @if (!empty($item['backoff']))
                        <div><strong>Backoff:</strong> {{ $item['backoff'] }}</div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Tags - Compact --}}
        @if (!empty($item['tags']))
            <div style="margin-bottom: 8px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    TAGS ({{ count($item['tags']) }})
                </div>
                <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                    @foreach ($item['tags'] as $index => $tag)
                        <span style="background: #ecfdf5; color: #065f46; padding: 1px 3px; border-radius: 1px; margin-right: 2px; margin-bottom: 1px; display: inline-block; font-weight: bold;">{{ $tag }}</span>@if($index < count($item['tags']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Methods Table - Same card --}}
        @if (!empty($item['methods']))
            <div style="margin-bottom: 8px; border-top: 2px solid #059669; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    METHODS ({{ count($item['methods']) }})
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc;">Method</th>
                            <th style="background: #f8fafc;">Visibility</th>
                            <th style="background: #f8fafc;">Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (array_slice($item['methods'], 0, 10) as $method)
                            <tr>
                                <td style="font-family: monospace; color: #1d4ed8; font-weight: bold; font-size: 7px;">{{ $method['name'] ?? 'N/A' }}</td>
                                <td style="font-weight: bold; font-size: 7px;">{{ substr($method['visibility'] ?? 'public', 0, 3) }}</td>
                                <td style="font-size: 7px;">
                                    @if(($method['name'] ?? '') === 'handle')
                                        Main execution
                                    @elseif(($method['name'] ?? '') === 'failed')
                                        Error handling
                                    @elseif(($method['name'] ?? '') === 'retryUntil')
                                        Retry logic
                                    @else
                                        Custom method
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if (count($item['methods']) > 10)
                            <tr>
                                <td colspan="3" style="text-align: center; font-style: italic; color: #6b7280; font-size: 7px;">
                                    ... and {{ count($item['methods']) - 10 }} more methods
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Failure Handling - Same card --}}
        @if (!empty($item['failed_method']) || !empty($item['retry_until']))
            <div style="margin-bottom: 8px; border-top: 2px solid #dc2626; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    FAILURE HANDLING
                </div>
                <div style="font-size: 7px;">
                    @if (!empty($item['failed_method']))
                        <div><strong>Failed Method:</strong> {{ $item['failed_method'] }}</div>
                    @endif
                    @if (!empty($item['retry_until']))
                        <div><strong>Retry Until:</strong> {{ $item['retry_until'] }}</div>
                    @endif
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
