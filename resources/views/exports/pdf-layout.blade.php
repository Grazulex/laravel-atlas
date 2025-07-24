<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project_name }} – Atlas – {{ count($models) }} Models, {{ count($commands) }} Commands, {{ count($routes) }} Routes, {{ count($services) }} Services, {{ count($notifications) }} Notifications, {{ count($middlewares) }} Middlewares, {{ count($form_requests) }} Form Requests, {{ count($events) }} Events, {{ count($controllers) }} Controllers, {{ count($resources) }} Resources, {{ count($jobs) }} Jobs</title>
    
    <style>
        /* PDF-optimized styles - no dark mode, no interactive elements */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        /* Page layout */
        .container {
            width: 100%;
            max-width: 190mm; /* A4 width minus margins */
            margin: 0 auto;
            padding: 8mm 10mm; /* Smaller top/bottom padding, consistent sides */
        }

        @page {
            margin: 10mm; /* Standard margins all around */
            size: A4;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4f46e5;
        }

        .header h1 {
            font-size: 24px;
            color: #4f46e5;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        /* Statistics summary */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 8px;
            margin-bottom: 30px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
        }

        .stat-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Sections */
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 12px;
            padding: 6px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Cards for PDF */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            background: white;
            page-break-inside: avoid;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .card-header {
            background: #f8fafc;
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-title {
            font-size: 13px;
            font-weight: bold;
            color: #1f2937;
            line-height: 1.3;
        }

        .card-subtitle {
            font-size: 10px;
            color: #6b7280;
            margin-top: 2px;
            line-height: 1.2;
        }

        .card-content {
            padding: 10px;
        }

        /* Properties */
        .property-item {
            margin-bottom: 6px;
            font-size: 10px;
            line-height: 1.3;
        }

        .property-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 1px;
        }

        .property-value {
            color: #6b7280;
            font-family: monospace;
            word-break: break-word;
            font-size: 9px;
        }

        /* Tables */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 9px;
        }

        .detail-table th,
        .detail-table td {
            padding: 4px 6px;
            border: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }

        .detail-table th {
            background: #f8fafc;
            font-weight: bold;
            color: #374151;
        }

        .detail-table td {
            color: #6b7280;
        }

        /* Footer */
        .card-footer {
            padding: 6px 10px;
            border-top: 1px solid #e5e7eb;
            background: #f8fafc;
            font-size: 9px;
            color: #6b7280;
        }

        .footer-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        /* Page breaks */
        .page-break {
            page-break-before: always;
        }

        .section-break {
            page-break-before: always;
        }

        /* No page break */
        .no-break {
            page-break-inside: avoid;
        }

        /* Hide elements not suitable for PDF */
        .pdf-hidden {
            display: none;
        }

        /* Utilities */
        .text-xs { font-size: 10px; }
        .text-sm { font-size: 11px; }
        .text-base { font-size: 12px; }
        .text-lg { font-size: 14px; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: monospace; }
        .truncate { 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ $project_name }}</h1>
            <p>Atlas - {{ $created_at }}</p>
        </div>

        {{-- Statistics Summary --}}
        <div class="stats-summary">
            <div class="stat-item">
                <div class="stat-number">{{ count($models) }}</div>
                <div class="stat-label">Models</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($controllers) }}</div>
                <div class="stat-label">Controllers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($routes) }}</div>
                <div class="stat-label">Routes</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($commands) }}</div>
                <div class="stat-label">Commands</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($services) }}</div>
                <div class="stat-label">Services</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($jobs) }}</div>
                <div class="stat-label">Jobs</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($events) }}</div>
                <div class="stat-label">Events</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($listeners) }}</div>
                <div class="stat-label">Listeners</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($notifications) }}</div>
                <div class="stat-label">Notifications</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($middlewares) }}</div>
                <div class="stat-label">Middlewares</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($form_requests) }}</div>
                <div class="stat-label">Form Requests</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($resources) }}</div>
                <div class="stat-label">Resources</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($policies) }}</div>
                <div class="stat-label">Policies</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($rules) }}</div>
                <div class="stat-label">Rules</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($observers) }}</div>
                <div class="stat-label">Observers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ count($actions) }}</div>
                <div class="stat-label">Actions</div>
            </div>
        </div>

        {{-- Models Section --}}
        @if (count($models) > 0)
            <div class="section">
                <h2 class="section-title">MODELS ({{ count($models) }})</h2>
                <div class="cards-grid">
                    @foreach ($models as $item)
                        @include('atlas::exports.pdf.model-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Controllers Section --}}
        @if (count($controllers) > 0)
            <div class="section section-break">
                <h2 class="section-title">CONTROLLERS ({{ count($controllers) }})</h2>
                <div class="cards-grid">
                    @foreach ($controllers as $item)
                        @include('atlas::exports.pdf.controller-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Routes Section --}}
        @if (count($routes) > 0)
            <div class="section section-break">
                <h2 class="section-title">ROUTES ({{ count($routes) }})</h2>
                <div class="cards-grid">
                    @foreach ($routes as $item)
                        @include('atlas::exports.pdf.route-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Commands Section --}}
        @if (count($commands) > 0)
            <div class="section section-break">
                <h2 class="section-title">COMMANDS ({{ count($commands) }})</h2>
                <div class="cards-grid">
                    @foreach ($commands as $item)
                        @include('atlas::exports.pdf.command-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Services Section --}}
        @if (count($services) > 0)
            <div class="section section-break">
                <h2 class="section-title">SERVICES ({{ count($services) }})</h2>
                <div class="cards-grid">
                    @foreach ($services as $item)
                        @include('atlas::exports.pdf.service-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Jobs Section --}}
        @if (count($jobs) > 0)
            <div class="section section-break">
                <h2 class="section-title">JOBS ({{ count($jobs) }})</h2>
                <div class="cards-grid">
                    @foreach ($jobs as $item)
                        @include('atlas::exports.pdf.job-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Events Section --}}
        @if (count($events) > 0)
            <div class="section section-break">
                <h2 class="section-title">EVENTS ({{ count($events) }})</h2>
                <div class="cards-grid">
                    @foreach ($events as $item)
                        @include('atlas::exports.pdf.event-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Middlewares Section --}}
        @if (count($middlewares) > 0)
            <div class="section section-break">
                <h2 class="section-title">MIDDLEWARES ({{ count($middlewares) }})</h2>
                <div class="cards-grid">
                    @foreach ($middlewares as $item)
                        @include('atlas::exports.pdf.middleware-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Form Requests Section --}}
        @if (count($form_requests) > 0)
            <div class="section section-break">
                <h2 class="section-title">FORM REQUESTS ({{ count($form_requests) }})</h2>
                <div class="cards-grid">
                    @foreach ($form_requests as $item)
                        @include('atlas::exports.pdf.request-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Notifications Section --}}
        @if (count($notifications) > 0)
            <div class="section section-break">
                <h2 class="section-title">NOTIFICATIONS ({{ count($notifications) }})</h2>
                <div class="cards-grid">
                    @foreach ($notifications as $item)
                        @include('atlas::exports.pdf.notification-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Resources Section --}}
        @if (count($resources) > 0)
            <div class="section section-break">
                <h2 class="section-title">RESOURCES ({{ count($resources) }})</h2>
                <div class="cards-grid">
                    @foreach ($resources as $item)
                        @include('atlas::exports.pdf.resource-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Actions Section --}}
        @if (count($actions) > 0)
            <div class="section section-break">
                <h2 class="section-title">ACTIONS ({{ count($actions) }})</h2>
                <div class="cards-grid">
                    @foreach ($actions as $item)
                        @include('atlas::exports.pdf.action-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Policies Section --}}
        @if (count($policies) > 0)
            <div class="section section-break">
                <h2 class="section-title">POLICIES ({{ count($policies) }})</h2>
                <div class="cards-grid">
                    @foreach ($policies as $item)
                        @include('atlas::exports.pdf.policy-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Rules Section --}}
        @if (count($rules) > 0)
            <div class="section section-break">
                <h2 class="section-title">RULES ({{ count($rules) }})</h2>
                <div class="cards-grid">
                    @foreach ($rules as $item)
                        @include('atlas::exports.pdf.rule-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Listeners Section --}}
        @if (count($listeners) > 0)
            <div class="section section-break">
                <h2 class="section-title">LISTENERS ({{ count($listeners) }})</h2>
                <div class="cards-grid">
                    @foreach ($listeners as $item)
                        @include('atlas::exports.pdf.listener-card')
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Observers Section --}}
        @if (count($observers) > 0)
            <div class="section section-break">
                <h2 class="section-title">OBSERVERS ({{ count($observers) }})</h2>
                <div class="cards-grid">
                    @foreach ($observers as $item)
                        @include('atlas::exports.pdf.observer-card')
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</body>
</html>
