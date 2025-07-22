<!-- Commands Page -->
<div id="commands" class="page">
    <div class="card">
        <div class="card-header">
            <h2>⚡ Artisan Commands</h2>
        </div>
        <div class="card-body">
            @if(isset($data['commands']))
                @foreach($data['commands'] as $command)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ $command['signature_info']['name'] ?? class_basename($command['class_name'] ?? 'Unknown Command') }}</h3>
                        @if(isset($command['signature_info']['signature']))
                            <code style="background: #f8f9fa; padding: 8px 12px; border-radius: 4px; display: block; margin-top: 8px;">{{ $command['signature_info']['signature'] }}</code>
                        @else
                            <small style="color: #6c757d;">{{ $command['class_name'] ?? '' }}</small>
                        @endif
                    </div>
                    <div class="card-body">
                        <p style="margin-bottom: 20px; font-size: 1.1em;">{{ $command['signature_info']['description'] ?? 'No description available' }}</p>
                        
                        <!-- Arguments Section -->
                        @if(isset($command['arguments']) && count($command['arguments']) > 0)
                        <div style="margin-bottom: 25px;">
                            <h4 style="color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 8px; margin-bottom: 15px;">📝 Arguments</h4>
                            <div style="display: grid; gap: 12px;">
                                @foreach($command['arguments'] as $argument)
                                <div style="background: #f8f9fa; padding: 12px; border-left: 4px solid #28a745; border-radius: 4px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                                        <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-weight: bold;">{{ $argument['name'] }}</code>
                                        @if($argument['optional'])
                                            <span style="background: #ffc107; color: #212529; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; font-weight: bold;">Optional</span>
                                        @else
                                            <span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; font-weight: bold;">Required</span>
                                        @endif
                                        @if($argument['has_default'])
                                            <span style="background: #6c757d; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em;">Default: {{ $argument['default_value'] }}</span>
                                        @endif
                                    </div>
                                    @if($argument['description'])
                                    <div style="color: #6c757d; font-size: 0.9em;">{{ $argument['description'] }}</div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Options Section -->
                        @if(isset($command['options']) && count($command['options']) > 0)
                        <div style="margin-bottom: 25px;">
                            <h4 style="color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 8px; margin-bottom: 15px;">⚙️ Options</h4>
                            <div style="display: grid; gap: 12px;">
                                @foreach($command['options'] as $option)
                                <div style="background: #f8f9fa; padding: 12px; border-left: 4px solid #007bff; border-radius: 4px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                                        <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-weight: bold;">{{ $option['name'] }}</code>
                                        @if($option['has_default'])
                                            <span style="background: #28a745; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em;">Default: {{ $option['default_value'] }}</span>
                                        @endif
                                    </div>
                                    @if($option['description'])
                                    <div style="color: #6c757d; font-size: 0.9em;">{{ $option['description'] }}</div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Dependencies Section -->
                        @if(isset($command['dependencies']) && count($command['dependencies']) > 0)
                        <div style="margin-bottom: 20px;">
                            <h4 style="color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 8px; margin-bottom: 15px;">🔗 Dependencies</h4>
                            <div style="display: grid; gap: 8px;">
                                @foreach($command['dependencies'] as $dependency)
                                <div style="background: #e9ecef; padding: 8px 12px; border-radius: 4px; display: flex; align-items: center; gap: 8px;">
                                    <code style="background: white; padding: 2px 6px; border-radius: 3px;">{{ $dependency['name'] }}</code>
                                    <span style="color: #6c757d; font-size: 0.9em;">{{ $dependency['type'] }}</span>
                                    @if($dependency['is_service'])
                                        <span style="background: #17a2b8; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em;">Service</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Class Information -->
                        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                            <h5 style="color: #6c757d; margin-bottom: 10px;">Class Information</h5>
                            <div style="display: grid; gap: 5px; font-size: 0.9em; color: #6c757d;">
                                <div><strong>Class:</strong> {{ $command['class_name'] ?? 'Unknown' }}</div>
                                <div><strong>Namespace:</strong> {{ $command['namespace'] ?? 'Unknown' }}</div>
                                @if(isset($command['parent_class']))
                                <div><strong>Extends:</strong> {{ class_basename($command['parent_class']) }}</div>
                                @endif
                            </div>
                        </div>
                        
                        @if(isset($command['flows']))
                        <h4>Execution Flow</h4>
                        <div class="flow">
                            @if(isset($command['flows']['synchronous']))
                            <h5>Synchronous Steps</h5>
                            @foreach($command['flows']['synchronous'] as $step)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ is_array($step) ? implode(', ', $step) : $step }}
                            </div>
                            @endforeach
                            @endif
                            
                            @if(isset($command['flows']['asynchronous']))
                            <h5>Asynchronous Events</h5>
                            @foreach($command['flows']['asynchronous'] as $step)
                            <div class="flow-step async">
                                <div class="flow-step-icon">A{{ $loop->iteration }}</div>
                                {{ is_array($step) ? implode(', ', $step) : $step }}
                            </div>
                            @endforeach
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No commands found.</p>
            @endif
        </div>
    </div>
</div>
