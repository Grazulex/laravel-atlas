{{-- 
    Property grid item component
    @param string $icon - Emoji icon
    @param string $label - Property label
    @param string $value - Property value (optional)
    @param array $items - Array of items for lists (optional)
    @param string $type - Display type: 'simple', 'list', 'code', 'table' (default: simple)
--}}
<div class="min-h-[2rem]">
    <span class="block text-xs text-gray-400 dark:text-gray-500 font-semibold mb-1">{{ $icon }} {{ $label }}</span>
    
    @if ($type === 'list')
        @if (!empty($items))
            <ul class="text-xs space-y-0.5">
                @foreach ($items as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif ($type === 'code')
        @if (!empty($value))
            <div class="text-xs bg-gray-50 dark:bg-gray-700 rounded p-2 text-gray-800 dark:text-gray-200 leading-tight">
                {{ $value }}
            </div>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @elseif ($type === 'table')
        {{ $slot ?? '' }}
    @else
        @if (!empty($value))
            <code class="text-xs">{{ $value }}</code>
        @else
            <span class="text-xs text-gray-500 italic">None</span>
        @endif
    @endif
</div>
