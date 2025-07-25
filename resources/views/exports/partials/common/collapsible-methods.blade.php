{{-- Collapsible Methods Section Component --}}
@if(!empty($methods) && count($methods) > 0)
<div class="mb-3">
    <button 
        onclick="toggleSection('methods-{{ $componentId }}')" 
        class="flex items-center justify-between w-full text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded p-2 transition-colors duration-200 group"
    >
        <h4 class="text-xs font-medium text-gray-700 dark:text-gray-300 flex items-center">
            <span class="text-sm mr-2">{{ $icon ?? '⚙️' }}</span>
            {{ $title ?? 'Méthodes' }}
            <span class="ml-2 text-xs bg-gray-200 dark:bg-gray-700 dark:text-gray-100 text-gray-800 px-1.5 py-0.5 rounded">
                {{ count($methods) }}
            </span>
        </h4>
        <div class="flex items-center space-x-2">
            <span id="text-methods-{{ $componentId }}" class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                {{ $collapsed ?? true ? 'Show' : 'Hide' }}
            </span>
            <span id="icon-methods-{{ $componentId }}" class="text-xs text-gray-500 transform transition-transform duration-200 {{ $collapsed ?? true ? '' : 'rotate-180' }}">
                ▼
            </span>
        </div>
    </button>
    
    <div id="methods-{{ $componentId }}" class="mt-2 {{ $collapsed ?? true ? 'hidden' : '' }}">
        <div class="relative">
            <div class="space-y-1 max-h-64 overflow-y-auto scrollbar-hide hover:scrollbar-show" 
                 onscroll="handleMethodsScroll('methods-{{ $componentId }}')">
                @foreach($methods as $method)
                    <div class="text-xs bg-blue-50 dark:bg-blue-900/20 rounded p-2 border-l-2 border-blue-200 dark:border-blue-700">
                        <div class="flex items-start justify-between">
                            <div class="font-mono flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    @if(is_array($method))
                                        <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $method['name'] ?? 'Unknown' }}()</span>
                                        @if(!empty($method['returnType']) || !empty($method['return_type']))
                                            <span class="text-green-600 dark:text-green-400 text-xs">{{ $method['returnType'] ?? $method['return_type'] ?? 'mixed' }}</span>
                                        @endif
                                    @else
                                        <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $method }}()</span>
                                    @endif
                                </div>
                                
                                @if(is_array($method) && !empty($method['parameters']) && is_array($method['parameters']) && count($method['parameters']) > 0)
                                    <div class="text-gray-600 dark:text-gray-400 text-xs mb-1">
                                        Parameters: 
                                        @foreach($method['parameters'] as $index => $param)
                                            @if($index > 0), @endif
                                            @if(is_array($param))
                                                @if(!empty($param['type']))
                                                    <span class="text-purple-600 dark:text-purple-400">{{ $param['type'] }}</span>
                                                @endif
                                                <span class="text-blue-600 dark:text-blue-400">${{ $param['name'] ?? 'param' }}</span>
                                            @else
                                                <span class="text-blue-600 dark:text-blue-400">{{ $param }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if(is_array($method) && !empty($method['description']))
                                    <div class="text-gray-500 dark:text-gray-400 text-xs italic">
                                        {{ $method['description'] }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="ml-3 flex-shrink-0">
                                @if(is_array($method) && !empty($method['source']))
                                    @if($method['source'] === 'class')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                            Class
                                        </span>
                                    @elseif(str_contains($method['source'], 'trait'))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">
                                            {{ str_replace('trait: ', '', $method['source']) }}
                                        </span>
                                    @elseif(str_contains($method['source'], 'parent'))
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">
                                            {{ str_replace('parent: ', '', $method['source']) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            {{ $method['source'] }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Fade gradient at bottom -->
            <div id="fade-methods-{{ $componentId }}" class="absolute bottom-0 left-0 right-0 h-6 bg-gradient-to-t from-white dark:from-gray-800 to-transparent pointer-events-none opacity-0 transition-opacity duration-200"></div>
        </div>
        
        @if(count($methods) > 10)
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                {{ count($methods) }} methods total
            </div>
        @endif
    </div>
</div>

<script>
if (typeof toggleSection === 'undefined') {
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const icon = document.getElementById('icon-' + sectionId);
        const textSpan = document.getElementById('text-' + sectionId);
        
        if (section && icon && textSpan) {
            if (section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                icon.classList.add('rotate-180');
                textSpan.textContent = 'Hide';
                
                // Check scroll state after showing
                setTimeout(() => {
                    handleMethodsScroll(sectionId);
                }, 100);
            } else {
                section.classList.add('hidden');
                icon.classList.remove('rotate-180');
                textSpan.textContent = 'Show';
            }
        }
    }
}

if (typeof handleMethodsScroll === 'undefined') {
    function handleMethodsScroll(sectionId) {
        const section = document.getElementById(sectionId);
        if (!section) return;
        
        const scrollContainer = section.querySelector('[onscroll]');
        const fadeElement = document.getElementById('fade-' + sectionId);
        
        if (scrollContainer && fadeElement) {
            const isScrollable = scrollContainer.scrollHeight > scrollContainer.clientHeight;
            const isAtBottom = scrollContainer.scrollTop + scrollContainer.clientHeight >= scrollContainer.scrollHeight - 5;
            
            if (isScrollable && !isAtBottom) {
                fadeElement.classList.remove('opacity-0');
                fadeElement.classList.add('opacity-100');
            } else {
                fadeElement.classList.remove('opacity-100');
                fadeElement.classList.add('opacity-0');
            }
        }
    }
    
    // Check scroll state on load
    document.addEventListener('DOMContentLoaded', function() {
        const methodsSections = document.querySelectorAll('[id^="methods-"]');
        methodsSections.forEach(section => {
            if (!section.classList.contains('hidden')) {
                handleMethodsScroll(section.id);
            }
        });
    });
}
</script>
@endif
