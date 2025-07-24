<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '⚡',
        'title' => class_basename($job['class']),
        'badge' => 'Job',
        'badgeColor' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200'
    ])

    {{-- Queueable Badge --}}
    @if($job['queueable'])
        <div class="mb-3">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                Queueable
            </span>
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <!-- Traits -->
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔧',
            'label' => 'Traits',
            'value' => implode(', ', $job['traits']),
            'type' => 'simple'
        ])

        <!-- Configuration Queue -->
        @if(count($job['queue_config']) > 0)
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '⚙️',
                'label' => 'Configuration Queue',
                'items' => array_map(fn($key, $value) => "$key: $value", array_keys($job['queue_config']), array_values($job['queue_config'])),
                'type' => 'list'
            ])
        @endif

        <!-- Propriétés -->
        @if(count($job['properties']) > 0)
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '📋',
                'label' => 'Propriétés',
                'items' => $job['properties'],
                'type' => 'properties'
            ])
        @endif

        <!-- Constructeur -->
        @if(count($job['constructor']['parameters']) > 0)
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🏗️',
                'label' => 'Paramètres constructeur',
                'items' => $job['constructor']['parameters'],
                'type' => 'parameters'
            ])
        @endif

        <!-- Méthodes -->
        @if(count($job['methods']) > 0)
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '⚙️',
                'label' => 'Méthodes',
                'items' => $job['methods'],
                'type' => 'methods'
            ])
        @endif

        <!-- Flow - Jobs Dispatchés -->
        @if(count($job['flow']['jobs']) > 0)
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔄',
                'label' => 'Jobs Dispatchés',
                'items' => array_map(fn($j) => $j['class'] . ($j['async'] ? ' (async)' : ' (sync)'), $job['flow']['jobs']),
                'type' => 'list'
            ])
        @endif

        <!-- Flow - Événements -->
        @if(count($job['flow']['events']) > 0)
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '📡',
                'label' => 'Événements déclenchés',
                'items' => array_map(fn($e) => class_basename($e['class']), $job['flow']['events']),
                'type' => 'list'
            ])
        @endif

        <!-- Flow - Notifications -->
        @if(count($job['flow']['notifications']) > 0)
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '📬',
                'label' => 'Notifications envoyées',
                'items' => array_map(fn($n) => class_basename($n['class']), $job['flow']['notifications']),
                'type' => 'list'
            ])
        @endif

        <!-- Dépendances -->
        @foreach(['models', 'services', 'facades', 'classes'] as $depType)
            @if(count($job['flow']['dependencies'][$depType]) > 0)
                @include('atlas::exports.partials.common.property-item', [
                    'icon' => $depType === 'models' ? '🗃️' : ($depType === 'services' ? '🔧' : ($depType === 'facades' ? '🏛️' : '📦')),
                    'label' => ucfirst($depType),
                    'items' => array_map(fn($item) => class_basename($item), $job['flow']['dependencies'][$depType]),
                    'type' => 'list'
                ])
            @endif
        @endforeach
    </div>
</div>
