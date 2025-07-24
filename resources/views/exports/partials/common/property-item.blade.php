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
    @elseif (($type ?? 'simple') === 'properties')
        @if (!empty($items))
            <div class="text-xs space-y-1">
                @foreach ($items as $property)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300">{{ $property['name'] }}</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $property['type'] }}</span>
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
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300 font-mono">{{ $method['name'] }}()</span>
                        <span class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $method['returnType'] }}</span>
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
                            <span class="text-gray-700 dark:text-gray-300">{{ implode(', ', $deps) }}</span>
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
                        <span class="text-gray-700 dark:text-gray-300">${{ $param['name'] }}</span>
                        <div class="flex items-center space-x-1">
                            <span class="text-indigo-600 dark:text-indigo-400 font-mono">{{ $param['type'] }}</span>
                            @if($param['nullable'])
                                <span class="text-yellow-600 dark:text-yellow-400">?</span>
                            @endif
                            @if($param['hasDefault'])
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
