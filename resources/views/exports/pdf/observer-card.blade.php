{{-- PDF Observer Card - Complete Observer on one or few pages --}}
<div class="card">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title">
            OBSERVER: {{ class_basename($item['class'] ?? 'Unknown') }}
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
                <div class="property-label">Events</div>
                <div class="property-value">{{ !empty($item['events']) ? count($item['events']) : 0 }} events</div>
            </div>
            <div class="property-item">
                <div class="property-label">Methods</div>
                <div class="property-value">{{ !empty($item['methods']) ? count($item['methods']) : 0 }} methods</div>
            </div>
        </div>

        {{-- Observed Events - Compact --}}
        @if (!empty($item['events']))
            <div style="margin-bottom: 8px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    OBSERVED EVENTS ({{ count($item['events']) }})
                </div>
                <div style="background: #f8fafc; padding: 4px; border-radius: 2px; font-size: 7px; border: 1px solid #e5e7eb;">
                    @foreach ($item['events'] as $index => $event)
                        <span style="background: #fef3c7; color: #92400e; padding: 1px 3px; border-radius: 1px; margin-right: 2px; margin-bottom: 1px; display: inline-block; font-weight: bold;">{{ $event }}</span>@if($index < count($item['events']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Event Methods Table - Same card --}}
        @if (!empty($item['methods']))
            <div style="margin-bottom: 8px; border-top: 2px solid #f59e0b; padding-top: 6px;">
                <div style="font-weight: bold; font-size: 9px; color: #374151; margin-bottom: 3px; border-bottom: 1px solid #e5e7eb; padding-bottom: 1px;">
                    EVENT HANDLERS ({{ count($item['methods']) }})
                </div>
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc;">Method</th>
                            <th style="background: #f8fafc;">Event</th>
                            <th style="background: #f8fafc;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item['methods'] as $method)
                            <tr>
                                <td style="font-family: monospace; color: #1d4ed8; font-weight: bold; font-size: 7px;">{{ $method['name'] ?? 'N/A' }}</td>
                                <td style="font-size: 7px; font-weight: bold;">
                                    @switch($method['name'] ?? '')
                                        @case('creating')
                                            creating
                                            @break
                                        @case('created')
                                            created
                                            @break
                                        @case('updating')
                                            updating
                                            @break
                                        @case('updated')
                                            updated
                                            @break
                                        @case('saving')
                                            saving
                                            @break
                                        @case('saved')
                                            saved
                                            @break
                                        @case('deleting')
                                            deleting
                                            @break
                                        @case('deleted')
                                            deleted
                                            @break
                                        @case('restoring')
                                            restoring
                                            @break
                                        @case('restored')
                                            restored
                                            @break
                                        @default
                                            custom
                                    @endswitch
                                </td>
                                <td style="font-size: 7px;">
                                    @switch($method['name'] ?? '')
                                        @case('creating')
                                            Before model creation
                                            @break
                                        @case('created')
                                            After model creation
                                            @break
                                        @case('updating')
                                            Before model update
                                            @break
                                        @case('updated')
                                            After model update
                                            @break
                                        @case('saving')
                                            Before save (create/update)
                                            @break
                                        @case('saved')
                                            After save (create/update)
                                            @break
                                        @case('deleting')
                                            Before model deletion
                                            @break
                                        @case('deleted')
                                            After model deletion
                                            @break
                                        @case('restoring')
                                            Before soft delete restore
                                            @break
                                        @case('restored')
                                            After soft delete restore
                                            @break
                                        @default
                                            Custom event handler
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
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
