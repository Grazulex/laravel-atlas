<!-- Controllers Page -->
<div id="controllers" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🎮 HTTP Controllers</h2>
        </div>
        <div class="card-body">
            @if(isset($data['controllers']))
                @foreach($data['controllers'] as $controller)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($controller['class_name']) }}</h3>
                        <small>{{ $controller['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($controller['methods']))
                        <h4>Methods</h4>
                        @if(is_array($controller['methods']) && count($controller['methods']) > 0)
                            @if(is_numeric(array_keys($controller['methods'])[0]))
                                <p><em>{{ count($controller['methods']) }} methods found</em></p>
                            @else
                                @foreach($controller['methods'] as $method => $details)
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                                    <strong>{{ is_array($method) ? implode(', ', $method) : $method }}()</strong>
                                    @if(isset($details['dependencies']))
                                    <br><small><strong>Dependencies:</strong> {{ implode(', ', array_map('class_basename', $details['dependencies'])) }}</small>
                                    @endif
                                    @if(isset($details['events']))
                                    <br><small><strong>Events:</strong> {{ implode(', ', array_map('class_basename', $details['events'])) }}</small>
                                    @endif
                                </div>
                                @endforeach
                            @endif
                        @else
                            <p><em>No method details available</em></p>
                        @endif
                        @endif
                        
                        @if(isset($controller['connected_to']))
                        <div class="component-connections">
                            @foreach($controller['connected_to'] as $type => $components)
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
                <p>No controllers found.</p>
            @endif
        </div>
    </div>
</div>
