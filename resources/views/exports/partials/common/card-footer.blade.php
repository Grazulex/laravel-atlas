{{-- Card Footer --}}
@php
    $fileSize = 'N/A';
    $lastModified = 'N/A';
    
    if (!empty($file) && $file !== 'N/A' && file_exists($file)) {
        $fileSize = number_format(filesize($file) / 1024, 1) . ' KB';
        $lastModified = date('d/m/Y H:i', filemtime($file));
    }
@endphp

<div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-600">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs text-gray-500 dark:text-gray-400">
        {{-- Class and File Info --}}
        <div class="space-y-1">
            <div class="flex items-center space-x-1">
                <span>🏷️</span>
                <span class="font-mono truncate">{{ $class }}</span>
            </div>
            @if (!empty($file))
                <div class="flex items-center space-x-1">
                    <span>📄</span>
                    <span class="font-mono text-[10px] truncate">{{ basename($file) }}</span>
                </div>
            @endif
        </div>
        
        {{-- File Stats --}}
        @if (!empty($file) && $file !== 'N/A')
            <div class="space-y-1 text-right md:text-left">
                <div class="flex items-center justify-end md:justify-start space-x-1">
                    <span>💾</span>
                    <span>{{ $fileSize }}</span>
                </div>
                <div class="flex items-center justify-end md:justify-start space-x-1">
                    <span>🕒</span>
                    <span>{{ $lastModified }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
