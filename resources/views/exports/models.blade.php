<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Atlas – Models</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f8fafc; }
        h1 { color: #333; }
        .model { margin-bottom: 2rem; padding: 1rem; background: white; border-radius: 8px; box-shadow: 0 0 4px rgba(0,0,0,0.05); }
        .section { margin-top: 1rem; }
        code, pre { background: #f1f5f9; padding: 0.2rem 0.4rem; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; }
        th, td { text-align: left; padding: 0.3rem; border-bottom: 1px solid #e2e8f0; }
        th { background: #e2e8f0; }
    </style>
</head>
<body>
    <h1>📦 Laravel Atlas – Models</h1>

    @foreach ($models['data'] as $model)
        <div class="model">
            <h2>🧱 {{ $model['class'] }}</h2>
            <p><strong>Table:</strong> <code>{{ $model['table'] }}</code></p>

            <div class="section">
                <strong>Fillable:</strong><br>
                <code>{{ implode(', ', $model['fillable']) }}</code>
            </div>

            @if (!empty($model['casts']))
                <div class="section">
                    <strong>Casts:</strong>
                    <table>
                        <thead>
                            <tr><th>Field</th><th>Type</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($model['casts'] as $field => $type)
                                <tr>
                                    <td><code>{{ $field }}</code></td>
                                    <td>{{ $type }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif


            @if (!empty($model['relations']))
                <div class="section">
                    <strong>Relations:</strong>
                    <table>
                        <thead><tr><th>Name</th><th>Type</th><th>Target</th></tr></thead>
                        <tbody>
                            @foreach ($model['relations'] as $name => $rel)
                                <tr>
                                    <td><code>{{ $name }}</code></td>
                                    <td>{{ $rel['type'] }}</td>
                                    <td><code>{{ $rel['related'] }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (!empty($model['scopes']))
                <div class="section">
                    <strong>Scopes:</strong>
                    <table>
                        <thead><tr><th>Name</th><th>Parameters</th></tr></thead>
                        <tbody>
                            @foreach ($model['scopes'] as $scope)
                                <tr>
                                    <td><code>{{ $scope['name'] }}</code></td>
                                    <td>{{ implode(', ', $scope['parameters']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (!empty($model['booted_hooks']))
                <div class="section">
                    <strong>Boot hooks:</strong>
                    <code>{{ implode(', ', $model['booted_hooks']) }}</code>
                </div>
            @endif
        </div>
    @endforeach
</body>
</html>
