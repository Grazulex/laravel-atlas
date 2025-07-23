<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '💬',
        'title' => class_basename($command['class']),
        'badge' => !empty($command['aliases']) ? implode(', ', $command['aliases']) : null,
        'badgeColor' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
    ])

    {{-- Description --}}
    @if (!empty($command['description']))
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-3">{{ $command['description'] }}</p>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Signature --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔤',
            'label' => 'Signature',
            'value' => $command['signature'] ?? null,
            'type' => 'code'
        ])
    </div>

    {{-- Signature Table --}}
    @if (!empty($command['parsed_signature']))
        <div class="mb-4">
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🧾',
                'label' => 'Arguments & Options',
                'type' => 'table'
            ])
            <table class="w-full text-xs border rounded overflow-hidden mt-1">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                    <tr>
                        <th class="p-2">Name</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Details</th>
                        <th class="p-2">Modifier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($command['parsed_signature'] as $sig)
                        <tr class="border-t dark:border-gray-600">
                            <td class="p-2"><code>{{ $sig['name'] }}</code></td>
                            <td class="p-2">{{ ucfirst($sig['type']) }}</td>
                            <td class="p-2 text-gray-600 dark:text-gray-300">{{ $sig['description'] ?? '-' }}</td>
                            <td class="p-2">
                                @if ($sig['modifier'] === '*')
                                    <span class="text-[10px] text-blue-600">[array]</span>
                                @elseif ($sig['modifier'] === '=')
                                    <span class="text-[10px] text-yellow-600">[default]</span>
                                @else
                                    <span class="text-[10px] text-gray-500">[required]</span>
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
        'flow' => $command['flow'] ?? [],
        'type' => 'command'
    ])
</div>