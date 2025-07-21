<!-- Notifications Page -->
<div id="notifications" class="page">
    <div class="card">
        <div class="card-header">
            <h2>📬 Notifications</h2>
        </div>
        <div class="card-body">
            @if(isset($data['notifications']))
                @foreach($data['notifications'] as $notification)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($notification['class_name']) }}</h3>
                        <small>{{ $notification['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($notification['channels']) && !empty($notification['channels']))
                        <h4>Channels</h4>
                        <div style="margin-bottom: 15px;">
                            @foreach($notification['channels'] as $channel)
                                <span class="badge bg-info">{{ $channel }}</span>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($notification['methods']) && is_array($notification['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($notification['methods'] as $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #ffc107;">
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

                        @if(isset($notification['dependencies']) && !empty($notification['dependencies']))
                        <h4>Dependencies</h4>
                        <ul>
                            @foreach($notification['dependencies'] as $dependency)
                                <li><code>{{ $dependency }}</code></li>
                            @endforeach
                        </ul>
                        @endif

                        @if(isset($notification['data_structure']) && !empty($notification['data_structure']))
                        <h4>Data Structure</h4>
                        <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px;"><code>{{ json_encode($notification['data_structure'], JSON_PRETTY_PRINT) }}</code></pre>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No notifications found.</p>
            @endif
        </div>
    </div>
</div>
