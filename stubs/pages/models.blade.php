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
                                    <ul>
                                        @foreach($model['attributes'] as $attribute)
                                            @if(is_array($attribute))
                                                <li>{{ implode(', ', $attribute) }}</li>
                                            @else
                                                <li>{{ $attribute }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <p><em>No attributes found</em></p>
                                @endif
                            </div>
                            <div>
                                <h4>Relationships</h4>
                                @if(isset($model['relationships']))
                                    <ul>
                                        @foreach($model['relationships'] as $relationship)
                                            @if(is_array($relationship))
                                                <li>{{ implode(' → ', $relationship) }}</li>
                                            @else
                                                <li>{{ $relationship }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
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
