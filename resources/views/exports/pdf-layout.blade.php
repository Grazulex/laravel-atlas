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

        /* Table with page break margin */
        .table-with-page-margin {
            margin-top: 20mm; /* Top margin when table breaks to new page */
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

        /* Page break with top margin for the next element */
        .page-break-with-margin {
            page-break-before: always;
            padding-top: 20mm; /* Top margin for new page */
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
            <div class="section section-break">
                <h2 class="section-title">MODELS ({{ count($models) }})</h2>
                <div class="cards-grid">
                    @foreach ($models as $index => $item)
                        <div class="@if($index > 0) page-break-with-margin @endif">
                            @include('atlas::exports.pdf.model-card')
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Controllers Section --}}
        @if (count($controllers) > 0)
            <div class="section section-break">
                <h2 class="section-title">CONTROLLERS ({{ count($controllers) }})</h2>
                <div class="cards-grid">
                    @foreach ($controllers as $index => $item)
                        <div class="@if($index > 0) page-break-with-margin @endif">
                            @include('atlas::exports.pdf.controller-card')
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Routes Section --}}
        @if (count($routes) > 0)
            <div class="section section-break">
                <h2 class="section-title">ROUTES ({{ count($routes) }})</h2>
                
                {{-- Routes Table --}}
                <table class="detail-table table-with-page-margin" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc; width: 8%;">Method</th>
                            <th style="background: #f8fafc; width: 35%;">URI</th>
                            <th style="background: #f8fafc; width: 20%;">Name</th>
                            <th style="background: #f8fafc; width: 25%;">Controller</th>
                            <th style="background: #f8fafc; width: 12%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($routes as $route)
                            <tr>
                                <td style="font-weight: bold; color: 
                                    @if(($route['method'] ?? '') === 'GET') #059669
                                    @elseif(($route['method'] ?? '') === 'POST') #dc2626
                                    @elseif(($route['method'] ?? '') === 'PUT') #f59e0b
                                    @elseif(($route['method'] ?? '') === 'PATCH') #8b5cf6
                                    @elseif(($route['method'] ?? '') === 'DELETE') #ef4444
                                    @else #6b7280 @endif
                                ;">
                                    {{ $route['method'] ?? 'GET' }}
                                </td>
                                <td style="font-family: monospace; color: #1d4ed8; font-size: 7px; word-break: break-all;">
                                    {{ $route['uri'] ?? '/' }}
                                </td>
                                <td style="font-family: monospace; color: #7c3aed; font-size: 7px;">
                                    {{ $route['name'] ?? '-' }}
                                </td>
                                <td style="font-family: monospace; color: #374151; font-size: 7px;">
                                    {{ isset($route['controller']) ? class_basename($route['controller']) : '-' }}
                                </td>
                                <td style="font-family: monospace; color: #374151; font-size: 7px;">
                                    {{ $route['action'] ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Commands Section --}}
        @if (count($commands) > 0)
            <div class="section section-break">
                <h2 class="section-title">COMMANDS ({{ count($commands) }})</h2>
                <div class="cards-grid">
                    @foreach ($commands as $index => $item)
                        <div class="@if($index > 0) page-break-with-margin @endif">
                            @include('atlas::exports.pdf.command-card')
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Services Section --}}
        @if (count($services) > 0)
            <div class="section section-break">
                <h2 class="section-title">SERVICES ({{ count($services) }})</h2>
                <div class="cards-grid">
                    @foreach ($services as $index => $item)
                        <div class="@if($index > 0) page-break-with-margin @endif">
                            @include('atlas::exports.pdf.service-card')
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Jobs Section --}}
        @if (count($jobs) > 0)
            <div class="section section-break">
                <h2 class="section-title">JOBS ({{ count($jobs) }})</h2>
                <div class="cards-grid">
                    @foreach ($jobs as $index => $item)
                        <div class="@if($index > 0) page-break-with-margin @endif">
                            @include('atlas::exports.pdf.job-card')
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Events Section --}}
        @if (count($events) > 0)
            <div class="section section-break">
                <h2 class="section-title">EVENTS ({{ count($events) }})</h2>
                
                {{-- Events Table --}}
                <table class="detail-table table-with-page-margin" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc; width: 30%;">Event Name</th>
                            <th style="background: #f8fafc; width: 25%;">Class</th>
                            <th style="background: #f8fafc; width: 25%;">Namespace</th>
                            <th style="background: #f8fafc; width: 20%;">Properties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td style="font-family: monospace; color: #dc2626; font-weight: bold; font-size: 7px;">{{ class_basename($event['class'] ?? 'Unknown') }}</td>
                                <td style="font-family: monospace; color: #1d4ed8; font-size: 7px;">{{ $event['class'] ?? 'Unknown' }}</td>
                                <td style="font-family: monospace; color: #7c3aed; font-size: 6px;">{{ $event['namespace'] ?? '-' }}</td>
                                <td style="font-size: 6px;">
                                    {{ !empty($event['properties']) ? count($event['properties']) . ' props' : '0 props' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Middlewares Section --}}
        @if (count($middlewares) > 0)
            <div class="section section-break">
                <h2 class="section-title">MIDDLEWARES ({{ count($middlewares) }})</h2>
                
                {{-- Middlewares Table --}}
                <table class="detail-table table-with-page-margin" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc; width: 25%;">Middleware Name</th>
                            <th style="background: #f8fafc; width: 35%;">Class</th>
                            <th style="background: #f8fafc; width: 20%;">Type</th>
                            <th style="background: #f8fafc; width: 20%;">Priority</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($middlewares as $middleware)
                            <tr>
                                <td style="font-family: monospace; color: #f59e0b; font-weight: bold; font-size: 7px;">{{ class_basename($middleware['class'] ?? 'Unknown') }}</td>
                                <td style="font-family: monospace; color: #1d4ed8; font-size: 7px;">{{ $middleware['class'] ?? 'Unknown' }}</td>
                                <td style="font-size: 7px; font-weight: bold;">{{ $middleware['type'] ?? 'Global' }}</td>
                                <td style="font-size: 7px;">{{ $middleware['priority'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Form Requests Section --}}
        @if (count($form_requests) > 0)
            <div class="section section-break">
                <h2 class="section-title">FORM REQUESTS ({{ count($form_requests) }})</h2>
                
                {{-- Form Requests Table --}}
                <table class="detail-table table-with-page-margin" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc; width: 25%;">Request Name</th>
                            <th style="background: #f8fafc; width: 35%;">Class</th>
                            <th style="background: #f8fafc; width: 20%;">Rules</th>
                            <th style="background: #f8fafc; width: 20%;">Authorized</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($form_requests as $request)
                            <tr>
                                <td style="font-family: monospace; color: #8b5cf6; font-weight: bold; font-size: 7px;">{{ class_basename($request['class'] ?? 'Unknown') }}</td>
                                <td style="font-family: monospace; color: #1d4ed8; font-size: 7px;">{{ $request['class'] ?? 'Unknown' }}</td>
                                <td style="font-size: 7px;">
                                    {{ !empty($request['rules']) ? count($request['rules']) . ' rules' : '0 rules' }}
                                </td>
                                <td style="font-size: 7px; font-weight: bold;">{{ !empty($request['authorize']) ? 'Yes' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Notifications Section --}}
        @if (count($notifications) > 0)
            <div class="section section-break">
                <h2 class="section-title">NOTIFICATIONS ({{ count($notifications) }})</h2>
                
                {{-- Notifications Table --}}
                <table class="detail-table table-with-page-margin" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc; width: 25%;">Notification Name</th>
                            <th style="background: #f8fafc; width: 30%;">Class</th>
                            <th style="background: #f8fafc; width: 25%;">Channels</th>
                            <th style="background: #f8fafc; width: 20%;">Queue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notification)
                            <tr>
                                <td style="font-family: monospace; color: #ef4444; font-weight: bold; font-size: 7px;">{{ class_basename($notification['class'] ?? 'Unknown') }}</td>
                                <td style="font-family: monospace; color: #1d4ed8; font-size: 7px;">{{ $notification['class'] ?? 'Unknown' }}</td>
                                <td style="font-size: 6px;">
                                    {{ !empty($notification['channels']) ? implode(', ', array_slice($notification['channels'], 0, 3)) : '-' }}
                                    @if (!empty($notification['channels']) && count($notification['channels']) > 3)
                                        ...
                                    @endif
                                </td>
                                <td style="font-size: 7px; font-weight: bold;">{{ !empty($notification['queued']) ? 'Yes' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Resources Section --}}
        @if (count($resources) > 0)
            <div class="section section-break">
                <h2 class="section-title">RESOURCES ({{ count($resources) }})</h2>
                
                {{-- Resources Table --}}
                <table class="detail-table table-with-page-margin" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc; width: 25%;">Resource Name</th>
                            <th style="background: #f8fafc; width: 35%;">Class</th>
                            <th style="background: #f8fafc; width: 20%;">Type</th>
                            <th style="background: #f8fafc; width: 20%;">Collection</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($resources as $resource)
                            <tr>
                                <td style="font-family: monospace; color: #06b6d4; font-weight: bold; font-size: 7px;">{{ class_basename($resource['class'] ?? 'Unknown') }}</td>
                                <td style="font-family: monospace; color: #1d4ed8; font-size: 7px;">{{ $resource['class'] ?? 'Unknown' }}</td>
                                <td style="font-size: 7px; font-weight: bold;">{{ $resource['type'] ?? 'Resource' }}</td>
                                <td style="font-size: 7px; font-weight: bold;">{{ !empty($resource['is_collection']) ? 'Yes' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Actions Section --}}
        @if (count($actions) > 0)
            <div class="section section-break">
                <h2 class="section-title">ACTIONS ({{ count($actions) }})</h2>
                <div class="cards-grid">
                    @foreach ($actions as $index => $item)
                        <div class="@if($index > 0) page-break-with-margin @endif">
                            @include('atlas::exports.pdf.action-card')
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Policies Section --}}
        @if (count($policies) > 0)
            <div class="section section-break">
                <h2 class="section-title">POLICIES ({{ count($policies) }})</h2>
                <div class="cards-grid">
                    @foreach ($policies as $index => $item)
                        <div class="@if($index > 0) page-break-with-margin @endif">
                            @include('atlas::exports.pdf.policy-card')
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Rules Section --}}
        @if (count($rules) > 0)
            <div class="section section-break">
                <h2 class="section-title">RULES ({{ count($rules) }})</h2>
                
                {{-- Rules Table --}}
                <table class="detail-table table-with-page-margin" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc; width: 25%;">Rule Name</th>
                            <th style="background: #f8fafc; width: 40%;">Class</th>
                            <th style="background: #f8fafc; width: 35%;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rules as $rule)
                            <tr>
                                <td style="font-family: monospace; color: #84cc16; font-weight: bold; font-size: 7px;">{{ class_basename($rule['class'] ?? 'Unknown') }}</td>
                                <td style="font-family: monospace; color: #1d4ed8; font-size: 7px;">{{ $rule['class'] ?? 'Unknown' }}</td>
                                <td style="font-size: 7px;">{{ $rule['description'] ?? 'Custom validation rule' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Listeners Section --}}
        @if (count($listeners) > 0)
            <div class="section section-break">
                <h2 class="section-title">LISTENERS ({{ count($listeners) }})</h2>
                
                {{-- Listeners Table --}}
                <table class="detail-table table-with-page-margin" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th style="background: #f8fafc; width: 25%;">Listener Name</th>
                            <th style="background: #f8fafc; width: 30%;">Event</th>
                            <th style="background: #f8fafc; width: 25%;">Class</th>
                            <th style="background: #f8fafc; width: 20%;">Queue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listeners as $listener)
                            <tr>
                                <td style="font-family: monospace; color: #059669; font-weight: bold; font-size: 7px;">{{ class_basename($listener['class'] ?? 'Unknown') }}</td>
                                <td style="font-family: monospace; color: #dc2626; font-size: 7px;">{{ $listener['event'] ?? '-' }}</td>
                                <td style="font-family: monospace; color: #1d4ed8; font-size: 6px;">{{ $listener['class'] ?? 'Unknown' }}</td>
                                <td style="font-size: 7px; font-weight: bold;">{{ !empty($listener['queued']) ? 'Yes' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Observers Section --}}
        @if (count($observers) > 0)
            <div class="section section-break">
                <h2 class="section-title">OBSERVERS ({{ count($observers) }})</h2>
                <div class="cards-grid">
                    @foreach ($observers as $index => $item)
                        <div class="@if($index > 0) page-break-with-margin @endif">
                            @include('atlas::exports.pdf.observer-card')
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</body>
</html>
