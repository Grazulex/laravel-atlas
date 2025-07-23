<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🧱',
        'title' => class_basename($model['class']),
        'badge' => $model['table'],
        'badgeColor' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
    ])

    {{-- Description --}}
    @if (!empty($model['description']))
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-3">{{ $model['description'] }}</p>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Primary Key --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🆔',
            'label' => 'Primary key',
            'value' => $model['primary_key'],
            'type' => 'simple'
        ])

        {{-- Fillable --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📝',
            'label' => 'Fillable',
            'value' => !empty($model['fillable']) ? implode(', ', $model['fillable']) : null,
            'type' => 'code'
        ])

        {{-- Guarded --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⛔',
            'label' => 'Guarded',
            'value' => !empty($model['guarded']) ? implode(', ', $model['guarded']) : null,
            'type' => 'code'
        ])

        {{-- Boot Hooks --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧷',
            'label' => 'Boot Hooks',
            'value' => !empty($model['booted_hooks']) ? implode(', ', $model['booted_hooks']) : null,
            'type' => 'code'
        ])
    </div>

    {{-- Casts Table --}}
    @if (!empty($model['casts']))
        <div class="mb-4">
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔣',
                'label' => 'Casts',
                'type' => 'table'
            ])
            <table class="w-full text-xs border rounded overflow-hidden mt-1">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                    <tr>
                        <th class="p-2">Field</th>
                        <th class="p-2">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($model['casts'] as $field => $type)
                        <tr class="border-t dark:border-gray-600">
                            <td class="p-2"><code>{{ $field }}</code></td>
                            <td class="p-2 text-gray-600 dark:text-gray-300">{{ $type }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Relations Table --}}
    @if (!empty($model['relations']))
        <div class="mb-4">
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔗',
                'label' => 'Relations',
                'type' => 'table'
            ])
            <table class="w-full text-xs border rounded overflow-hidden mt-1">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                    <tr>
                        <th class="p-2">Name</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Target</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($model['relations'] as $name => $rel)
                        <tr class="border-t dark:border-gray-600">
                            <td class="p-2"><code>{{ $name }}</code></td>
                            <td class="p-2">{{ $rel['type'] }}</td>
                            <td class="p-2"><code>{{ class_basename($rel['related']) }}</code></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Scopes --}}
    @if (!empty($model['scopes']))
        <div class="mb-4">
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔍',
                'label' => 'Scopes',
                'type' => 'list',
                'items' => collect($model['scopes'])->map(function($scope) {
                    return $scope['name'] . '(' . implode(', ', $scope['parameters']) . ')';
                })->toArray()
            ])
        </div>
    @endif

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $model['flow'] ?? [],
        'type' => 'model'
    ])
</div>