<!-- Requests Page -->
<div id="requests" class="page">
    <div class="card">
        <div class="card-header">
            <h2>📝 Form Requests</h2>
        </div>
        <div class="card-body">
            @if(isset($data['requests']))
                @foreach($data['requests'] as $request)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($request['class_name']) }}</h3>
                        <small>{{ $request['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($request['validation_rules']) && !empty($request['validation_rules']))
                        <h4>Validation Rules</h4>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Rules</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request['validation_rules'] as $rule)
                                    <tr>
                                        <td><code>{{ $rule['field'] ?? 'unknown' }}</code></td>
                                        <td>
                                            @if(isset($rule['rules']) && is_array($rule['rules']))
                                                @foreach($rule['rules'] as $validationRule)
                                                    <span class="badge bg-secondary">{{ $validationRule }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-secondary">{{ $rule['rules'] ?? 'no rules' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        @if(isset($request['custom_messages']) && !empty($request['custom_messages']))
                        <h4>Custom Messages</h4>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rule</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request['custom_messages'] as $rule => $message)
                                    <tr>
                                        <td><code>{{ $rule }}</code></td>
                                        <td>{{ $message }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        @if(isset($request['authorization']) && $request['authorization'] !== null)
                        <h4>Authorization</h4>
                        <span class="badge bg-{{ $request['authorization'] ? 'success' : 'warning' }}">
                            {{ $request['authorization'] ? 'Required' : 'Not Required' }}
                        </span>
                        @endif

                        @if(isset($request['methods']) && is_array($request['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                            @foreach($request['methods'] as $method)
                            <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #6f42c1;">
                                <strong>{{ $method['name'] }}()</strong>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No form requests found.</p>
            @endif
        </div>
    </div>
</div>
