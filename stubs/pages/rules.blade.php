<!-- Rules Page -->
<div id="rules" class="page">
    <div class="card">
        <div class="card-header">
            <h2>✅ Validation Rules</h2>
        </div>
        <div class="card-body">
            @if(isset($data['rules']))
                @foreach($data['rules'] as $rule)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($rule['class_name']) }}</h3>
                        <small>{{ $rule['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($rule['rule_type']))
                        <h4>Rule Type</h4>
                        <span class="badge bg-primary">{{ ucfirst($rule['rule_type']) }}</span>
                        @endif

                        @if(isset($rule['methods']) && is_array($rule['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($rule['methods'] as $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #198754;">
                                <strong>{{ $method['name'] }}()</strong>
                                @if($method['name'] === 'passes')
                                    <br><small class="text-success">✓ Main validation logic</small>
                                @elseif($method['name'] === 'message')
                                    <br><small class="text-info">💬 Error message</small>
                                @endif
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

                        @if(isset($rule['dependencies']) && !empty($rule['dependencies']))
                        <h4>Dependencies</h4>
                        <ul>
                            @foreach($rule['dependencies'] as $dependency)
                                <li><code>{{ $dependency }}</code></li>
                            @endforeach
                        </ul>
                        @endif

                        @if(isset($rule['error_message']))
                        <h4>Default Error Message</h4>
                        <div style="padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; color: #856404;">
                            {{ $rule['error_message'] }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No validation rules found.</p>
            @endif
        </div>
    </div>
</div>
