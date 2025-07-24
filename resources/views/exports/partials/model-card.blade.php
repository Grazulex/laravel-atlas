{{-- Model Card Component --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-200">
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🧱',
        'title' => $item['name'],
        'subtitle' => $item['namespace'],
        'badges' => [
            [
                'text' => 'Model',
                'class' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
                'icon' => '🧱'
            ],
            [
                'text' => $item['table'],
                'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'icon' => '🗃️'
            ]
        ]
    ])

    <div class="p-6 space-y-6">
        {{-- Basic Information --}}
        <div class="grid md:grid-cols-2 gap-4">
            @include('atlas::exports.partials.common.property-item', [
                'label' => 'Table Name',
                'value' => $item['table'],
                'type' => 'code'
            ])

            @include('atlas::exports.partials.common.property-item', [
                'label' => 'Primary Key',
                'value' => $item['primary_key'],
                'type' => 'code'
            ])

            @include('atlas::exports.partials.common.property-item', [
                'label' => 'File Location',
                'value' => str_replace(base_path() . '/', '', $item['file']),
                'type' => 'code'
            ])

            @include('atlas::exports.partials.common.property-item', [
                'label' => 'Class',
                'value' => $item['class'],
                'type' => 'code'
            ])
        </div>

        {{-- Fillable and Guarded --}}
        @if (!empty($item['fillable']) || !empty($item['guarded']))
            <div class="grid md:grid-cols-2 gap-4">
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Fillable Fields',
                    'value' => $item['fillable'] ?? [],
                    'type' => 'badge-list'
                ])

                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Guarded Fields',
                    'value' => $item['guarded'] ?? [],
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Casts --}}
        @if (!empty($item['casts']))
            <div>
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Attribute Casts',
                    'value' => collect($item['casts'])->map(function($cast, $attribute) { 
                        return $attribute . ' → ' . $cast; 
                    })->values()->toArray(),
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Relations --}}
        @if (!empty($item['relations']))
            <div>
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Relationships',
                    'value' => $item['relations'],
                    'type' => 'relation-list'
                ])
            </div>
        @endif

        {{-- Scopes --}}
        @if (!empty($item['scopes']))
            <div>
                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Query Scopes</dt>
                <dd>
                    <div class="space-y-2">
                        @foreach ($item['scopes'] as $scope)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <code class="text-sm font-medium">{{ $scope['name'] }}</code>
                                    <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                        scope
                                    </span>
                                </div>
                                @if (!empty($scope['parameters']))
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        Parameters: <code>{{ implode(', ', $scope['parameters']) }}</code>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </dd>
            </div>
        @endif

        {{-- Boot Hooks --}}
        @if (!empty($item['booted_hooks']))
            <div>
                @include('atlas::exports.partials.common.property-item', [
                    'label' => 'Model Boot Hooks',
                    'value' => $item['booted_hooks'],
                    'type' => 'badge-list'
                ])
            </div>
        @endif

        {{-- Flow Section --}}
        @if (!empty($item['flow']))
            @include('atlas::exports.partials.common.flow-section', [
                'flow' => $item['flow'],
                'type' => 'model'
            ])
        @endif
    </div>
</div>