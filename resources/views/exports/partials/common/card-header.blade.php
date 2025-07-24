{{-- 
    Card header component
    @param string $icon - Emoji icon for the component type
    @param string $title - Title to display
    @param string $subtitle - Optional subtitle
    @param array $badges - Optional array of badges to display
--}}
<div class="flex items-start justify-between p-6 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-start space-x-4">
        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
            <span class="text-white text-lg">{{ $icon }}</span>
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $title }}</h3>
            @if (isset($subtitle) && $subtitle)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
            @endif
            
            @if (isset($badges) && is_array($badges) && count($badges) > 0)
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach ($badges as $badge)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                            @if (isset($badge['icon']))
                                <span class="mr-1">{{ $badge['icon'] }}</span>
                            @endif
                            {{ $badge['text'] }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>