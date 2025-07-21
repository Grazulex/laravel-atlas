<!-- Observers Page -->
<div id="observers" class="page">
    <div class="card">
        <div class="card-header">
            <h2>👁️ Model Observers</h2>
        </div>
        <div class="card-body">
            @if(isset($data['observers']))
                @foreach($data['observers'] as $observer)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($observer['class_name']) }}</h3>
                        <small>Observes: {{ class_basename($observer['model']) }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($observer['methods']) && is_array($observer['methods']))
                        <h4>Lifecycle Hooks</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                            @foreach($observer['methods'] as $method)
                            <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid {{ $method['type'] == 'before' ? '#ffc107' : '#28a745' }};">
                                <strong>{{ $method['name'] }}()</strong>
                                <br><small>
                                    <span class="badge badge-{{ $method['type'] == 'before' ? 'warning' : 'success' }}">
                                        {{ ucfirst($method['type']) }}
                                    </span>
                                    {{ $method['type'] == 'before' ? 'Pre-action hook' : 'Post-action hook' }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                        @else
                            <p><em>No lifecycle hooks found</em></p>
                        @endif
                        
                        @if(isset($observer['dependencies']) && count($observer['dependencies']) > 0)
                        <h4>Dependencies</h4>
                        <ul>
                            @foreach($observer['dependencies'] as $dependency)
                            <li>{{ class_basename($dependency) }}</li>
                            @endforeach
                        </ul>
                        @endif
                        
                        @if(isset($observer['events']) && count($observer['events']) > 0)
                        <h4>Events Triggered</h4>
                        <ul>
                            @foreach($observer['events'] as $event)
                            <li>{{ class_basename($event) }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No observers found.</p>
            @endif
        </div>
    </div>
</div>
