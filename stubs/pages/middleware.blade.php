<!-- Middleware Page -->
<div id="middleware" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🛂 HTTP Middleware</h2>
        </div>
        <div class="card-body">
            @if(isset($data['middleware']))
                @foreach($data['middleware'] as $middleware)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($middleware['class_name']) }}</h3>
                        <small>{{ $middleware['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($middleware['methods']) && is_array($middleware['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($middleware['methods'] as $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #0d6efd;">
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

                        @if(isset($middleware['dependencies']) && !empty($middleware['dependencies']))
                        <h4>Dependencies</h4>
                        <ul>
                            @foreach($middleware['dependencies'] as $dependency)
                                <li><code>{{ $dependency }}</code></li>
                            @endforeach
                        </ul>
                        @endif

                        @if(isset($middleware['priority']))
                        <h4>Priority</h4>
                        <span class="badge bg-info">{{ $middleware['priority'] }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No middleware found.</p>
            @endif
        </div>
    </div>
</div>
