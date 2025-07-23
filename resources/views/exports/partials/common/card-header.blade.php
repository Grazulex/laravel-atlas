{{-- 
    Common card header component
    @param string $icon - Emoji icon
    @param string $title - Main title
    @param string $badge - Badge text (optional)
    @param string $badgeColor - Badge color scheme (optional)
--}}
<div class="flex items-center justify-between mb-3">
    <h2 class="text-sm font-semibold text-indigo-700 truncate max-w-[70%]">
        {{ $icon }} {{ $title }}
    </h2>
    @if (!empty($badge))
        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $badgeColor ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
            {{ $badge }}
        </span>
    @endif
</div>
