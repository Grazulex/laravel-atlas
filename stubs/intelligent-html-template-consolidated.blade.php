<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Laravel Atlas - Architecture Report' }}</title>
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #333;
        line-height: 1.6;
    }
    
    .container {
        max-width: 1400px;
        margin: 0 auto;
        background: white;
        min-height: 100vh;
        display: grid;
        grid-template-columns: 280px 1fr;
        grid-template-rows: auto 1fr;
    }
    
    /* Header */
    .header {
        grid-column: 1 / -1;
        background: linear-gradient(45deg, #2c3e50, #3498db);
        color: white;
        padding: 20px;
        text-align: center;
    }
    
    .header h1 {
        font-size: 2.5em;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .app-info {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 15px;
        font-size: 0.9em;
    }
    
    /* Sidebar Navigation */
    .sidebar {
        background: #f8f9fa;
        border-right: 2px solid #e9ecef;
        padding: 20px 0;
        overflow-y: auto;
    }
    
    .nav-section {
        margin-bottom: 25px;
    }
    
    .nav-section h3 {
        color: #495057;
        font-size: 0.9em;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 20px 10px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .nav-item {
        display: block;
        padding: 12px 20px;
        text-decoration: none;
        color: #6c757d;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .nav-item:hover, .nav-item.active {
        background: #e9ecef;
        color: #495057;
        border-left-color: #007bff;
    }
    
    .nav-badge {
        background: #6c757d;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8em;
        margin-left: auto;
    }
    
    /* Main Content */
    .content {
        padding: 30px;
        overflow-y: auto;
    }
    
    .page {
        display: none;
    }
    
    .page.active {
        display: block;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Cards */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        margin-bottom: 25px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }
    
    .card-header {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-header h2 {
        color: #495057;
        font-size: 1.4em;
    }
    
    .card-body {
        padding: 25px;
    }
    
    /* Entry Points */
    .entry-points {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .entry-point {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    
    .entry-point:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    
    .entry-point.route {
        border-left: 4px solid #28a745;
    }
    
    .entry-point.command {
        border-left: 4px solid #ffc107;
    }
    
    .entry-point-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .entry-point-details {
        font-size: 0.9em;
        color: #6c757d;
    }
    
    /* Flow Visualization */
    .flow {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin: 15px 0;
    }
    
    .flow-step {
        display: flex;
        align-items: center;
        margin: 10px 0;
        padding: 8px 12px;
        background: white;
        border-radius: 6px;
        border-left: 3px solid #007bff;
    }
    
    .flow-step.async {
        border-left-color: #fd7e14;
        background: #fff3cd;
    }
    
    .flow-step-icon {
        width: 24px;
        height: 24px;
        margin-right: 12px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #007bff;
        color: white;
        font-size: 0.8em;
    }
    
    .flow-step.async .flow-step-icon {
        background: #fd7e14;
    }
    
    /* Component Details */
    .component-connections {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .connection-group {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
    }
    
    .connection-group h4 {
        color: #495057;
        margin-bottom: 10px;
        font-size: 0.9em;
    }
    
    .connection-item {
        display: block;
        padding: 5px 0;
        color: #6c757d;
        text-decoration: none;
        font-size: 0.85em;
        border-bottom: 1px solid #dee2e6;
    }
    
    .connection-item:hover {
        color: #007bff;
    }
    
    /* Badges and Labels */
    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75em;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-primary { background: #007bff; color: white; }
    .badge-success { background: #28a745; color: white; }
    .badge-warning { background: #ffc107; color: #212529; }
    .badge-info { background: #17a2b8; color: white; }
    .badge-secondary { background: #6c757d; color: white; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .container {
            grid-template-columns: 1fr;
            grid-template-rows: auto auto 1fr;
        }
        
        .sidebar {
            order: 2;
        }
        
        .content {
            order: 3;
        }
        
        .entry-points {
            grid-template-columns: 1fr;
        }
    }
    
    /* Tables */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    
    .data-table th,
    .data-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }
    
    .data-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }
    
    .data-table tbody tr:hover {
        background: #f8f9fa;
    }
</style>

</head>
<body>
    <div class="container">
        <!-- Header -->
<header class="header">
    <h1>🗺️ {{ $data['metadata']['app_name'] ?? 'Laravel Application' }}</h1>
    <div class="app-info">
        <span>📊 Architecture Analysis</span>
        <span>⚡ {{ $data['metadata']['version'] ?? 'v1.0.0' }}</span>
        <span>📅 {{ date('Y-m-d H:i') }}</span>
    </div>
</header>

        <!-- Sidebar Navigation -->
<nav class="sidebar">
    <div class="nav-section">
        <h3>📖 Documentation</h3>
        <a href="#legend" class="nav-item" onclick="showPage('legend')">
            Legend & Definitions <span class="nav-badge">ℹ️</span>
        </a>
    </div>
    
    <div class="nav-section">
        <h3>📍 Entry Points</h3>
        <a href="#overview" class="nav-item active" onclick="showPage('overview')">
            Overview <span class="nav-badge">{{ count($data['routes'] ?? []) + count($data['commands'] ?? []) }}</span>
        </a>
        <a href="#routes" class="nav-item" onclick="showPage('routes')">
            Routes <span class="nav-badge">{{ count($data['routes'] ?? []) }}</span>
        </a>
        <a href="#commands" class="nav-item" onclick="showPage('commands')">
            Commands <span class="nav-badge">{{ count($data['commands'] ?? []) }}</span>
        </a>
    </div>
    
    <div class="nav-section">
        <h3>🏗️ Architecture</h3>
        <a href="#flows" class="nav-item" onclick="showPage('flows')">
            Application Flows <span class="nav-badge">{{ count($data['flows'] ?? []) }}</span>
        </a>
        <a href="#models" class="nav-item" onclick="showPage('models')">
            Models <span class="nav-badge">{{ count($data['models'] ?? []) }}</span>
        </a>
        <a href="#observers" class="nav-item" onclick="showPage('observers')">
            Observers <span class="nav-badge">{{ count($data['observers'] ?? []) }}</span>
        </a>
        <a href="#actions" class="nav-item" onclick="showPage('actions')">
            Actions <span class="nav-badge">{{ count($data['actions'] ?? []) }}</span>
        </a>
        <a href="#services" class="nav-item" onclick="showPage('services')">
            Services <span class="nav-badge">{{ count($data['services'] ?? []) }}</span>
        </a>
    </div>
    
    <div class="nav-section">
        <h3>⚡ Async Components</h3>
        <a href="#jobs" class="nav-item" onclick="showPage('jobs')">
            Jobs <span class="nav-badge">{{ count($data['jobs'] ?? []) }}</span>
        </a>
        <a href="#events" class="nav-item" onclick="showPage('events')">
            Events <span class="nav-badge">{{ count($data['events'] ?? []) }}</span>
        </a>
        <a href="#listeners" class="nav-item" onclick="showPage('listeners')">
            Listeners <span class="nav-badge">{{ count($data['listeners'] ?? []) }}</span>
        </a>
    </div>
    
    <div class="nav-section">
        <h3>🔧 Other Components</h3>
        <a href="#controllers" class="nav-item" onclick="showPage('controllers')">
            Controllers <span class="nav-badge">{{ count($data['controllers'] ?? []) }}</span>
        </a>
        <a href="#middleware" class="nav-item" onclick="showPage('middleware')">
            Middleware <span class="nav-badge">{{ count($data['middleware'] ?? []) }}</span>
        </a>
        <a href="#policies" class="nav-item" onclick="showPage('policies')">
            Policies <span class="nav-badge">{{ count($data['policies'] ?? []) }}</span>
        </a>
        <a href="#resources" class="nav-item" onclick="showPage('resources')">
            Resources <span class="nav-badge">{{ count($data['resources'] ?? []) }}</span>
        </a>
        <a href="#notifications" class="nav-item" onclick="showPage('notifications')">
            Notifications <span class="nav-badge">{{ count($data['notifications'] ?? []) }}</span>
        </a>
        <a href="#requests" class="nav-item" onclick="showPage('requests')">
            Requests <span class="nav-badge">{{ count($data['requests'] ?? []) }}</span>
        </a>
        <a href="#rules" class="nav-item" onclick="showPage('rules')">
            Rules <span class="nav-badge">{{ count($data['rules'] ?? []) }}</span>
        </a>
    </div>
</nav>

        
        <!-- Main Content -->
        <main class="content">
<!-- Overview Page -->
<div id="overview" class="page active">
    <div class="card">
        <div class="card-header">
            <h2>🎯 Application Entry Points</h2>
        </div>
        <div class="card-body">
            <p>Your Laravel application has <strong>{{ count($data['routes'] ?? []) + count($data['commands'] ?? []) }}</strong> entry points. Click on any entry point to explore its execution flow.</p>
            
            <div class="entry-points">
                @if(isset($data['routes']))
                    @foreach($data['routes'] as $route)
                    <div class="entry-point route" onclick="showRouteDetails('{{ $route['name'] ?? $route['uri'] }}')">
                        <div class="entry-point-title">
                            🛣️ {{ $route['name'] ?? $route['uri'] }}
                        </div>
                        <div class="entry-point-details">
                            <span class="badge badge-success">{{ is_array($route['method'] ?? '') ? implode('|', $route['method']) : ($route['method'] ?? 'GET') }}</span>
                            {{ $route['uri'] }}
                        </div>
                    </div>
                    @endforeach
                @endif
                
                @if(isset($data['commands']))
                    @foreach($data['commands'] as $command)
                    <div class="entry-point command" onclick="showCommandDetails('{{ $command['name'] ?: $command['class_name'] }}')">
                        <div class="entry-point-title">
                            ⚡ {{ $command['name'] ?: class_basename($command['class_name'] ?? 'Unknown Command') }}
                        </div>
                        <div class="entry-point-details">
                            <span class="badge badge-warning">CMD</span>
                            {{ $command['description'] ?? 'No description available' }}
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>


<!-- Legend & Definitions Page -->
<div id="legend" class="page">
    <div class="card">
        <div class="card-header">
            <h2>📖 Legend & Definitions</h2>
        </div>
        <div class="card-body">
            <p>This page explains all the colors, badges, and symbols used throughout this architecture report.</p>
            
            <!-- Badges & Colors -->
            <div style="margin: 30px 0;">
                <h3>🏷️ Badges & Status Indicators</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                    
                    <div class="connection-group">
                        <h4>HTTP Methods</h4>
                        <div style="margin: 10px 0;"><span class="badge badge-success">GET</span> - Retrieve data</div>
                        <div style="margin: 10px 0;"><span class="badge badge-success">POST</span> - Create new resource</div>
                        <div style="margin: 10px 0;"><span class="badge badge-success">PUT/PATCH</span> - Update resource</div>
                        <div style="margin: 10px 0;"><span class="badge badge-success">DELETE</span> - Remove resource</div>
                    </div>
                    
                    <div class="connection-group">
                        <h4>Component Types</h4>
                        <div style="margin: 10px 0;"><span class="badge badge-warning">CMD</span> - Artisan Command</div>
                        <div style="margin: 10px 0;"><span class="badge badge-info">Queue: payments</span> - Background Job Queue</div>
                        <div style="margin: 10px 0;"><span class="badge badge-secondary">auth</span> - Middleware</div>
                        <div style="margin: 10px 0;"><span class="badge badge-primary">Synchronous</span> - Blocking execution</div>
                    </div>
                    
                    <div class="connection-group">
                        <h4>Execution Types</h4>
                        <div style="margin: 10px 0;"><span class="badge badge-primary">Synchronous</span> - Blocking, sequential</div>
                        <div style="margin: 10px 0;"><span class="badge badge-warning">Mixed</span> - Both sync & async</div>
                        <div style="margin: 10px 0;"><span class="badge badge-info">Asynchronous</span> - Non-blocking, queued</div>
                    </div>
                </div>
            </div>
            
            <!-- Entry Point Colors -->
            <div style="margin: 30px 0;">
                <h3>🎯 Entry Point Indicators</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div class="entry-point route" style="cursor: default;">
                        <div class="entry-point-title">
                            <span class="badge badge-success">GET</span>
                            /api/users
                        </div>
                        <div class="entry-point-details">
                            <strong>Route Entry Point</strong><br>
                            Green left border = HTTP Route
                        </div>
                    </div>
                    
                    <div class="entry-point command" style="cursor: default;">
                        <div class="entry-point-title">
                            <span class="badge badge-warning">CMD</span>
                            users:cleanup
                        </div>
                        <div class="entry-point-details">
                            <strong>Command Entry Point</strong><br>
                            Yellow left border = Artisan Command
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Flow Visualization -->
            <div style="margin: 30px 0;">
                <h3>🔄 Flow Step Indicators</h3>
                <div class="flow">
                    <div class="flow-step">
                        <div class="flow-step-icon">1</div>
                        <strong>Synchronous Step</strong> - Executed in order, blocks until complete
                    </div>
                    <div class="flow-step async">
                        <div class="flow-step-icon">A1</div>
                        <strong>Asynchronous Step</strong> - Queued/dispatched, non-blocking (orange background)
                    </div>
                </div>
            </div>
            
            <!-- Navigation Counters -->
            <div style="margin: 30px 0;">
                <h3>📊 Navigation Counters</h3>
                <p>Numbers in gray badges next to menu items indicate:</p>
                <div style="margin: 20px 0;">
                    <div style="display: flex; align-items: center; margin: 10px 0;">
                        <span style="margin-right: 15px;">Routes <span class="nav-badge">{{ count($data['routes'] ?? []) }}</span></span>
                        <span>= Total number of HTTP routes in your application</span>
                    </div>
                    <div style="display: flex; align-items: center; margin: 10px 0;">
                        <span style="margin-right: 15px;">Models <span class="nav-badge">{{ count($data['models'] ?? []) }}</span></span>
                        <span>= Total number of Eloquent models</span>
                    </div>
                    <div style="display: flex; align-items: center; margin: 10px 0;">
                        <span style="margin-right: 15px;">Jobs <span class="nav-badge">{{ count($data['jobs'] ?? []) }}</span></span>
                        <span>= Total number of background job classes</span>
                    </div>
                    <div style="display: flex; align-items: center; margin: 10px 0;">
                        <span style="margin-right: 15px;">Application Flows <span class="nav-badge">{{ count($data['flows'] ?? []) }}</span></span>
                        <span>= Number of defined execution flows</span>
                    </div>
                </div>
            </div>
            
            <!-- Component Connections -->
            <div style="margin: 30px 0;">
                <h3>🔗 Component Connections</h3>
                <p>Gray boxes show relationships between components:</p>
                <div class="component-connections" style="margin: 20px 0;">
                    <div class="connection-group">
                        <h4>Controllers</h4>
                        <span class="connection-item">UserController</span>
                        <span class="connection-item">OrderController</span>
                    </div>
                    <div class="connection-group">
                        <h4>Services</h4>
                        <span class="connection-item">UserService</span>
                        <span class="connection-item">OrderService</span>
                    </div>
                    <div class="connection-group">
                        <h4>Events</h4>
                        <span class="connection-item">UserCreated</span>
                        <span class="connection-item">OrderPlaced</span>
                    </div>
                </div>
                <p><em>These show which components are connected to/used by the current component.</em></p>
            </div>
            
            <!-- Tables -->
            <div style="margin: 30px 0;">
                <h3>📋 Table Features</h3>
                <p>Data tables include:</p>
                <ul style="margin: 15px 0 15px 30px;">
                    <li><strong>Hover effects</strong> - Rows highlight when you hover over them</li>
                    <li><strong>Badge indicators</strong> - Color-coded status and type information</li>
                    <li><strong>Clickable links</strong> - Navigate between related components</li>
                    <li><strong>Responsive design</strong> - Tables adapt to screen size</li>
                </ul>
            </div>
            
            <!-- Icons Reference -->
            <div style="margin: 30px 0;">
                <h3>🎨 Icon Reference</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>🗺️ Application Title</div>
                    <div>📍 Entry Points</div>
                    <div>🏗️ Architecture</div>
                    <div>⚡ Async Components</div>
                    <div>🔧 Other Components</div>
                    <div>🛣️ Routes</div>
                    <div>⚡ Commands</div>
                    <div>🔄 Flows</div>
                    <div>📊 Models</div>
                    <div>🔧 Services</div>
                    <div>⚡ Jobs</div>
                    <div>📡 Events</div>
                    <div>👂 Listeners</div>
                    <div>🎮 Controllers</div>
                    <div>🛡️ Policies</div>
                    <div>� Middleware</div>
                    <div>🎯 Resources</div>
                    <div>📬 Notifications</div>
                    <div>📝 Requests</div>
                    <div>✅ Rules</div>
                    <div>👀 Observers</div>
                    <div>🎬 Actions</div>
                    <div>�📖 Documentation</div>
                </div>
            </div>
            
        </div>
    </div>
</div>


<!-- Routes Page -->
<div id="routes" class="page">
    @if (isset($data['routes']) && !empty($data['routes']))
        <div class="card">
            <div class="card-header">
                <h2>🛣️ Application Routes</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>URI</th>
                                <th>Name</th>
                                <th>Controller</th>
                                <th>Action</th>
                                <th>Middleware</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['routes'] as $route)
                                <tr>
                                    <td><span class="badge badge-success">{{ is_array($route['method'] ?? '') ? implode('|', $route['method']) : ($route['method'] ?? 'GET') }}</span></td>
                                    <td><code>{{ $route['uri'] }}</code></td>
                                    <td>{{ $route['name'] ?? '-' }}</td>
                                    <td>
                                        @if(is_array($route['controller'] ?? ''))
                                            @if(isset($route['controller']['short_name']))
                                                {{ $route['controller']['short_name'] }}
                                            @elseif(isset($route['controller']['class']))
                                                {{ class_basename($route['controller']['class']) }}
                                            @else
                                                [Complex Controller]
                                            @endif
                                        @else
                                            {{ $route['controller'] ? class_basename($route['controller']) : 'Closure' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($route['controller']['method']))
                                            {{ $route['controller']['method'] }}
                                        @elseif(is_array($route['action'] ?? null))
                                            @if(isset($route['action']['uses']) && strpos($route['action']['uses'], '@') !== false)
                                                {{ explode('@', $route['action']['uses'])[1] ?? '__invoke' }}
                                            @else
                                                __invoke
                                            @endif
                                        @else
                                            {{ is_string($route['action'] ?? null) ? $route['action'] : 'handle' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($route['middleware']) && !empty($route['middleware']))
                                            @if(is_array($route['middleware']))
                                                @foreach($route['middleware'] as $middleware)
                                                    <span class="badge badge-secondary">{{ $middleware }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge badge-secondary">{{ $route['middleware'] }}</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <p>No routes found.</p>
    @endif
</div>


<!-- Commands Page -->
<div id="commands" class="page">
    <div class="card">
        <div class="card-header">
            <h2>⚡ Artisan Commands</h2>
        </div>
        <div class="card-body">
            @if(isset($data['commands']))
                @foreach($data['commands'] as $command)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ $command['signature_info']['name'] ?? class_basename($command['class_name'] ?? 'Unknown Command') }}</h3>
                        @if(isset($command['signature_info']['signature']))
                            <code style="background: #f8f9fa; padding: 8px 12px; border-radius: 4px; display: block; margin-top: 8px;">{{ $command['signature_info']['signature'] }}</code>
                        @else
                            <small style="color: #6c757d;">{{ $command['class_name'] ?? '' }}</small>
                        @endif
                    </div>
                    <div class="card-body">
                        <p style="margin-bottom: 20px; font-size: 1.1em;">{{ $command['signature_info']['description'] ?? 'No description available' }}</p>
                        
                        <!-- Arguments Section -->
                        @if(isset($command['arguments']) && count($command['arguments']) > 0)
                        <div style="margin-bottom: 25px;">
                            <h4 style="color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 8px; margin-bottom: 15px;">📝 Arguments</h4>
                            <div style="display: grid; gap: 12px;">
                                @foreach($command['arguments'] as $argument)
                                <div style="background: #f8f9fa; padding: 12px; border-left: 4px solid #28a745; border-radius: 4px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                                        <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-weight: bold;">{{ $argument['name'] }}</code>
                                        @if($argument['optional'])
                                            <span style="background: #ffc107; color: #212529; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; font-weight: bold;">Optional</span>
                                        @else
                                            <span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; font-weight: bold;">Required</span>
                                        @endif
                                        @if($argument['has_default'])
                                            <span style="background: #6c757d; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em;">Default: {{ $argument['default_value'] }}</span>
                                        @endif
                                    </div>
                                    @if($argument['description'])
                                    <div style="color: #6c757d; font-size: 0.9em;">{{ $argument['description'] }}</div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Options Section -->
                        @if(isset($command['options']) && count($command['options']) > 0)
                        <div style="margin-bottom: 25px;">
                            <h4 style="color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 8px; margin-bottom: 15px;">⚙️ Options</h4>
                            <div style="display: grid; gap: 12px;">
                                @foreach($command['options'] as $option)
                                <div style="background: #f8f9fa; padding: 12px; border-left: 4px solid #007bff; border-radius: 4px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                                        <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-weight: bold;">{{ $option['name'] }}</code>
                                        @if($option['has_default'])
                                            <span style="background: #28a745; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em;">Default: {{ $option['default_value'] }}</span>
                                        @endif
                                    </div>
                                    @if($option['description'])
                                    <div style="color: #6c757d; font-size: 0.9em;">{{ $option['description'] }}</div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Dependencies Section -->
                        @if(isset($command['dependencies']) && count($command['dependencies']) > 0)
                        <div style="margin-bottom: 20px;">
                            <h4 style="color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 8px; margin-bottom: 15px;">🔗 Dependencies</h4>
                            <div style="display: grid; gap: 8px;">
                                @foreach($command['dependencies'] as $dependency)
                                <div style="background: #e9ecef; padding: 8px 12px; border-radius: 4px; display: flex; align-items: center; gap: 8px;">
                                    <code style="background: white; padding: 2px 6px; border-radius: 3px;">{{ $dependency['name'] }}</code>
                                    <span style="color: #6c757d; font-size: 0.9em;">{{ $dependency['type'] }}</span>
                                    @if($dependency['is_service'])
                                        <span style="background: #17a2b8; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em;">Service</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Class Information -->
                        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                            <h5 style="color: #6c757d; margin-bottom: 10px;">Class Information</h5>
                            <div style="display: grid; gap: 5px; font-size: 0.9em; color: #6c757d;">
                                <div><strong>Class:</strong> {{ $command['class_name'] ?? 'Unknown' }}</div>
                                <div><strong>Namespace:</strong> {{ $command['namespace'] ?? 'Unknown' }}</div>
                                @if(isset($command['parent_class']))
                                <div><strong>Extends:</strong> {{ class_basename($command['parent_class']) }}</div>
                                @endif
                            </div>
                        </div>
                        
                        @if(isset($command['flows']))
                        <h4>Execution Flow</h4>
                        <div class="flow">
                            @if(isset($command['flows']['synchronous']))
                            <h5>Synchronous Steps</h5>
                            @foreach($command['flows']['synchronous'] as $step)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ is_array($step) ? implode(', ', $step) : $step }}
                            </div>
                            @endforeach
                            @endif
                            
                            @if(isset($command['flows']['asynchronous']))
                            <h5>Asynchronous Events</h5>
                            @foreach($command['flows']['asynchronous'] as $step)
                            <div class="flow-step async">
                                <div class="flow-step-icon">A{{ $loop->iteration }}</div>
                                {{ is_array($step) ? implode(', ', $step) : $step }}
                            </div>
                            @endforeach
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No commands found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Application Flows -->
<div id="flows" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🔄 Application Flows</h2>
        </div>
        <div class="card-body">
            @if(isset($data['flows']))
                @foreach($data['flows'] as $flow)
                <div class="flow">
                    <h3>{{ $flow['name'] }}</h3>
                    <p><strong>Entry Point:</strong> {{ $flow['entry_point'] }}</p>
                    <p><strong>Type:</strong> <span class="badge badge-{{ $flow['type'] == 'mixed' ? 'warning' : ($flow['type'] == 'synchronous' ? 'primary' : 'info') }}">{{ ucfirst($flow['type']) }}</span></p>
                    
                    <div style="margin-top: 15px;">
                        @foreach($flow['steps'] as $step)
                        <div class="flow-step {{ strpos($step, '(async)') !== false ? 'async' : '' }}">
                            <div class="flow-step-icon">{{ $loop->iteration }}</div>
                            {{ str_replace('(async)', '', $step) }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @else
                <p>No application flows defined.</p>
            @endif
        </div>
    </div>
</div>


<!-- Models Page -->
<div id="models" class="page">
    <div class="card">
        <div class="card-header">
            <h2>📊 Data Models & Relationships</h2>
        </div>
        <div class="card-body">
            @if(isset($data['models']))
                @foreach($data['models'] as $model)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($model['class_name']) }}</h3>
                        <small>{{ $model['class_name'] }} → {{ $model['attributes']['table'] ?? 'unknown' }}</small>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <h4>Attributes</h4>
                                @if(isset($model['attributes']) && is_array($model['attributes']))
                                    @if(isset($model['attributes']['fillable']) && is_array($model['attributes']['fillable']) && !empty($model['attributes']['fillable']))
                                        <p><strong>Fillable:</strong></p>
                                        <ul>
                                            @foreach($model['attributes']['fillable'] as $field)
                                            <li>{{ $field }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    
                                    @if(isset($model['attributes']['hidden']) && is_array($model['attributes']['hidden']) && !empty($model['attributes']['hidden']))
                                        <p><strong>Hidden:</strong></p>
                                        <ul>
                                            @foreach($model['attributes']['hidden'] as $field)
                                            <li>{{ $field }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    
                                    @if(isset($model['attributes']['casts']) && is_array($model['attributes']['casts']) && !empty($model['attributes']['casts']))
                                        <p><strong>Casts:</strong></p>
                                        <ul>
                                            @foreach($model['attributes']['casts'] as $field => $type)
                                            <li>{{ $field }} → {{ $type }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    
                                    @if(isset($model['attributes']['dates']) && is_array($model['attributes']['dates']) && !empty($model['attributes']['dates']))
                                        <p><strong>Dates:</strong></p>
                                        <ul>
                                            @foreach($model['attributes']['dates'] as $field)
                                            <li>{{ $field }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    
                                    @if(isset($model['attributes']['primary_key']))
                                        <p><strong>Primary Key:</strong> {{ $model['attributes']['primary_key'] }}</p>
                                    @endif
                                    
                                    @if(isset($model['attributes']['timestamps']) && $model['attributes']['timestamps'])
                                        <p><strong>Timestamps:</strong> Yes</p>
                                    @endif
                                @else
                                    <p><em>No attributes found</em></p>
                                @endif
                            </div>
                            <div>
                                <h4>Relationships</h4>
                                @if(isset($model['relationships']) && !empty($model['relationships']))
                                    @foreach($model['relationships'] as $relationName => $relationData)
                                    <div style="margin-bottom: 10px;">
                                        <p><strong>{{ ucfirst($relationName) }}:</strong></p>
                                        @if(is_array($relationData))
                                            <ul style="margin-left: 20px;">
                                                @foreach($relationData as $key => $value)
                                                <li>{{ $key }}: {{ is_array($value) ? implode(', ', $value) : $value }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span style="margin-left: 20px;">{{ $relationData }}</span>
                                        @endif
                                    </div>
                                    @endforeach
                                @else
                                    <p><em>No relationships found</em></p>
                                @endif
                            </div>
                        </div>
                        
                        @if(isset($model['connected_to']))
                        <div class="component-connections">
                            @foreach($model['connected_to'] as $type => $components)
                            <div class="connection-group">
                                <h4>{{ is_array($type) ? 'Mixed' : ucfirst($type) }}</h4>
                                @foreach($components as $component)
                                <span class="connection-item">{{ is_array($component) ? 'Mixed' : class_basename($component) }}</span>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No models found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Observers Page -->
<div id="observers" class="page">
    <div class="card">
        <div class="card-header">
            <h2>👁️ Model Observers</h2>
        </div>
        <div class="card-body">
            @if(isset($data['observers']))
                @foreach($data['observers'] as $observer)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($observer['class_name']) }}</h3>
                        <small>Observes: {{ class_basename($observer['model']) }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($observer['methods']) && is_array($observer['methods']))
                        <h4>Lifecycle Hooks</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                            @foreach($observer['methods'] as $method)
                            <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid {{ $method['type'] == 'before' ? '#ffc107' : '#28a745' }};">
                                <strong>{{ $method['name'] }}()</strong>
                                <br><small>
                                    <span class="badge badge-{{ $method['type'] == 'before' ? 'warning' : 'success' }}">
                                        {{ ucfirst($method['type']) }}
                                    </span>
                                    {{ $method['type'] == 'before' ? 'Pre-action hook' : 'Post-action hook' }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                        @else
                            <p><em>No lifecycle hooks found</em></p>
                        @endif
                        
                        @if(isset($observer['dependencies']) && count($observer['dependencies']) > 0)
                        <h4>Dependencies</h4>
                        <ul>
                            @foreach($observer['dependencies'] as $dependency)
                            <li>{{ class_basename($dependency) }}</li>
                            @endforeach
                        </ul>
                        @endif
                        
                        @if(isset($observer['events']) && count($observer['events']) > 0)
                        <h4>Events Triggered</h4>
                        <ul>
                            @foreach($observer['events'] as $event)
                            <li>{{ class_basename($event) }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No observers found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Actions Page -->
<div id="actions" class="page">
    <div class="card">
        <div class="card-header">
            <h2>⚡ Business Actions</h2>
        </div>
        <div class="card-body">
            @if(isset($data['actions']))
                @foreach($data['actions'] as $action)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($action['class_name']) }}</h3>
                        <div>
                            <span class="badge badge-{{ $action['type'] ?? 'custom' }}">{{ ucfirst($action['type'] ?? 'custom') }}</span>
                            @if(isset($action['is_invokable']) && $action['is_invokable'])
                                <span class="badge badge-success">Invokable</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($action['methods']) && is_array($action['methods']) && count($action['methods']) > 0)
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($action['methods'] as $methodName => $methodDetails)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid {{ $methodName == '__invoke' ? '#17a2b8' : '#6c757d' }};">
                                <strong>{{ $methodName }}()</strong>
                                @if(isset($methodDetails['parameters']) && count($methodDetails['parameters']) > 0)
                                <br><small><strong>Parameters:</strong></small>
                                <ul style="margin: 5px 0; font-size: 0.9em;">
                                    @foreach($methodDetails['parameters'] as $param)
                                    <li>
                                        <strong>{{ $param['name'] }}</strong>: {{ $param['type'] ?? 'mixed' }}
                                        @if(isset($param['required']) && !$param['required'])
                                            <span class="badge badge-secondary" style="font-size: 0.7em;">optional</span>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                                @if(isset($methodDetails['return_type']))
                                <br><small><strong>Returns:</strong> {{ $methodDetails['return_type'] }}</small>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                            <p><em>No method details found</em></p>
                        @endif
                        
                        @if(isset($action['dependencies']) && count($action['dependencies']) > 0)
                        <h4>Dependency Injection</h4>
                        <div class="flow">
                            @foreach($action['dependencies'] as $dependency)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ class_basename($dependency) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($action['events']) && count($action['events']) > 0)
                        <h4>Events Dispatched</h4>
                        <div class="flow">
                            @foreach($action['events'] as $event)
                            <div class="flow-step async">
                                <div class="flow-step-icon">A{{ $loop->iteration }}</div>
                                {{ class_basename($event) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(!isset($action['dependencies']) || count($action['dependencies']) == 0)
                            @if(!isset($action['events']) || count($action['events']) == 0)
                            <p><em>This action has no dependencies or events.</em></p>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No actions found.</p>
            @endif
        </div>
    </div>
</div>

<!-- Services Page -->
<div id="services" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🔧 Business Services</h2>
        </div>
        <div class="card-body">
            @if(isset($data['services']) && is_array($data['services']) && count($data['services']) > 0)
                @foreach($data['services'] as $service)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($service['class_name']) }}</h3>
                        <small>{{ $service['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($service['methods']) && is_array($service['methods']) && count($service['methods']) > 0)
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 15px;">
                            @foreach($service['methods'] as $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #17a2b8;">
                                <div style="margin-bottom: 10px;">
                                    <strong style="font-size: 1.1em;">{{ $method['name'] ?? 'unknown' }}()</strong>
                                    @if(isset($method['visibility']))
                                        <span style="background: {{ $method['visibility'] === 'public' ? '#28a745' : ($method['visibility'] === 'protected' ? '#ffc107' : '#6c757d') }}; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; margin-left: 8px;">{{ ucfirst($method['visibility']) }}</span>
                                    @endif
                                    @if(isset($method['is_static']) && $method['is_static'])
                                        <span style="background: #6f42c1; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75em; margin-left: 8px;">Static</span>
                                    @endif
                                </div>
                                
                                @if(isset($method['parameters']) && is_array($method['parameters']) && count($method['parameters']) > 0)
                                <div style="margin-bottom: 10px;">
                                    <small style="color: #6c757d; font-weight: bold;">Parameters:</small>
                                    <ul style="margin: 5px 0 0 20px; font-size: 0.9em;">
                                        @foreach($method['parameters'] as $param)
                                        <li>
                                            <code>${{ $param['name'] }}</code>: {{ $param['type'] ?? 'mixed' }}
                                            @if(isset($param['optional']) && $param['optional'])
                                                <span style="background: #ffc107; color: #212529; padding: 1px 4px; border-radius: 8px; font-size: 0.7em;">optional</span>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                
                                @if(isset($method['return_type']) && $method['return_type'])
                                <div style="margin-bottom: 10px;">
                                    <small style="color: #6c757d; font-weight: bold;">Returns:</small>
                                    <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px; margin-left: 5px;">{{ $method['return_type'] }}</code>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                            <p><em>No methods found</em></p>
                        @endif
                        
                        @if(isset($service['dependencies']) && is_array($service['dependencies']) && count($service['dependencies']) > 0)
                        <h4>Dependencies</h4>
                        <div class="flow">
                            @foreach($service['dependencies'] as $dependency)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ class_basename($dependency) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($service['interfaces']) && is_array($service['interfaces']) && count($service['interfaces']) > 0)
                        <h4>Implements</h4>
                        <div style="margin: 10px 0;">
                            @foreach($service['interfaces'] as $interface)
                                <span style="background: #6f42c1; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.85em; margin-right: 8px; margin-bottom: 4px; display: inline-block;">{{ class_basename($interface) }}</span>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($service['traits']) && is_array($service['traits']) && count($service['traits']) > 0)
                        <h4>Uses Traits</h4>
                        <div style="margin: 10px 0;">
                            @foreach($service['traits'] as $trait)
                                <span style="background: #fd7e14; color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.85em; margin-right: 8px; margin-bottom: 4px; display: inline-block;">{{ class_basename($trait) }}</span>
                            @endforeach
                        </div>
                        @endif
                        
                        <!-- Class Information -->
                        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                            <h5 style="color: #6c757d; margin-bottom: 10px;">Class Information</h5>
                            <div style="display: grid; gap: 5px; font-size: 0.9em; color: #6c757d;">
                                <div><strong>Namespace:</strong> {{ $service['namespace'] ?? 'Unknown' }}</div>
                                @if(isset($service['parent_class']) && $service['parent_class'])
                                <div><strong>Extends:</strong> {{ class_basename($service['parent_class']) }}</div>
                                @endif
                                @if(isset($service['is_abstract']) && $service['is_abstract'])
                                <div><strong>Type:</strong> Abstract Class</div>
                                @elseif(isset($service['is_interface']) && $service['is_interface'])
                                <div><strong>Type:</strong> Interface</div>
                                @else
                                <div><strong>Type:</strong> Concrete Class</div>
                                @endif
                            </div>
                        </div>
                        
                        @if(isset($service['connected_to']))
                        <div class="component-connections">
                            @foreach($service['connected_to'] as $type => $components)
                            <div class="connection-group">
                                <h4>{{ is_array($type) ? 'Mixed' : ucfirst($type) }}</h4>
                                @foreach($components as $component)
                                <span class="connection-item">{{ is_array($component) ? 'Mixed' : class_basename($component) }}</span>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No services found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Jobs Page -->
<div id="jobs" class="page">
    <div class="card">
        <div class="card-header">
            <h2>⚡ Background Jobs</h2>
        </div>
        <div class="card-body">
            @if(isset($data['jobs']))
                @foreach($data['jobs'] as $job)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($job['class_name']) }}</h3>
                        <div>
                            <span class="badge badge-info">Queue: {{ $job['queue'] ?? 'default' }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($job['triggered_by']))
                        <h4>Triggered By</h4>
                        <div class="flow">
                            @foreach($job['triggered_by'] as $trigger)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ class_basename($trigger) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($job['dependencies']))
                        <h4>Dependencies</h4>
                        <div class="flow">
                            @foreach($job['dependencies'] as $dep)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ class_basename($dep) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($job['events']))
                        <h4>Events Dispatched</h4>
                        <div class="flow">
                            @foreach($job['events'] as $event)
                            <div class="flow-step async">
                                <div class="flow-step-icon">A{{ $loop->iteration }}</div>
                                {{ class_basename($event) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No jobs found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Events Page -->
<div id="events" class="page">
    <div class="card">
        <div class="card-header">
            <h2>📡 Application Events</h2>
        </div>
        <div class="card-body">
            @if(isset($data['events']))
                @foreach($data['events'] as $event)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($event['class_name']) }}</h3>
                        <small>{{ $event['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($event['properties']) && is_array($event['properties']))
                        <p><strong>Properties:</strong></p>
                        <ul>
                            @foreach($event['properties'] as $property)
                                @if(is_array($property) && isset($property['name']))
                                    <li>{{ $property['name'] }} ({{ $property['type'] ?? 'mixed' }})</li>
                                @else
                                    <li>{{ is_array($property) ? implode(', ', $property) : $property }}</li>
                                @endif
                            @endforeach
                        </ul>
                        @elseif(isset($event['properties']))
                        <p><strong>Properties:</strong> {{ implode(', ', $event['properties']) }}</p>
                        @endif
                        
                        @php
                        // Dynamically find what triggers this event by checking actions
                        $eventName = class_basename($event['class_name']);
                        $triggers = [];
                        if(isset($data['actions'])) {
                            foreach($data['actions'] as $action) {
                                if(isset($action['events']) && is_array($action['events'])) {
                                    foreach($action['events'] as $actionEvent) {
                                        if(class_basename($actionEvent) === $eventName) {
                                            $triggers[] = $action['class_name'];
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        @endphp
                        
                        @if(!empty($triggers))
                        <h4>Triggered By</h4>
                        <div class="flow">
                            @foreach($triggers as $trigger)
                            <div class="flow-step">
                                <div class="flow-step-icon">{{ $loop->iteration }}</div>
                                {{ class_basename($trigger) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        @if(isset($event['potential_listeners']) && !empty($event['potential_listeners']))
                        <h4>Event Listeners</h4>
                        <div class="flow">
                            @foreach($event['potential_listeners'] as $listener)
                            <div class="flow-step async">
                                <div class="flow-step-icon">A{{ $loop->iteration }}</div>
                                {{ class_basename($listener) }}
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No events found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Listeners Page -->
<div id="listeners" class="page">
    <div class="card">
        <div class="card-header">
            <h2>👂 Event Listeners</h2>
        </div>
        <div class="card-body">
            @if(isset($data['listeners']))
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Listener</th>
                            <th>Event</th>
                            <th>Dependencies</th>
                            <th>Jobs Dispatched</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['listeners'] as $listener)
                        <tr>
                            <td>{{ class_basename($listener['class_name']) }}</td>
                            <td>{{ class_basename($listener['event']) }}</td>
                            <td>
                                @if(isset($listener['dependencies']))
                                    {{ implode(', ', array_map('class_basename', $listener['dependencies'])) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(isset($listener['jobs']))
                                    {{ implode(', ', array_map('class_basename', $listener['jobs'])) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No listeners found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Controllers Page -->
<div id="controllers" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🎮 HTTP Controllers</h2>
        </div>
        <div class="card-body">
            @if(isset($data['controllers']))
                @foreach($data['controllers'] as $controller)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($controller['class_name']) }}</h3>
                        <small>{{ $controller['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($controller['methods']))
                        <h4>Methods</h4>
                        @if(is_array($controller['methods']) && count($controller['methods']) > 0)
                            @if(is_numeric(array_keys($controller['methods'])[0]))
                                <p><em>{{ count($controller['methods']) }} methods found</em></p>
                            @else
                                @foreach($controller['methods'] as $method => $details)
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                                    <strong>{{ is_array($method) ? implode(', ', $method) : $method }}()</strong>
                                    @if(isset($details['dependencies']))
                                    <br><small><strong>Dependencies:</strong> {{ implode(', ', array_map('class_basename', $details['dependencies'])) }}</small>
                                    @endif
                                    @if(isset($details['events']))
                                    <br><small><strong>Events:</strong> {{ implode(', ', array_map('class_basename', $details['events'])) }}</small>
                                    @endif
                                </div>
                                @endforeach
                            @endif
                        @else
                            <p><em>No method details available</em></p>
                        @endif
                        @endif
                        
                        @if(isset($controller['connected_to']))
                        <div class="component-connections">
                            @foreach($controller['connected_to'] as $type => $components)
                            <div class="connection-group">
                                <h4>{{ is_array($type) ? 'Mixed' : ucfirst($type) }}</h4>
                                @foreach($components as $component)
                                <span class="connection-item">{{ is_array($component) ? 'Mixed' : class_basename($component) }}</span>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No controllers found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Policies Page -->
<div id="policies" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🛡️ Authorization Policies</h2>
        </div>
        <div class="card-body">
            @if(isset($data['policies']))
                @foreach($data['policies'] as $policy)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($policy['class_name']) }}</h3>
                        <small>Model: {{ class_basename($policy['model']) }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($policy['methods']))
                        @if(is_array($policy['methods']) && count($policy['methods']) > 0)
                            @if(isset($policy['methods'][0]['name']))
                                <p><strong>Methods:</strong> 
                                @foreach($policy['methods'] as $method)
                                    {{ $method['name'] ?? 'unknown' }}@if(!$loop->last), @endif
                                @endforeach
                                </p>
                            @else
                                <p><strong>Methods:</strong> {{ implode(', ', $policy['methods']) }}</p>
                            @endif
                        @else
                            <p><strong>Methods:</strong> <em>No methods available</em></p>
                        @endif
                        @endif
                        
                        @if(isset($policy['connected_to']))
                        <div class="component-connections">
                            @foreach($policy['connected_to'] as $type => $components)
                            <div class="connection-group">
                                <h4>{{ is_array($type) ? 'Mixed' : ucfirst($type) }}</h4>
                                @foreach($components as $component)
                                <span class="connection-item">{{ is_array($component) ? 'Mixed' : class_basename($component) }}</span>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No policies found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Middleware Page -->
<div id="middleware" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🛂 HTTP Middleware</h2>
        </div>
        <div class="card-body">
            @if(isset($data['middleware']))
                @foreach($data['middleware'] as $middleware)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($middleware['class_name']) }}</h3>
                        <small>{{ $middleware['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($middleware['methods']) && is_array($middleware['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($middleware['methods'] as $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #0d6efd;">
                                <strong>{{ $method['name'] }}()</strong>
                                @if(isset($method['parameters']) && !empty($method['parameters']))
                                    <br><small>Parameters:</small>
                                    @foreach($method['parameters'] as $param)
                                        <br><span class="badge bg-light text-dark">{{ $param['type'] ?? 'mixed' }} ${{ $param['name'] }}</span>
                                    @endforeach
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($middleware['dependencies']) && !empty($middleware['dependencies']))
                        <h4>Dependencies</h4>
                        <ul>
                            @foreach($middleware['dependencies'] as $dependency)
                                <li><code>{{ $dependency }}</code></li>
                            @endforeach
                        </ul>
                        @endif

                        @if(isset($middleware['priority']))
                        <h4>Priority</h4>
                        <span class="badge bg-info">{{ $middleware['priority'] }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No middleware found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Resources Page -->
<div id="resources" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🎯 API Resources</h2>
        </div>
        <div class="card-body">
            @if(isset($data['resources']))
                @foreach($data['resources'] as $resource)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($resource['class_name']) }}</h3>
                        <small>{{ $resource['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($resource['type']))
                        <h4>Resource Type</h4>
                        <span class="badge bg-primary">{{ ucfirst($resource['type']) }}</span>
                        @endif

                        @if(isset($resource['model']) && $resource['model'])
                        <h4>Associated Model</h4>
                        <code>{{ $resource['model'] }}</code>
                        @endif

                        @if(isset($resource['methods']) && is_array($resource['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($resource['methods'] as $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #28a745;">
                                <strong>{{ $method['name'] }}()</strong>
                                @if(isset($method['parameters']) && !empty($method['parameters']))
                                    <br><small>Parameters:</small>
                                    @foreach($method['parameters'] as $param)
                                        <br><span class="badge bg-light text-dark">{{ $param['type'] ?? 'mixed' }} ${{ $param['name'] }}</span>
                                    @endforeach
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($resource['relationships']) && !empty($resource['relationships']))
                        <h4>Relationships</h4>
                        <ul>
                            @foreach($resource['relationships'] as $relationship)
                                <li><strong>{{ $relationship['name'] }}</strong> ({{ $relationship['type'] ?? 'unknown' }})</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No resources found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Notifications Page -->
<div id="notifications" class="page">
    <div class="card">
        <div class="card-header">
            <h2>📬 Notifications</h2>
        </div>
        <div class="card-body">
            @if(isset($data['notifications']))
                @foreach($data['notifications'] as $notification)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($notification['class_name']) }}</h3>
                        <small>{{ $notification['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($notification['channels']) && !empty($notification['channels']))
                        <h4>Channels</h4>
                        <div style="margin-bottom: 15px;">
                            @foreach($notification['channels'] as $channel)
                                <span class="badge bg-info">{{ $channel }}</span>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($notification['methods']) && is_array($notification['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($notification['methods'] as $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #ffc107;">
                                <strong>{{ $method['name'] }}()</strong>
                                @if(isset($method['parameters']) && !empty($method['parameters']))
                                    <br><small>Parameters:</small>
                                    @foreach($method['parameters'] as $param)
                                        <br><span class="badge bg-light text-dark">{{ $param['type'] ?? 'mixed' }} ${{ $param['name'] }}</span>
                                    @endforeach
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($notification['dependencies']) && !empty($notification['dependencies']))
                        <h4>Dependencies</h4>
                        <ul>
                            @foreach($notification['dependencies'] as $dependency)
                                <li><code>{{ $dependency }}</code></li>
                            @endforeach
                        </ul>
                        @endif

                        @if(isset($notification['data_structure']) && !empty($notification['data_structure']))
                        <h4>Data Structure</h4>
                        <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px;"><code>{{ json_encode($notification['data_structure'], JSON_PRETTY_PRINT) }}</code></pre>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No notifications found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Requests Page -->
<div id="requests" class="page">
    <div class="card">
        <div class="card-header">
            <h2>📝 Form Requests</h2>
        </div>
        <div class="card-body">
            @if(isset($data['requests']))
                @foreach($data['requests'] as $request)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($request['class_name']) }}</h3>
                        <small>{{ $request['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($request['validation_rules']) && !empty($request['validation_rules']))
                        <h4>Validation Rules</h4>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Rules</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request['validation_rules'] as $rule)
                                    <tr>
                                        <td><code>{{ $rule['field'] ?? 'unknown' }}</code></td>
                                        <td>
                                            @if(isset($rule['rules']) && is_array($rule['rules']))
                                                @foreach($rule['rules'] as $validationRule)
                                                    <span class="badge bg-secondary">{{ $validationRule }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge bg-secondary">{{ $rule['rules'] ?? 'no rules' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        @if(isset($request['custom_messages']) && !empty($request['custom_messages']))
                        <h4>Custom Messages</h4>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rule</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request['custom_messages'] as $rule => $message)
                                    <tr>
                                        <td><code>{{ $rule }}</code></td>
                                        <td>{{ $message }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                        @if(isset($request['authorization']) && $request['authorization'] !== null)
                        <h4>Authorization</h4>
                        <span class="badge bg-{{ $request['authorization'] ? 'success' : 'warning' }}">
                            {{ $request['authorization'] ? 'Required' : 'Not Required' }}
                        </span>
                        @endif

                        @if(isset($request['methods']) && is_array($request['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                            @foreach($request['methods'] as $method)
                            <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #6f42c1;">
                                <strong>{{ $method['name'] }}()</strong>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No form requests found.</p>
            @endif
        </div>
    </div>
</div>


<!-- Rules Page -->
<div id="rules" class="page">
    <div class="card">
        <div class="card-header">
            <h2>✅ Validation Rules</h2>
        </div>
        <div class="card-body">
            @if(isset($data['rules']))
                @foreach($data['rules'] as $rule)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($rule['class_name']) }}</h3>
                        <small>{{ $rule['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($rule['rule_type']))
                        <h4>Rule Type</h4>
                        <span class="badge bg-primary">{{ ucfirst($rule['rule_type']) }}</span>
                        @endif

                        @if(isset($rule['methods']) && is_array($rule['methods']))
                        <h4>Methods</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                            @foreach($rule['methods'] as $method)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid #198754;">
                                <strong>{{ $method['name'] }}()</strong>
                                @if($method['name'] === 'passes')
                                    <br><small class="text-success">✓ Main validation logic</small>
                                @elseif($method['name'] === 'message')
                                    <br><small class="text-info">💬 Error message</small>
                                @endif
                                @if(isset($method['parameters']) && !empty($method['parameters']))
                                    <br><small>Parameters:</small>
                                    @foreach($method['parameters'] as $param)
                                        <br><span class="badge bg-light text-dark">{{ $param['type'] ?? 'mixed' }} ${{ $param['name'] }}</span>
                                    @endforeach
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($rule['dependencies']) && !empty($rule['dependencies']))
                        <h4>Dependencies</h4>
                        <ul>
                            @foreach($rule['dependencies'] as $dependency)
                                <li><code>{{ $dependency }}</code></li>
                            @endforeach
                        </ul>
                        @endif

                        @if(isset($rule['error_message']))
                        <h4>Default Error Message</h4>
                        <div style="padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; color: #856404;">
                            {{ $rule['error_message'] }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p>No validation rules found.</p>
            @endif
        </div>
    </div>
</div>

        </main>
    </div>
    
    <script>
    function showPage(pageId) {
        // Hide all pages
        document.querySelectorAll('.page').forEach(page => {
            page.classList.remove('active');
        });
        
        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Show selected page
        document.getElementById(pageId).classList.add('active');
        
        // Add active class to clicked nav item
        event.target.classList.add('active');
    }
    
    function showRouteDetails(routeName) {
        showPage('routes');
        // Additional logic to highlight specific route
    }
    
    function showCommandDetails(commandName) {
        showPage('commands');
        // Additional logic to highlight specific command
    }
</script>

</body>
</html>