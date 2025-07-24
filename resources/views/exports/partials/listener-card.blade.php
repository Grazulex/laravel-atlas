<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '👂',
        'title' => $listener['name'],
        'badge' => 'Listener',
        'badgeColor' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300',
        'namespace' => $listener['namespace'],
        'class' => $listener['class']
    ])

    {{-- Handle Method Status --}}
    @include('atlas::exports.partials.common.property-item', [
        'icon' => '🔧',
        'label' => 'Has Handle Method',
        'value' => $listener['handle_method'] ? 'Yes' : 'No',
        'type' => 'simple'
    ])

    {{-- Methods --}}
    @if (!empty($listener['methods']))
        <div class="mb-3">
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🧾',
                'label' => 'Public Methods',
                'type' => 'table'
            ])
            <table class="w-full text-xs border rounded overflow-hidden mt-1">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                    <tr>
                        <th class="p-2">Method</th>
                        <th class="p-2">Parameters</th>
                        <th class="p-2">Return Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listener['methods'] as $method)
                        <tr class="border-t dark:border-gray-600">
                            <td class="p-2">
                                <code class="text-purple-600 dark:text-purple-400">{{ $method['name'] }}</code>
                                @if ($method['is_static'])
                                    <span class="text-[10px] text-orange-600 ml-1">[static]</span>
                                @endif
                            </td>
                            <td class="p-2">
                                @if (!empty($method['parameters']))
                                    <div class="space-y-1">
                                        @foreach ($method['parameters'] as $param)
                                            <div class="text-[10px]">
                                                @if ($param['type'])
                                                    <span class="text-gray-600 dark:text-gray-400">{{ class_basename($param['type']) }}</span>
                                                @endif
                                                <span class="text-blue-600 dark:text-blue-400">${{ $param['name'] }}</span>
                                                @if ($param['has_default'])
                                                    <span class="text-yellow-600">[default]</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-2">
                                @if ($method['return_type'])
                                    <code class="text-green-600 dark:text-green-400">{{ class_basename($method['return_type']) }}</code>
                                @else
                                    <span class="text-gray-400">void</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Should Queue --}}
    @include('atlas::exports.partials.common.property-item', [
        'icon' => '⏰',
        'label' => 'Should Queue',
        'value' => $listener['queued'] ? 'Yes' : 'No',
        'type' => 'simple'
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $listener['class'],
        'file' => $listener['file']
    ])
</div>
