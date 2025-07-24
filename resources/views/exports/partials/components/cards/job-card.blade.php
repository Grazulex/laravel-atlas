<div class="card-header">
    <div class="card-title">
        <h4>{{ $job['name'] }}</h4>
    </div>
    <div class="card-meta">
        <span class="badge bg-warning">Job</span>
        @if($job['queueable'])
            <span class="badge bg-info">Queueable</span>
        @endif
        @if(count($job['traits']) > 0)
            <span class="badge bg-secondary">{{ count($job['traits']) }} traits</span>
        @endif
    </div>
</div>

<div class="card-body">
    <!-- Traits -->
    @if(count($job['traits']) > 0)
        @include('atlas::exports.partials.common.property-item', [
            'label' => 'Traits',
            'value' => implode(', ', $job['traits']),
            'type' => 'traits'
        ])
    @endif

    <!-- Configuration Queue -->
    @if(count($job['queue_config']) > 0)
        <div class="property-item">
            <span class="label">Configuration Queue</span>
            <div class="value">
                @foreach($job['queue_config'] as $key => $value)
                    <div class="config-item">
                        <strong>{{ $key }}:</strong> {{ $value }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Propriétés -->
    @if(count($job['properties']) > 0)
        @include('atlas::exports.partials.common.property-item', [
            'label' => 'Propriétés',
            'items' => $job['properties'],
            'type' => 'properties'
        ])
    @endif

    <!-- Constructeur -->
    @if(count($job['constructor']['parameters']) > 0)
        @include('atlas::exports.partials.common.property-item', [
            'label' => 'Paramètres constructeur',
            'items' => $job['constructor']['parameters'],
            'type' => 'parameters'
        ])
    @endif

    <!-- Méthodes -->
    @if(count($job['methods']) > 0)
        @include('atlas::exports.partials.common.property-item', [
            'label' => 'Méthodes',
            'items' => $job['methods'],
            'type' => 'methods'
        ])
    @endif

    <!-- Flow - Jobs Dispatchés -->
    @if(count($job['flow']['jobs']) > 0)
        <div class="property-item">
            <span class="label">Jobs Dispatchés</span>
            <div class="value">
                @foreach($job['flow']['jobs'] as $job_data)
                    <div class="flow-item">
                        <span class="badge bg-warning">{{ class_basename($job_data['class']) }}</span>
                        @if($job_data['async'])
                            <span class="badge bg-info">async</span>
                        @else
                            <span class="badge bg-secondary">sync</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Flow - Événements -->
    @if(count($job['flow']['events']) > 0)
        <div class="property-item">
            <span class="label">Événements déclenchés</span>
            <div class="value">
                @foreach($job['flow']['events'] as $event)
                    <span class="badge bg-purple">{{ class_basename($event['class']) }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Flow - Notifications -->
    @if(count($job['flow']['notifications']) > 0)
        <div class="property-item">
            <span class="label">Notifications envoyées</span>
            <div class="value">
                @foreach($job['flow']['notifications'] as $notification)
                    <span class="badge bg-success">{{ class_basename($notification['class']) }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Dépendances -->
    @foreach(['models', 'services', 'facades', 'classes'] as $depType)
        @if(count($job['flow']['dependencies'][$depType]) > 0)
            @include('atlas::exports.partials.common.property-item', [
                'label' => ucfirst($depType),
                'items' => array_map(fn($item) => ['name' => class_basename($item), 'fqcn' => $item], $job['flow']['dependencies'][$depType]),
                'type' => 'dependencies'
            ])
        @endif
    @endforeach
</div>
