<!-- Actions Page -->
<div id="actions" class="page">
    <div class="card">
        <div class="card-header">
            <h2>⚡ Business Actions</h2>
        </div>
        <div class="card-body">
            @if(isset($data['actions']))
                @foreach($data['actions'] as $action)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($action['class_name']) }}</h3>
                        <div>
                            <span class="badge badge-{{ $action['type'] ?? 'custom' }}">{{ ucfirst($action['type'] ?? 'custom') }}</span>
                            @if(isset($action['is_invokable']) && $action['is_invokable'])
                                <span class="badge badge-success">Invokable</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($action['methods']) && is_array($action['methods']) && count($action['methods']) > 0)
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($action['methods'] as $methodName => $methodDetails)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid {{ $methodName == '__invoke' ? '#17a2b8' : '#6c757d' }};">
                                <strong>{{ $methodName }}()</strong>
                                @if(isset($methodDetails['parameters']) && count($methodDetails['parameters']) > 0)
                                <br><small><strong>Parameters:</strong></small>
                                <ul style="margin: 5px 0; font-size: 0.9em;">
                                    @foreach($methodDetails['parameters'] as $param)
                                    <li>
                                        <strong>{{ $param['name'] }}</strong>: {{ $param['type'] ?? 'mixed' }}
                                        @if(isset($param['required']) && !$param['required'])
                                            <span class="badge badge-secondary" style="font-size: 0.7em;">optional</span>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                                @if(isset($methodDetails['return_type']))
                                <br><small><strong>Returns:</strong> {{ $methodDetails['return_type'] }}</small>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                            <p><em>No method details found</em></p>
                        @endif
                        
                        @if(isset($action['dependencies']) && count($action['dependencies']) > 0)
                        <h4>Dependencies</h4>
                        <div class="component-connections">
                            <div class="connection-group">
                                <h4>Injected Dependencies</h4>
                                @foreach($action['dependencies'] as $dependency)
                                <span class="connection-item">{{ class_basename($dependency) }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($action['events']) && count($action['events']) > 0)
                        <h4>Events Dispatched</h4>
                        <div class="component-connections">
                            <div class="connection-group">
                                <h4>Events</h4>
                                @foreach($action['events'] as $event)
                                <span class="connection-item">{{ class_basename($event) }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        @if(!isset($action['dependencies']) || count($action['dependencies']) == 0)
                            @if(!isset($action['events']) || count($action['events']) == 0)
                            <p><em>This action has no dependencies or events.</em></p>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No actions found.</p>
            @endif
        </div>
    </div>
</div>