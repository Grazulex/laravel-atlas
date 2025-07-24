<div class="bg-whi    @endif

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Authorization Status --}}unded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{-- Header --}}
    @include('atlas::exports.partials.common.card-header', [
        'icon' => '📝',
        'title' => str_replace(['Store', 'Update', 'Request'], ['Create', 'Edit', ''], class_basename($form_request['class'])),
        'badge' => str_contains($form_request['class'], 'Store') ? 'Create' : (str_contains($form_request['class'], 'Update') ? 'Update' : 'Form Request'),
        'badgeColor' => str_contains($form_request['class'], 'Store') ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : (str_contains($form_request['class'], 'Update') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'),
        'namespace' => $form_request['namespace'],
        'class' => $form_request['class']
    ])

    {{-- Description --}}
    @if (!empty($form_request['description']))
        <div class="mb-4">
            <p class="text-xs text-gray-600 dark:text-gray-300 italic bg-gray-50 dark:bg-gray-700/50 rounded p-3">
                {{ $form_request['description'] }}
            </p>
        </div>
    @endif

    {{-- Key Properties Grid (Always 3 columns on large screens) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Authorization --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🔐',
            'label' => 'Authorization',
            'value' => !empty($form_request['authorization']) ? 
                      ($form_request['authorization']['always_true'] ? 'Always Allow' : 
                      ($form_request['authorization']['always_false'] ? 'Always Deny' : 
                      ($form_request['authorization']['uses_can'] ? 'Policy-based' : 'Custom Logic'))) : 'Not Set',
            'type' => 'simple'
        ])

        {{-- Rules Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📋',
            'label' => 'Validation Rules',
            'value' => count($form_request['rules']) . ' fields',
            'type' => 'simple'
        ])

        {{-- Messages Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '💬',
            'label' => 'Custom Messages',
            'value' => count($form_request['messages']) . ' messages',
            'type' => 'simple'
        ])
    </div>

    {{-- Detailed Tables Section --}}
    <div class="space-y-6">
        {{-- Validation Rules Table --}}
        @if (!empty($form_request['rules']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">✅</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Validation Rules ({{ count($form_request['rules']) }})
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="p-3 text-left font-medium">Field</th>
                                <th class="p-3 text-left font-medium">Rules</th>
                                <th class="p-3 text-left font-medium">Custom Attribute</th>
                                <th class="p-3 text-left font-medium">Custom Message</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-600">
                            @foreach ($form_request['rules'] as $field => $rules)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="p-3">
                                        <code class="text-blue-600 dark:text-blue-400">{{ $field }}</code>
                                        @if (in_array('required', $rules))
                                            <span class="ml-1 text-xs text-red-600 font-bold">*</span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($rules as $rule)
                                                <span class="text-xs px-2 py-1 rounded-full font-medium
                                                    {{ str_contains($rule, 'required') ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' : 
                                                       (str_contains($rule, 'unique') || str_contains($rule, 'exists') ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200' : 
                                                       (str_contains($rule, 'new ') ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200' : 
                                                       'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200')) }}">
                                                    {{ $rule }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="p-3 text-gray-600 dark:text-gray-300">
                                        {{ $form_request['attributes'][$field] ?? '—' }}
                                    </td>
                                    <td class="p-3">
                                        @php
                                            $hasCustomMessage = false;
                                            foreach ($form_request['messages'] as $key => $message) {
                                                if (str_starts_with($key, $field . '.')) {
                                                    $hasCustomMessage = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <span class="text-{{ $hasCustomMessage ? 'green' : 'gray' }}-600 dark:text-{{ $hasCustomMessage ? 'green' : 'gray' }}-400">
                                            {{ $hasCustomMessage ? '✓' : '—' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Custom Messages Table --}}
        @if (!empty($form_request['messages']))
            <div>
                <div class="flex items-center mb-3">
                    <span class="text-sm mr-2">💬</span>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Custom Messages ({{ count($form_request['messages']) }})
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="p-3 text-left font-medium">Rule</th>
                                <th class="p-3 text-left font-medium">Custom Message</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-600">
                            @foreach ($form_request['messages'] as $rule => $message)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="p-3">
                                        <code class="text-blue-600 dark:text-blue-400">{{ $rule }}</code>
                                    </td>
                                    <td class="p-3 text-gray-600 dark:text-gray-300">{{ $message }}</td>
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
            'methods' => $form_request['methods'] ?? [],
            'componentId' => 'form-request-' . md5($form_request['class']),
            'title' => 'Methods',
            'icon' => '⚙️',
            'collapsed' => true
        ])

        {{-- Flow Section --}}
        @include('atlas::exports.partials.common.flow-section', [
            'flow' => $form_request['flow'] ?? [],
            'type' => 'form_request'
        ])
    </div>

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $form_request['class'],
        'file' => $form_request['file']
    ])
</div>
