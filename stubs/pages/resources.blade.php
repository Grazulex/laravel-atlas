<!-- Resources Page -->
<div id="resources" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🎯 API Resources</h2>
        </div>
        <div class="card-body">
            @if(isset($data['resources']))
                @foreach($data['resources'] as $resource)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($resource['class_name']) }}</h3>
                        <small>{{ $resource['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($resource['type']))
                        <h4>Resource Type</h4>
                        <span class="badge bg-primary">{{ ucfirst($resource['type']) }}</span>
                        @endif

                        @if(isset($resource['model']) && $resource['model'])
                        <h4>Associated Model</h4>
                        <code>{{ $resource['model'] }}</code>
                        @endif

                        @if(isset($resource['methods']) && is_array($resource['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($resource['methods'] as $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #28a745;">
                                <strong>{{ $method['name'] }}()</strong>
                                @if(isset($method['parameters']) && !empty($method['parameters']))
                                    <br><small>Parameters:</small>
                                    @foreach($method['parameters'] as $param)
                                        <br><span class="badge bg-light text-dark">{{ $param['type'] ?? 'mixed' }} ${{ $param['name'] }}</span>
                                    @endforeach
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($resource['relationships']) && !empty($resource['relationships']))
                        <h4>Relationships</h4>
                        <ul>
                            @foreach($resource['relationships'] as $relationship)
                                <li><strong>{{ $relationship['name'] }}</strong> ({{ $relationship['type'] ?? 'unknown' }})</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No resources found.</p>
            @endif
        </div>
    </div>
</div>
