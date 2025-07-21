<!-- Header -->
<header class="header">
    <h1>🗺️ {{ $data['metadata']['app_name'] ?? 'Laravel Application' }}</h1>
    <div class="app-info">
        <span>📊 Architecture Analysis</span>
        <span>⚡ {{ $data['metadata']['version'] ?? 'v1.0.0' }}</span>
        <span>📅 {{ date('Y-m-d H:i') }}</span>
    </div>
</header>
