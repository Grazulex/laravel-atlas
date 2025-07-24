{{-- PDF Model Card with intelligent page breaks --}}
<div class="card no-break">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title">
            🧱 {{ class_basename($item['class']) }}
            @if (!empty($item['table']))
                <span style="font-size: 9px; background: #f3f4f6; color: #6b7280; padding: 2px 6px; border-radius: 3px; margin-left: 8px;">
                    {{ $item['table'] }}
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
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 12px; padding: 8px; background: #f8fafc; border-radius: 4px;">
            <div class="property-item">
                <div class="property-label">🆔 Primary Key</div>
                <div class="property-value">{{ $item['primary_key'] ?? 'id' }}</div>
            </div>
            <div class="property-item">
                <div class="property-label">📝 Fillable</div>
                <div class="property-value">{{ !empty($item['fillable']) ? count($item['fillable']) : 0 }} fields</div>
            </div>
            <div class="property-item">
                <div class="property-label">🔗 Relations</div>
                <div class="property-value">{{ !empty($item['relations']) ? count($item['relations']) : 0 }} relations</div>
            </div>
        </div>

        {{-- Fillable Fields --}}
        @if (!empty($item['fillable']))
            <div style="margin-bottom: 10px;" class="no-break">
                <div style="font-weight: bold; font-size: 10px; color: #374151; margin-bottom: 4px;">
                    📝 Fillable Fields ({{ count($item['fillable']) }})
                </div>
                <div style="background: #f8fafc; padding: 6px; border-radius: 3px; font-size: 8px;">
                    @foreach ($item['fillable'] as $index => $field)
                        <span style="background: #dbeafe; color: #1d4ed8; padding: 1px 4px; border-radius: 2px; margin-right: 3px; margin-bottom: 2px; display: inline-block;">{{ $field }}</span>@if($index < count($item['fillable']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Guarded Fields --}}
        @if (!empty($item['guarded']))
            <div style="margin-bottom: 10px;" class="no-break">
                <div style="font-weight: bold; font-size: 10px; color: #374151; margin-bottom: 4px;">
                    ⛔ Guarded Fields ({{ count($item['guarded']) }})
                </div>
                <div style="background: #f8fafc; padding: 6px; border-radius: 3px; font-size: 8px;">
                    @foreach ($item['guarded'] as $index => $field)
                        <span style="background: #fecaca; color: #b91c1c; padding: 1px 4px; border-radius: 2px; margin-right: 3px; margin-bottom: 2px; display: inline-block;">{{ $field }}</span>@if($index < count($item['guarded']) - 1) @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Casts Table (Separate card if needed for page break management) --}}
@if (!empty($item['casts']))
    <div class="card no-break" style="margin-top: 8px;">
        <div class="card-header">
            <div class="card-title">🔣 Casts ({{ count($item['casts']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }}</div>
        </div>
        <div class="card-content">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['casts'] as $field => $type)
                        <tr>
                            <td style="font-family: monospace; color: #1d4ed8;">{{ $field }}</td>
                            <td>{{ $type }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Relations Table (Separate card if needed for page break management) --}}
@if (!empty($item['relations']))
    <div class="card no-break" style="margin-top: 8px;">
        <div class="card-header">
            <div class="card-title">🔗 Relations ({{ count($item['relations']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }}</div>
        </div>
        <div class="card-content">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Target Model</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['relations'] as $name => $rel)
                        <tr>
                            <td style="font-family: monospace; color: #1d4ed8;">{{ $name }}</td>
                            <td>{{ $rel['type'] }}</td>
                            <td style="font-family: monospace; color: #7c3aed;">{{ class_basename($rel['related']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Scopes (Separate card if exists) --}}
@if (!empty($item['scopes']))
    <div class="card no-break" style="margin-top: 8px;">
        <div class="card-header">
            <div class="card-title">🔍 Scopes ({{ count($item['scopes']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }}</div>
        </div>
        <div class="card-content">
            @foreach($item['scopes'] as $scope)
                <div style="background: #f8fafc; padding: 4px 6px; border-radius: 3px; margin-bottom: 3px; font-size: 8px; border: 1px solid #e5e7eb;">
                    <span style="font-family: monospace; color: #7c3aed;">{{ $scope['name'] }}({{ implode(', ', $scope['parameters']) }})</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Boot Hooks (Separate card if exists) --}}
@if (!empty($item['booted_hooks']))
    <div class="card no-break" style="margin-top: 8px;">
        <div class="card-header">
            <div class="card-title">🧷 Boot Hooks ({{ count($item['booted_hooks']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }}</div>
        </div>
        <div class="card-content">
            <div style="background: #f8fafc; padding: 6px; border-radius: 3px; font-size: 8px;">
                @foreach ($item['booted_hooks'] as $index => $hook)
                    <span style="background: #fed7aa; color: #c2410c; padding: 1px 4px; border-radius: 2px; margin-right: 3px; margin-bottom: 2px; display: inline-block;">{{ $hook }}</span>@if($index < count($item['booted_hooks']) - 1) @endif
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Methods (Separate card if exists) --}}
@if (!empty($item['methods']))
    <div class="card no-break" style="margin-top: 8px;">
        <div class="card-header">
            <div class="card-title">⚙️ Methods ({{ count($item['methods']) }})</div>
            <div class="card-subtitle">{{ class_basename($item['class']) }}</div>
        </div>
        <div class="card-content">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Visibility</th>
                        <th>Source</th>
                        <th>Parameters</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['methods'] as $method)
                        <tr>
                            <td style="font-family: monospace; color: #1d4ed8;">{{ $method['name'] }}</td>
                            <td>{{ $method['visibility'] ?? 'public' }}</td>
                            <td>{{ $method['source'] ?? 'class' }}</td>
                            <td style="font-size: 8px;">
                                @if (!empty($method['parameters']))
                                    {{ implode(', ', array_map(function($p) { return $p['name']; }, $method['parameters'])) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Footer with file info --}}
<div class="card-footer">
    <div class="footer-info">
        <span>📁 {{ basename($item['file'] ?? '') }}</span>
        <span>📦 {{ $item['class'] }}</span>
    </div>
</div>