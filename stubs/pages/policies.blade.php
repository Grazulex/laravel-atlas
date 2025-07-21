<!-- Policies Page -->
<div id="policies" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🛡️ Authorization Policies</h2>
        </div>
        <div class="card-body">
            @if(isset($data['policies']))
                @foreach($data['policies'] as $policy)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($policy['class_name']) }}</h3>
                        <small>Model: {{ class_basename($policy['model']) }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($policy['methods']))
                        @if(is_array($policy['methods']) && count($policy['methods']) > 0)
                            @if(isset($policy['methods'][0]['name']))
                                <p><strong>Methods:</strong> 
                                @foreach($policy['methods'] as $method)
                                    {{ $method['name'] ?? 'unknown' }}@if(!$loop->last), @endif
                                @endforeach
                                </p>
                            @else
                                <p><strong>Methods:</strong> {{ implode(', ', $policy['methods']) }}</p>
                            @endif
                        @else
                            <p><strong>Methods:</strong> <em>No methods available</em></p>
                        @endif
                        @endif
                        
                        @if(isset($policy['connected_to']))
                        <div class="component-connections">
                            @foreach($policy['connected_to'] as $type => $components)
                            <div class="connection-group">
                                <h4>{{ is_array($type) ? 'Mixed' : ucfirst($type) }}</h4>
                                @foreach($components as $component)
                                <span class="connection-item">{{ is_array($component) ? 'Mixed' : class_basename($component) }}</span>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No policies found.</p>
            @endif
        </div>
    </div>
</div>
