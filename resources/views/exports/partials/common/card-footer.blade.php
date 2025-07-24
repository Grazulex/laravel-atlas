{{-- Card Footer --}}
<div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-600">
    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center">
                📄 <span class="ml-1 font-mono">{{ $class }}</span>
            </span>
        </div>
        @if (!empty($file))
            <div class="flex items-center space-x-1 text-gray-400 dark:text-gray-500">
                <span>📁</span>
                <span class="font-mono text-[10px]">{{ $file }}</span>
            </div>
        @endif
    </div>
</div>
