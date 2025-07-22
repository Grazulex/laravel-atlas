<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Atlas – Models</title>

    <!-- ✅ Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ✅ jQuery CDN (optionnel pour interactivité future) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        pre, code {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 p-8">

    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-indigo-700">📦 Laravel Atlas – Models</h1>

        @foreach ($models['data'] as $model)
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-indigo-800 mb-1">🧱 {{ $model['class'] }}</h2>
                <p class="text-sm text-gray-600 mb-3">Table: <code>{{ $model['table'] }}</code> — Primary Key: <code>{{ $model['primary_key'] }}</code></p>

                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Fillable --}}
                    <div>
                        <h3 class="font-semibold text-sm text-gray-500 mb-1">Fillable</h3>
                        <div class="bg-gray-100 rounded p-2 text-sm text-gray-800">
                            {{ implode(', ', $model['fillable']) }}
                        </div>
                    </div>

                    {{-- Casts --}}
                    @if (!empty($model['casts']))
                        <div>
                            <h3 class="font-semibold text-sm text-gray-500 mb-1">Casts</h3>
                            <table class="w-full text-sm border rounded overflow-hidden">
                                <thead class="bg-gray-200 text-left">
                                    <tr>
                                        <th class="p-2">Field</th>
                                        <th class="p-2">Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model['casts'] as $field => $type)
                                        <tr class="border-t">
                                            <td class="p-2"><code>{{ $field }}</code></td>
                                            <td class="p-2">{{ $type }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Relations --}}
                @if (!empty($model['relations']))
                    <div class="mt-6">
                        <h3 class="font-semibold text-sm text-gray-500 mb-1">Relations</h3>
                        <table class="w-full text-sm border rounded overflow-hidden">
                            <thead class="bg-gray-200 text-left">
                                <tr>
                                    <th class="p-2">Name</th>
                                    <th class="p-2">Type</th>
                                    <th class="p-2">Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($model['relations'] as $name => $rel)
                                    <tr class="border-t">
                                        <td class="p-2"><code>{{ $name }}</code></td>
                                        <td class="p-2">{{ $rel['type'] }}</td>
                                        <td class="p-2"><code>{{ $rel['related'] }}</code></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Scopes --}}
                @if (!empty($model['scopes']))
                    <div class="mt-6">
                        <h3 class="font-semibold text-sm text-gray-500 mb-1">Scopes</h3>
                        <table class="w-full text-sm border rounded overflow-hidden">
                            <thead class="bg-gray-200 text-left">
                                <tr>
                                    <th class="p-2">Name</th>
                                    <th class="p-2">Parameters</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($model['scopes'] as $scope)
                                    <tr class="border-t">
                                        <td class="p-2"><code>{{ $scope['name'] }}</code></td>
                                        <td class="p-2">{{ implode(', ', $scope['parameters']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Boot Hooks --}}
                @if (!empty($model['booted_hooks']))
                    <div class="mt-6">
                        <h3 class="font-semibold text-sm text-gray-500 mb-1">Boot Hooks</h3>
                        <div class="bg-gray-100 rounded p-2 text-sm text-gray-800">
                            {{ implode(', ', $model['booted_hooks']) }}
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

</body>
</html>
