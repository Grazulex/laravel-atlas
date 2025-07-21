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
        <a href="#policies" class="nav-item" onclick="showPage('policies')">
            Policies <span class="nav-badge">{{ count($data['policies'] ?? []) }}</span>
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
                    <div>📖 Documentation</div>
                </div>
            </div>
            
        </div>
    </div>
</div>


<!-- Routes Page -->
<div id="routes" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🛣️ Application Routes</h2>
        </div>
        <div class="card-body">
            @if(isset($data['routes']))
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
                                @if(is_array($route['action'] ?? 'handle'))
                                    @if(isset($route['action']['uses']) && strpos($route['action']['uses'], '@') !== false)
                                        {{ explode('@', $route['action']['uses'])[1] ?? 'handle' }}
                                    @elseif(isset($route['controller']['method']))
                                        {{ $route['controller']['method'] }}
                                    @else
                                        [Complex Action]
                                    @endif
                                @else
                                    {{ $route['action'] ?? 'handle' }}
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
            @else
                <p>No routes found.</p>
            @endif
        </div>
    </div>
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
                        <h3>{{ $command['name'] ?: class_basename($command['class_name'] ?? 'Unknown Command') }}</h3>
                        @if($command['signature'])
                            <code>{{ $command['signature'] }}</code>
                        @else
                            <small>{{ $command['class_name'] ?? '' }}</small>
                        @endif
                    </div>
                    <div class="card-body">
                        <p>{{ $command['description'] ?? 'No description available' }}</p>
                        
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
                        <small>{{ $model['class_name'] }} → {{ $model['table'] }}</small>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <h4>Attributes</h4>
                                @if(isset($model['attributes']) && is_array($model['attributes']))
                                    <ul>
                                        @foreach($model['attributes'] as $attribute)
                                            @if(is_array($attribute))
                                                <li>{{ implode(', ', $attribute) }}</li>
                                            @else
                                                <li>{{ $attribute }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <p><em>No attributes found</em></p>
                                @endif
                            </div>
                            <div>
                                <h4>Relationships</h4>
                                @if(isset($model['relationships']))
                                    <ul>
                                        @foreach($model['relationships'] as $relationship)
                                            @if(is_array($relationship))
                                                <li>{{ implode(' → ', $relationship) }}</li>
                                            @else
                                                <li>{{ $relationship }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
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
                            <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; border-left: 3px solid {{ $method['lifecycle_event'] == 'before' ? '#ffc107' : '#28a745' }};">
                                <strong>{{ $method['name'] }}()</strong>
                                <br><small>
                                    <span class="badge badge-{{ $method['lifecycle_event'] == 'before' ? 'warning' : 'success' }}">
                                        {{ ucfirst($method['lifecycle_event']) }}
                                    </span>
                                    {{ $method['lifecycle_event'] == 'before' ? 'Pre-action hook' : 'Post-action hook' }}
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


<!-- Services Page -->
<div id="services" class="page">
    <div class="card">
        <div class="card-header">
            <h2>🔧 Business Services</h2>
        </div>
        <div class="card-body">
            @if(isset($data['services']))
                @foreach($data['services'] as $service)
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3>{{ class_basename($service['class_name']) }}</h3>
                        <small>{{ $service['class_name'] }}</small>
                    </div>
                    <div class="card-body">
                        @if(isset($service['methods']))
                        <h4>Methods</h4>
                        @if(is_array($service['methods']) && count($service['methods']) > 0)
                            @if(is_numeric(array_keys($service['methods'])[0]))
                                <p><em>{{ count($service['methods']) }} methods found</em></p>
                            @else
                                @foreach($service['methods'] as $method => $details)
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                                    <strong>{{ is_array($method) ? implode(', ', $method) : $method }}()</strong>
                                    @if(isset($details['dependencies']))
                                    <br><small><strong>Dependencies:</strong> {{ implode(', ', array_map('class_basename', $details['dependencies'])) }}</small>
                                    @endif
                                    @if(isset($details['returns']))
                                    <br><small><strong>Returns:</strong> {{ is_array($details['returns']) ? implode(', ', $details['returns']) : $details['returns'] }}</small>
                                    @endif
                                </div>
                                @endforeach
                            @endif
                        @else
                            <p><em>No method details available</em></p>
                        @endif
                        @endif
                        
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
                        <p><strong>Triggered by:</strong></p>
                        <ul>
                            @foreach($job['triggered_by'] as $trigger)
                            <li>{{ class_basename($trigger) }}</li>
                            @endforeach
                        </ul>
                        @endif
                        
                        @if(isset($job['dependencies']))
                        <p><strong>Dependencies:</strong></p>
                        <ul>
                            @foreach($job['dependencies'] as $dep)
                            <li>{{ class_basename($dep) }}</li>
                            @endforeach
                        </ul>
                        @endif
                        
                        @if(isset($job['events']))
                        <p><strong>Events fired:</strong></p>
                        <ul>
                            @foreach($job['events'] as $event)
                            <li>{{ class_basename($event) }}</li>
                            @endforeach
                        </ul>
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
                        
                        @if(isset($event['triggered_by']))
                        <p><strong>Triggered by:</strong></p>
                        <ul>
                            @foreach($event['triggered_by'] as $trigger)
                            <li>{{ is_array($trigger) ? implode(', ', $trigger) : $trigger }}</li>
                            @endforeach
                        </ul>
                        @endif
                        
                        @if(isset($event['listeners']))
                        <p><strong>Listeners:</strong></p>
                        <ul>
                            @foreach($event['listeners'] as $listener)
                            <li>{{ class_basename($listener) }}</li>
                            @endforeach
                        </ul>
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