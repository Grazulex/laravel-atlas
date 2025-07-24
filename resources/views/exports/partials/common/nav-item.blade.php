{{-- Navigation Item Component --}}
<button 
    data-section="{{ $section }}" 
    class="nav-item group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 ease-in-out hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-700 dark:hover:text-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
>
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center space-x-3">
            <span class="text-lg group-hover:scale-110 transition-transform duration-200">{{ $icon }}</span>
            <div class="flex flex-col items-start">
                <span class="text-gray-900 dark:text-gray-100">{{ $label }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-indigo-500 dark:group-hover:text-indigo-400">
                    {{ $description }}
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900 group-hover:text-indigo-800 dark:group-hover:text-indigo-200 transition-colors duration-200">
                {{ $count }}
            </span>
        </div>
    </div>
</button>
