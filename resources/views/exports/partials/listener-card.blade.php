<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
{{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '👂',
        'title' => $item['name'],
        'badge' => 'Listener',
        'badgeColor' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300',
        'namespace' => $item['namespace'],
        'class' => $item['class']
    ])

    {{-- Handle Method Status --}}
    @include('atlas::exports.partials.common.property-item', [
        'icon' => '🔧',
        'label' => 'Has Handle Method',
        'value' => $item['handle_method'] ? 'Yes' : 'No',
        'type' => 'simple'
    ])

    {{-- Should Queue --}}
    @include('atlas::exports.partials.common.property-item', [
        'icon' => '⏰',
        'label' => 'Should Queue',
        'value' => $item['queued'] ? 'Yes' : 'No',
        'type' => 'simple'
    ])

    {{-- Méthodes --}}
    @include('atlas::exports.partials.common.collapsible-methods', [
        'methods' => $item['methods'] ?? [],
        'componentId' => 'listener-' . md5($item['class']),
        'title' => 'Méthodes',
        'icon' => '⚙️',
        'collapsed' => true
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $item['class'],
        'file' => $item['file']
    ])
</div>