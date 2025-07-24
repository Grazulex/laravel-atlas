{{-- 
    Common card header component
    @param string $icon - Emoji icon
    @param string $title - Main title
    @param string $badge - Badge text (optional)
    @param string $badgeColor - Badge color scheme (optional)
    @param string $namespace - Component namespace (optional)
    @param string $class - Full class name (optional)
--}}
<div class="mb-4">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 truncate max-w-[70%]">
            {{ $icon }} {{ $title }}
        </h2>
        @if (!empty($badge))
            <span class="text-xs font-bold px-2 py-1 rounded-full {{ $badgeColor ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                {{ $badge }}
            </span>
        @endif
    </div>
    
    @if (!empty($namespace))
        <div class="mb-2">
            <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                📦 {{ $namespace }}
            </p>
        </div>
    @elseif (!empty($class))
        {{-- Extract namespace from class if not provided separately --}}
        @php
            $namespaceParts = explode('\\', $class);
            array_pop($namespaceParts); // Remove class name
            $extractedNamespace = implode('\\', $namespaceParts);
        @endphp
        @if (!empty($extractedNamespace))
            <div class="mb-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                    📦 {{ $extractedNamespace }}
                </p>
            </div>
        @endif
    @endif
    
    @if (!empty($class))
        <div class="mb-2">
            <p class="text-xs text-gray-500 dark:text-gray-500 font-mono">
                🏷️ {{ $class }}
            </p>
        </div>
    @endif
    
    {{-- Horizontal separator --}}
    <div class="border-t border-gray-200 dark:border-gray-600"></div>
</div>
