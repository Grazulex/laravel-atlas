<div class="bg-white rounded-lg shadow-sm p-4 mb-4 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
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
        <p class="text-xs text-gray-600 dark:text-gray-300 italic mb-3">{{ $form_request['description'] }}</p>
    @endif

    {{-- Properties Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        {{-- Authorization --}}
        @if (!empty($form_request['authorization']))
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔐',
                'label' => 'Authorization',
                'value' => $form_request['authorization']['always_true'] ? 'Always Allow' : 
                          ($form_request['authorization']['always_false'] ? 'Always Deny' : 
                          ($form_request['authorization']['uses_can'] ? 'Policy-based' : 'Custom Logic')),
                'type' => 'simple'
            ])
        @endif

        {{-- Rules Count --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '📋',
            'label' => 'Validation Rules',
            'value' => count($form_request['rules']) . ' fields',
            'type' => 'simple'
        ])

        {{-- Full Class Name --}}
        @include('atlas::exports.partials.common.property-item', [
            'icon' => '🏷️',
            'label' => 'Full Class Name',
            'value' => $form_request['class'],
            'type' => 'code'
        ])
    </div>

    {{-- Validation Rules Table --}}
    @if (!empty($form_request['rules']))
        <div class="mb-4">
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '✅',
                'label' => 'Validation Rules',
                'type' => 'table'
            ])
            <table class="w-full text-xs border rounded overflow-hidden mt-1">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                    <tr>
                        <th class="p-2">Field</th>
                        <th class="p-2">Rules</th>
                        <th class="p-2">Custom Attribute</th>
                        <th class="p-2">Custom Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($form_request['rules'] as $field => $rules)
                        <tr class="border-t dark:border-gray-600">
                            <td class="p-2">
                                <code>{{ $field }}</code>
                                @if (in_array('required', $rules))
                                    <span class="ml-1 text-[10px] text-red-600">*</span>
                                @endif
                            </td>
                            <td class="p-2">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($rules as $rule)
                                        <span class="text-[10px] px-1.5 py-0.5 rounded 
                                            {{ str_contains($rule, 'required') ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' : 
                                               (str_contains($rule, 'unique') || str_contains($rule, 'exists') ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200' : 
                                               (str_contains($rule, 'new ') ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200' : 
                                               'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200')) }}">
                                            {{ $rule }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-2 text-gray-600 dark:text-gray-300">
                                {{ $form_request['attributes'][$field] ?? '-' }}
                            </td>
                            <td class="p-2 text-gray-600 dark:text-gray-300">
                                @php
                                    $messageKey = $field . '.required';
                                    $hasCustomMessage = false;
                                    foreach ($form_request['messages'] as $key => $message) {
                                        if (str_starts_with($key, $field . '.')) {
                                            $hasCustomMessage = true;
                                            break;
                                        }
                                    }
                                @endphp
                                {{ $hasCustomMessage ? '✓' : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Custom Messages --}}
    @if (!empty($form_request['messages']))
        <div class="mb-4">
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '💬',
                'label' => 'Custom Messages',
                'type' => 'table'
            ])
            <table class="w-full text-xs border rounded overflow-hidden mt-1">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                    <tr>
                        <th class="p-2">Rule</th>
                        <th class="p-2">Custom Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($form_request['messages'] as $rule => $message)
                        <tr class="border-t dark:border-gray-600">
                            <td class="p-2"><code>{{ $rule }}</code></td>
                            <td class="p-2 text-gray-600 dark:text-gray-300">{{ $message }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Methods Table --}}
    @if (!empty($form_request['methods']))
        <div class="mb-4">
            @include('atlas::exports.partials.common.property-item', [
                'icon' => '🔧',
                'label' => 'Methods',
                'type' => 'table'
            ])
            <table class="w-full text-xs border rounded overflow-hidden mt-1">
                <thead class="bg-gray-100 dark:bg-gray-700 text-left">
                    <tr>
                        <th class="p-2">Method</th>
                        <th class="p-2">Visibility</th>
                        <th class="p-2">Return Type</th>
                        <th class="p-2">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($form_request['methods'] as $method)
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
                                {{ $method['return_type'] ?? 'mixed' }}
                            </td>
                            <td class="p-2">
                                @if (in_array($method['name'], ['authorize', 'rules', 'attributes', 'messages']))
                                    <span class="text-[10px] text-green-600">[validation]</span>
                                @elseif (in_array($method['name'], ['withValidator', 'prepareForValidation']))
                                    <span class="text-[10px] text-blue-600">[lifecycle]</span>
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
        'flow' => $form_request['flow'] ?? [],
        'type' => 'form_request'
    ])

    {{-- Footer --}}
    @include('atlas::exports.partials.common.card-footer', [
        'class' => $form_request['class'],
        'file' => $form_request['file']
    ])
</div>
