<!-- Services Page -->
<div id="services" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🔧 Business Services</h2>
        </div>
        <div class="card-body">
            @if(isset($data['services']['data']) && is_array($data['services']['data']) && count($data['services']['data']) > 0)
                @foreach($data['services']['data'] as $key => $service)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($service['class_name']) }}</h3>
                        <small>{{ $service['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($service['methods']) && is_array($service['methods']) && count($service['methods']) > 0)
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 15px;">
                            @foreach($service['methods'] as $methodKey => $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #17a2b8;">
                                <div style="margin-bottom: 10px;">
                                    <strong style="font-size: 1.1em;">{{ $method['name'] ?? 'unknown' }}()</strong>
                                    @if(isset($method['visibility']))
                                        <span style="background: {{ $method['visibility'] === 'public' ? '#28a745' : ($method['visibility'] === 'protected' ? '#ffc107' : '#6c757d') }}; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; margin-left: 8px;">{{ ucfirst($method['visibility']) }}</span>
                                    @endif
                                    @if(isset($method['is_static']) && $method['is_static'])
                                        <span style="background: #6f42c1; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; margin-left: 8px;">Static</span>
                                    @endif
                                </div>
                                
                                @if(isset($method['parameters']) && is_array($method['parameters']) && count($method['parameters']) > 0)
                                <div style="margin-bottom: 10px;">
                                    <small style="color: #6c757d; font-weight: bold;">Parameters:</small>
                                    <ul style="margin: 5px 0 0 20px; font-size: 0.9em;">
                                        @foreach($method['parameters'] as $param)
                                        <li>
                                            <code>${{ $param['name'] }}</code>: {{ $param['type'] ?? 'mixed' }}
                                            @if(isset($param['optional']) && $param['optional'])
                                                <span style="background: #ffc107; color: #212529; padding: 1px 4px; border-radius: 8px; font-size: 0.7em;">optional</span>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                
                                @if(isset($method['return_type']) && $method['return_type'])
                                <div style="margin-bottom: 10px;">
                                    <small style="color: #6c757d; font-weight: bold;">Returns:</small>
                                    <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px; margin-left: 5px;">{{ $method['return_type'] }}</code>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                            <p><em>No methods found</em></p>
                        @endif
                        
                        @if(isset($service['dependencies']) && is_array($service['dependencies']) && count($service['dependencies']) > 0)
                        <h4>Dependencies</h4>
                        <div class="flow">
                            @foreach($service['dependencies'] as $dependency)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ class_basename($dependency) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($service['interfaces']) && is_array($service['interfaces']) && count($service['interfaces']) > 0)
                        <h4>Implements</h4>
                        <div style="margin: 10px 0;">
                            @foreach($service['interfaces'] as $interface)
                                <span style="background: #6f42c1; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.85em; margin-right: 8px; margin-bottom: 4px; display: inline-block;">{{ class_basename($interface) }}</span>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($service['traits']) && is_array($service['traits']) && count($service['traits']) > 0)
                        <h4>Uses Traits</h4>
                        <div style="margin: 10px 0;">
                            @foreach($service['traits'] as $trait)
                                <span style="background: #fd7e14; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.85em; margin-right: 8px; margin-bottom: 4px; display: inline-block;">{{ class_basename($trait) }}</span>
                            @endforeach
                        </div>
                        @endif
                        
                        <!-- Class Information -->
                        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                            <h5 style="color: #6c757d; margin-bottom: 10px;">Class Information</h5>
                            <div style="display: grid; gap: 5px; font-size: 0.9em; color: #6c757d;">
                                <div><strong>Namespace:</strong> {{ $service['namespace'] ?? 'Unknown' }}</div>
                                @if(isset($service['parent_class']) && $service['parent_class'])
                                <div><strong>Extends:</strong> {{ class_basename($service['parent_class']) }}</div>
                                @endif
                                @if(isset($service['is_abstract']) && $service['is_abstract'])
                                <div><strong>Type:</strong> Abstract Class</div>
                                @elseif(isset($service['is_interface']) && $service['is_interface'])
                                <div><strong>Type:</strong> Interface</div>
                                @else
                                <div><strong>Type:</strong> Concrete Class</div>
                                @endif
                            </div>
                        </div>
                        
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
