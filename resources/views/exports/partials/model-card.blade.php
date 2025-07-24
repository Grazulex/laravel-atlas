<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🧱',
        'title' => class_basename($model['class']),
        'badge' => $model['table'],
        'badgeColor' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        'namespace' => $model['namespace'],
        'class' => $model['class']
    ])

    {{-- Description --}}
    @if (!empty($model['description']))
        <div class="mb-4">
            <p class="text-xs text-gray-600 dark:text-gray-300 italic bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                {{ $model['description'] }}
            </p>
        </div>
    @endif

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Primary Key --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🆔',
            'label' => 'Primary Key',
            'value' => $model['primary_key'],
            'type' => 'simple'
        ])

        {{-- Fillable Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📝',
            'label' => 'Fillable Fields',
            'value' => !empty($model['fillable']) ? count($model['fillable']) . ' fields' : '0 fields',
            'type' => 'simple'
        ])

        {{-- Relations Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔗',
            'label' => 'Relations',
            'value' => !empty($model['relations']) ? count($model['relations']) . ' relations' : '0 relations',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Fillable Fields --}}
        @if (!empty($model['fillable']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">📝</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Fillable Fields ({{ count($model['fillable']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($model['fillable'] as $field)
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 font-medium">
                                {{ $field }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Guarded Fields --}}
        @if (!empty($model['guarded']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">⛔</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Guarded Fields ({{ count($model['guarded']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($model['guarded'] as $field)
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 font-medium">
                                {{ $field }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Casts Table --}}
        @if (!empty($model['casts']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🔣</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Casts ({{ count($model['casts']) }})
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="p-3 text-left font-medium">Field</th>
                                <th class="p-3 text-left font-medium">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-600">
                            @foreach ($model['casts'] as $field => $type)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="p-3">
                                        <code class="text-blue-600 dark:text-blue-400">{{ $field }}</code>
                                    </td>
                                    <td class="p-3 text-gray-600 dark:text-gray-300">{{ $type }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Relations Table --}}
        @if (!empty($model['relations']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🔗</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Relations ({{ count($model['relations']) }})
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="p-3 text-left font-medium">Name</th>
                                <th class="p-3 text-left font-medium">Type</th>
                                <th class="p-3 text-left font-medium">Target</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-600">
                            @foreach ($model['relations'] as $name => $rel)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="p-3">
                                        <code class="text-blue-600 dark:text-blue-400">{{ $name }}</code>
                                    </td>
                                    <td class="p-3">{{ $rel['type'] }}</td>
                                    <td class="p-3">
                                        <code class="text-purple-600 dark:text-purple-400">{{ class_basename($rel['related']) }}</code>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Scopes --}}
        @if (!empty($model['scopes']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🔍</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Scopes ({{ count($model['scopes']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="space-y-2">
                        @foreach($model['scopes'] as $scope)
                            <div class="text-xs bg-white dark:bg-gray-800 rounded p-2 border">
                                <code class="text-purple-600 dark:text-purple-400">{{ $scope['name'] }}({{ implode(', ', $scope['parameters']) }})</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Boot Hooks --}}
        @if (!empty($model['booted_hooks']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🧷</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Boot Hooks ({{ count($model['booted_hooks']) }})
                    </h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($model['booted_hooks'] as $hook)
                            <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200 font-medium">
                                {{ $hook }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Interactive Sections --}}
    <div class="mt-6 space-y-4">
        {{-- Methods Section --}}
        @include('atlas::exports.partials.common.collapsible-methods', [
            'methods' => $model['methods'] ?? [],
            'componentId' => 'model-' . md5($model['class']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])

        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $model['flow'] ?? [],
            'type' => 'model'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $model['class'],
        'file' => $model['file']
    ])
</div>