{{-- 
    Base card wrapper component
    @param string $class - Additional CSS classes for the card
--}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200 {{ $class ?? '' }}">
    {{ $slot }}
</div>
