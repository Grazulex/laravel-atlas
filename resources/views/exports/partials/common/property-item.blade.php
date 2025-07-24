{{-- 
    Property grid item component
    @param string $icon - Emoji icon (optional)
    @param string $label - Property label
    @param string $value - Property value (optional)
    @param array $items - Array of items for lists (optional)
    @param string $type - Display type: 'simple', 'list', 'code', 'table', 'properties', 'methods', 'dependencies', 'transformations', 'parameters' (default: simple)
--}}
<div class="min-h-[2rem]">
    <span class="block text-xs text-gray-400 dark:text-gray-500 font-semibold mb-1">{{ $icon ?? '' }} {{ $label }}</span>
    
    @if (($type ?? 'simple') === 'list')
        @if (!empty($items))
            <ul class="text-xs space-y-0.5">
                @foreach ($items as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'badge-list')
        @if (!empty($items))
            <div class="flex flex-wrap gap-1">
                @foreach ($items as $item)
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                        {{ $item }}
                    </span>
                @endforeach
            </div>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'properties')
        @if (!empty($items))
            <div class="text-xs space-y-1">
                @foreach ($items as $property)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300">{{ is_array($property) ? ($property['name'] ?? $property) : $property }}</span>
                        @if (is_array($property) && isset($property['type']))
                            <span class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $property['type'] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'methods')
        @if (!empty($items))
            <div class="text-xs space-y-1">
                @foreach ($items as $method)
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-1 mb-1 last:border-b-0">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700 dark:text-gray-300 font-mono">
                                {{ is_array($method) ? ($method['name'] ?? $method) : $method }}{{ is_array($method) ? '()' : (str_contains($method, '(') ? '' : '()') }}
                            </span>
                            @if (is_array($method) && isset($method['returnType']))
                                <span class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $method['returnType'] }}</span>
                            @elseif (is_array($method) && isset($method['return_type']))
                                <span class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $method['return_type'] }}</span>
                            @endif
                        </div>
                        @if (is_array($method) && isset($method['source']))
                            <div class="text-xs {{ $method['source'] === 'class' ? 'text-green-500' : 'text-orange-500' }} italic mt-0.5">
                                Source: {{ $method['source'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'method-list')
        @if (!empty($items))
            <div class="space-y-2">
                @foreach ($items as $method)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <code class="text-sm font-medium">{{ is_array($method) ? ($method['name'] ?? $method) : $method }}</code>
                            @if (is_array($method) && isset($method['visibility']))
                                <span class="text-xs px-2 py-1 rounded-full {{ $method['visibility'] === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : ($method['visibility'] === 'protected' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300') }}">
                                    {{ $method['visibility'] }}
                                </span>
                            @endif
                        </div>
                        @if (is_array($method) && isset($method['parameters']) && count($method['parameters']) > 0)
                            <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                Parameters: <code>{{ is_array($method['parameters']) ? implode(', ', $method['parameters']) : $method['parameters'] }}</code>
                            </div>
                        @endif
                        @if (is_array($method) && isset($method['return_type']) && $method['return_type'])
                            <div class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                Returns: <code>{{ $method['return_type'] }}</code>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'relation-list')
        @if (!empty($items))
            <div class="space-y-2">
                @foreach ($items as $name => $relation)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-1">
                            <code class="text-sm font-medium">{{ $name }}</code>
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ is_array($relation) ? ($relation['type'] ?? 'Relation') : 'Relation' }}
                            </span>
                        </div>
                        @if (is_array($relation) && isset($relation['related']))
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                Related: <code>{{ class_basename($relation['related']) }}</code>
                            </div>
                        @endif
                        @if (is_array($relation) && isset($relation['foreignKey']) && $relation['foreignKey'])
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                Foreign Key: <code>{{ $relation['foreignKey'] }}</code>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'dependencies')
        @if (!empty($items))
            <div class="text-xs space-y-1">
                @foreach ($items as $depType => $deps)
                    @if (!empty($deps))
                        <div>
                            <span class="text-gray-500 capitalize">{{ $depType }}:</span>
                            <span class="text-gray-700 dark:text-gray-300">{{ is_array($deps) ? implode(', ', $deps) : $deps }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'transformations')
        @if (!empty($items))
            <ul class="text-xs space-y-0.5">
                @foreach ($items as $item)
                    <li class="font-mono text-blue-600 dark:text-blue-400">{{ $item }}</li>
                @endforeach
            </ul>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'parameters')
        @if (!empty($items))
            <div class="text-xs space-y-1">
                @foreach ($items as $param)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300">${{ is_array($param) ? ($param['name'] ?? $param) : $param }}</span>
                        <div class="flex items-center space-x-1">
                            @if (is_array($param) && isset($param['type']))
                                <span class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $param['type'] }}</span>
                            @endif
                            @if (is_array($param) && isset($param['nullable']) && $param['nullable'])
                                <span class="text-yellow-600 dark:text-yellow-400">?</span>
                            @endif
                            @if (is_array($param) && isset($param['hasDefault']) && $param['hasDefault'])
                                <span class="text-green-600 dark:text-green-400">default</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'code')
        @if (!empty($value))
            <div class="text-xs bg-gray-50 dark:bg-gray-700 rounded p-2 text-gray-800 dark:text-gray-200 leading-tight">
                {{ $value }}
            </div>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif (($type ?? 'simple') === 'table')
        {{ $slot ?? '' }}
    @else
        @if (!empty($value))
            <code class="text-xs">{{ $value }}</code>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @endif
</div>
