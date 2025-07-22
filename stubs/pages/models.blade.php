<!-- Models Page -->
<div id="models" class="page">
    <div class="card">
        <div class="card-header">
            <h2>📊 Data Models & Relationships</h2>
        </div>
        <div class="card-body">
            @if(isset($data['models']))
                @foreach($data['models'] as $model)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($model['class_name']) }}</h3>
                        <small>{{ $model['class_name'] }} → {{ $model['table'] }}</small>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <h4>Attributes</h4>
                                @if(isset($model['attributes']) && is_array($model['attributes']))
                                    @if(isset($model['attributes']['fillable']) && is_array($model['attributes']['fillable']) && !empty($model['attributes']['fillable']))
                                        <p><strong>Fillable:</strong></p>
                                        <ul>
                                            @foreach($model['attributes']['fillable'] as $field)
                                            <li>{{ $field }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    
                                    @if(isset($model['attributes']['hidden']) && is_array($model['attributes']['hidden']) && !empty($model['attributes']['hidden']))
                                        <p><strong>Hidden:</strong></p>
                                        <ul>
                                            @foreach($model['attributes']['hidden'] as $field)
                                            <li>{{ $field }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    
                                    @if(isset($model['attributes']['casts']) && is_array($model['attributes']['casts']) && !empty($model['attributes']['casts']))
                                        <p><strong>Casts:</strong></p>
                                        <ul>
                                            @foreach($model['attributes']['casts'] as $field => $type)
                                            <li>{{ $field }} → {{ $type }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    
                                    @if(isset($model['attributes']['dates']) && is_array($model['attributes']['dates']) && !empty($model['attributes']['dates']))
                                        <p><strong>Dates:</strong></p>
                                        <ul>
                                            @foreach($model['attributes']['dates'] as $field)
                                            <li>{{ $field }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    
                                    @if(isset($model['attributes']['table']))
                                        <p><strong>Table:</strong> {{ $model['attributes']['table'] }}</p>
                                    @endif
                                    
                                    @if(isset($model['attributes']['primary_key']))
                                        <p><strong>Primary Key:</strong> {{ $model['attributes']['primary_key'] }}</p>
                                    @endif
                                    
                                    @if(isset($model['attributes']['timestamps']) && $model['attributes']['timestamps'])
                                        <p><strong>Timestamps:</strong> Yes</p>
                                    @endif
                                @else
                                    <p><em>No attributes found</em></p>
                                @endif
                            </div>
                            <div>
                                <h4>Relationships</h4>
                                @if(isset($model['relationships']) && !empty($model['relationships']))
                                    @foreach($model['relationships'] as $relationName => $relationData)
                                    <div style="margin-bottom: 10px;">
                                        <p><strong>{{ ucfirst($relationName) }}:</strong></p>
                                        @if(is_array($relationData))
                                            <ul style="margin-left: 20px;">
                                                @foreach($relationData as $key => $value)
                                                <li>{{ $key }}: {{ is_array($value) ? implode(', ', $value) : $value }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span style="margin-left: 20px;">{{ $relationData }}</span>
                                        @endif
                                    </div>
                                    @endforeach
                                @else
                                    <p><em>No relationships found</em></p>
                                @endif
                            </div>
                        </div>
                        
                        @if(isset($model['connected_to']))
                        <div class="component-connections">
                            @foreach($model['connected_to'] as $type => $components)
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
                <p>No models found.</p>
            @endif
        </div>
    </div>
</div>
