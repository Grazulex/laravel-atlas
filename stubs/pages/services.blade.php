<!-- Services Page -->
<div id="services" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🔧 Business Services</h2>
        </div>
        <div class="card-body">
            @if(isset($data['services']))
                @foreach($data['services'] as $service)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($service['class_name']) }}</h3>
                        <small>{{ $service['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($service['methods']))
                        <h4>Methods</h4>
                        @if(is_array($service['methods']) && count($service['methods']) > 0)
                            @if(is_numeric(array_keys($service['methods'])[0]))
                                <p><em>{{ count($service['methods']) }} methods found</em></p>
                            @else
                                @foreach($service['methods'] as $method => $details)
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                                    <strong>{{ is_array($method) ? implode(', ', $method) : $method }}()</strong>
                                    @if(isset($details['dependencies']))
                                    <br><small><strong>Dependencies:</strong> {{ implode(', ', array_map('class_basename', $details['dependencies'])) }}</small>
                                    @endif
                                    @if(isset($details['returns']))
                                    <br><small><strong>Returns:</strong> {{ is_array($details['returns']) ? implode(', ', $details['returns']) : $details['returns'] }}</small>
                                    @endif
                                </div>
                                @endforeach
                            @endif
                        @else
                            <p><em>No method details available</em></p>
                        @endif
                        @endif
                        
                        @if(isset($service['connected_to']))
                        <div class="component-connections">
                            @foreach($service['connected_to'] as $type => $components)
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
                <p>No services found.</p>
            @endif
        </div>
    </div>
</div>
