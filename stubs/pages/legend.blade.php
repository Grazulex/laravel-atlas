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
