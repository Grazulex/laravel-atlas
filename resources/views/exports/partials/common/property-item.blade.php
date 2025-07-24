{{-- 
    Property item component
    @param string $label - Property label
    @param mixed $value - Property value (can be string, array, etc.)
    @param string $type - Type of display (default, code, list, badge-list)
    @param string $class - Additional CSS classes
--}}
@php
    $displayType = $type ?? 'default';
    $itemClass = $class ?? '';
@endphp

<div class="property-item {{ $itemClass }}">
    <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ $label }}</dt>
    <dd class="text-sm text-gray-900 dark:text-gray-100">
        @if ($displayType === 'code')
            @if ($value)
                <code class="block">{{ $value }}</code>
            @else
                <span class="text-gray-500 dark:text-gray-400 italic">Not set</span>
            @endif
            
        @elseif ($displayType === 'list')
            @if (is_array($value) && count($value) > 0)
                <ul class="space-y-1">
                    @foreach ($value as $item)
                        <li><code>{{ $item }}</code></li>
                    @endforeach
                </ul>
            @else
                <span class="text-gray-500 dark:text-gray-400 italic">None</span>
            @endif
            
        @elseif ($displayType === 'badge-list')
            @if (is_array($value) && count($value) > 0)
                <div class="flex flex-wrap gap-1">
                    @foreach ($value as $item)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                            {{ $item }}
                        </span>
                    @endforeach
                </div>
            @else
                <span class="text-gray-500 dark:text-gray-400 italic">None</span>
            @endif
            
        @elseif ($displayType === 'method-list')
            @if (is_array($value) && count($value) > 0)
                <div class="space-y-2">
                    @foreach ($value as $method)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <code class="text-sm font-medium">{{ $method['name'] ?? $method }}</code>
                                @if (isset($method['visibility']))
                                    <span class="text-xs px-2 py-1 rounded-full {{ $method['visibility'] === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : ($method['visibility'] === 'protected' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300') }}">
                                        {{ $method['visibility'] }}
                                    </span>
                                @endif
                            </div>
                            @if (isset($method['parameters']) && count($method['parameters']) > 0)
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                    Parameters: <code>{{ implode(', ', $method['parameters']) }}</code>
                                </div>
                            @endif
                            @if (isset($method['return_type']) && $method['return_type'])
                                <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                    Returns: <code>{{ $method['return_type'] }}</code>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <span class="text-gray-500 dark:text-gray-400 italic">None</span>
            @endif
            
        @elseif ($displayType === 'relation-list')
            @if (is_array($value) && count($value) > 0)
                <div class="space-y-2">
                    @foreach ($value as $name => $relation)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-1">
                                <code class="text-sm font-medium">{{ $name }}</code>
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $relation['type'] ?? 'Relation' }}
                                </span>
                            </div>
                            @if (isset($relation['related']))
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    Related: <code>{{ class_basename($relation['related']) }}</code>
                                </div>
                            @endif
                            @if (isset($relation['foreignKey']) && $relation['foreignKey'])
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    Foreign Key: <code>{{ $relation['foreignKey'] }}</code>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <span class="text-gray-500 dark:text-gray-400 italic">None</span>
            @endif
            
        @else
            {{-- Default display --}}
            @if (is_array($value))
                @if (count($value) > 0)
                    <code>{{ implode(', ', $value) }}</code>
                @else
                    <span class="text-gray-500 dark:text-gray-400 italic">None</span>
                @endif
            @elseif ($value)
                {{ $value }}
            @else
                <span class="text-gray-500 dark:text-gray-400 italic">Not set</span>
            @endif
        @endif
    </dd>
</div>
