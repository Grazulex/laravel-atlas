<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '🛡️',
        'title' => class_basename($middleware['class']),
        'badge' => $middleware['has_terminate'] ? 'Terminable' : 'Standard',
        'badgeColor' => $middleware['has_terminate'] ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        'namespace' => $middleware['namespace'],
        'class' => $middleware['class']
    ])

    {{-- Description --}}
    @if (!empty($middleware['description']))
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-3">{{ $middleware['description'] }}</p>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Constructor Dependencies --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🧩',
            'label' => 'Constructor Dependencies',
            'type' => 'list',
            'items' => !empty($middleware['dependencies']) ? collect($middleware['dependencies'])->filter()->map('class_basename')->toArray() : []
        ])

        {{-- Handle Parameters --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Handle Parameters',
            'type' => 'list',
            'items' => !empty($middleware['parameters']) ? collect($middleware['parameters'])->map(function($param) {
                $default = $param['has_default'] ? ' = ' . (is_string($param['default']) ? "'{$param['default']}'" : var_export($param['default'], true)) : '';
                return $param['type'] . ' ' . $param['name'] . $default;
            })->toArray() : []
        ])

        {{-- Full Class Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🏷️',
            'label' => 'Full Class Name',
            'value' => $middleware['class'],
            'type' => 'code'
        ])
    </div>

    {{-- Methods Table --}}
    @if (!empty($middleware['methods']))
        <div class="mb-4">
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '📋',
                'label' => 'Methods',
                'type' => 'table'
            ])
            <table class="w-full text-xs border rounded overflow-hidden mt-1">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                    <tr>
                        <th class="p-2">Method</th>
                        <th class="p-2">Visibility</th>
                        <th class="p-2">Parameters</th>
                        <th class="p-2">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($middleware['methods'] as $method)
                        <tr class="border-t dark:border-gray-600">
                            <td class="p-2">
                                <code>{{ $method['name'] }}</code>
                                @if ($method['is_important'])
                                    <span class="ml-1 text-[10px] text-blue-600">[core]</span>
                                @endif
                            </td>
                            <td class="p-2">
                                <span class="text-[10px] px-1.5 py-0.5 rounded 
                                    {{ $method['visibility'] === 'public' ? 'bg-green-100 text-green-700' : 
                                       ($method['visibility'] === 'protected' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}
                                    dark:bg-opacity-20">
                                    {{ $method['visibility'] }}
                                </span>
                            </td>
                            <td class="p-2 text-gray-600 dark:text-gray-300">
                                @if (!empty($method['parameters']))
                                    @foreach ($method['parameters'] as $param)
                                        <div class="text-[10px]">
                                            {{ $param['type'] ? $param['type'] . ' ' : '' }}{{ $param['name'] }}{{ $param['has_default'] ? ' = default' : '' }}
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-2">
                                @if ($method['name'] === 'handle')
                                    <span class="text-[10px] text-blue-600">[handler]</span>
                                @elseif ($method['name'] === 'terminate')
                                    <span class="text-[10px] text-purple-600">[terminate]</span>
                                @elseif ($method['name'] === '__construct')
                                    <span class="text-[10px] text-green-600">[constructor]</span>
                                @else
                                    <span class="text-[10px] text-gray-500">[helper]</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Flow Section --}}
    @include('atlas::exports.partials.common.flow-section', [
        'flow' => $middleware['flow'] ?? [],
        'type' => 'middleware'
    ])

    {{-- Méthodes --}}
    @include('atlas::exports.partials.common.collapsible-methods', [
        'methods' => $middleware['methods'] ?? [],
        'componentId' => 'middleware-' . md5($middleware['class']),
        'title' => 'Méthodes',
        'icon' => '⚙️',
        'collapsed' => true
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $middleware['class'],
        'file' => $middleware['file']
    ])
</div>
