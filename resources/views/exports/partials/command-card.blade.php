<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '💬',
        'title' => class_basename($command['class']),
        'badge' => !empty($command['aliases']) ? implode(', ', $command['aliases']) : 'Command',
        'badgeColor' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
        'namespace' => $command['namespace'],
        'class' => $command['class']
    ])

    {{-- Description --}}
    @if (!empty($command['description']))
        <div class="mb-4">
            <p class="text-xs text-gray-600 dark:text-gray-300 italic bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                {{ $command['description'] }}
            </p>
        </div>
    @endif

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Signature --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔤',
            'label' => 'Signature',
            'value' => $command['signature'] ?? 'Not Set',
            'type' => 'simple'
        ])

        {{-- Arguments Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📝',
            'label' => 'Arguments',
            'value' => !empty($command['parsed_signature']['arguments']) ? count($command['parsed_signature']['arguments']) . ' arguments' : '0 arguments',
            'type' => 'simple'
        ])

        {{-- Options Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '⚙️',
            'label' => 'Options',
            'value' => !empty($command['parsed_signature']['options']) ? count($command['parsed_signature']['options']) . ' options' : '0 options',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Arguments & Options Table --}}
        @if (!empty($command['parsed_signature']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">🧾</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Arguments & Options ({{ count($command['parsed_signature']) }})
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="p-3 text-left font-medium">Name</th>
                                <th class="p-3 text-left font-medium">Type</th>
                                <th class="p-3 text-left font-medium">Details</th>
                                <th class="p-3 text-left font-medium">Modifier</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-600">
                            @foreach ($command['parsed_signature'] as $sig)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="p-3">
                                        <code class="text-blue-600 dark:text-blue-400">{{ $sig['name'] }}</code>
                                    </td>
                                    <td class="p-3">{{ ucfirst($sig['type']) }}</td>
                                    <td class="p-3 text-gray-600 dark:text-gray-300">{{ $sig['description'] ?? '—' }}</td>
                                    <td class="p-3">
                                        @if ($sig['modifier'] === '*')
                                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 font-medium">array</span>
                                        @elseif ($sig['modifier'] === '=')
                                            <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200 font-medium">default</span>
                                        @else
                                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 font-medium">required</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Interactive Sections --}}
    <div class="mt-6 space-y-4">
        {{-- Methods Section --}}
        @include('atlas::exports.partials.common.collapsible-methods', [
            'methods' => $command['methods'] ?? [],
            'componentId' => 'command-' . md5($command['class']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])

        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $command['flow'] ?? [],
            'type' => 'command'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $command['class'],
        'file' => $command['file'] ?? 'N/A'
    ])
</div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $command['class'],
        'file' => $command['file']
    ])
</div>