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
        <a href="#policies" class="nav-item" onclick="showPage('policies')">
            Policies <span class="nav-badge">{{ count($data['policies'] ?? []) }}</span>
        </a>
    </div>
</nav>
