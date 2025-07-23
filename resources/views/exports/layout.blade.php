<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Atlas Export</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(function () {
            // Navigation functionality
            $('[data-section]').on('click', function () {
                const section = $(this).data('section');
                $('.content-section').hide();
                $('#section-' + section).show();
                $('[data-section]').removeClass('bg-indigo-600 text-white');
                $(this).addClass('bg-indigo-600 text-white');
                
                // Update URL hash without causing scroll
                if (history.pushState) {
                    history.pushState(null, null, '#' + section);
                } else {
                    window.location.hash = section;
                }
            });

            // Initialize from URL hash or show first section
            let initialSection = window.location.hash.substring(1);
            if (initialSection && $('[data-section="' + initialSection + '"]').length) {
                $('[data-section="' + initialSection + '"]').click();
            } else {
                $('[data-section]').first().click();
            }

            // Handle browser back/forward
            $(window).on('hashchange', function() {
                let section = window.location.hash.substring(1);
                if (section && $('[data-section="' + section + '"]').length) {
                    $('[data-section="' + section + '"]').click();
                }
            });

            // Add search functionality if there are multiple sections
            if ($('[data-section]').length > 1) {
                const searchHtml = `
                    <div class="mt-4 pt-4 border-t">
                        <input type="text" id="atlas-search" placeholder="Search components..." 
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                `;
                $('nav').append(searchHtml);

                $('#atlas-search').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    $('.content-section').each(function() {
                        const $section = $(this);
                        const sectionText = $section.text().toLowerCase();
                        const $navButton = $('[data-section="' + $section.attr('id').replace('section-', '') + '"]');
                        
                        if (searchTerm === '' || sectionText.includes(searchTerm)) {
                            $navButton.show();
                        } else {
                            $navButton.hide();
                        }
                    });
                });
            }
        });
    </script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">
<div class="flex">
    <nav class="w-64 bg-white shadow-md p-4 space-y-2 overflow-y-auto">
        <h1 class="text-2xl font-bold text-indigo-700 mb-4">Laravel Atlas</h1>
        
        @if (!empty($models))
            <button data-section="models" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">🧱 Models</button>
        @endif
        
        @if (!empty($routes))
            <button data-section="routes" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">🛣️ Routes</button>
        @endif
        
        @if (!empty($controllers))
            <button data-section="controllers" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">🎮 Controllers</button>
        @endif
        
        @if (!empty($services))
            <button data-section="services" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">🔧 Services</button>
        @endif
        
        @if (!empty($commands))
            <button data-section="commands" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">💬 Commands</button>
        @endif
        
        @if (!empty($jobs))
            <button data-section="jobs" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">📬 Jobs</button>
        @endif
        
        @if (!empty($events))
            <button data-section="events" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">🔔 Events</button>
        @endif
        
        @if (!empty($listeners))
            <button data-section="listeners" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">👂 Listeners</button>
        @endif
        
        @if (!empty($middleware))
            <button data-section="middleware" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">🛡️ Middleware</button>
        @endif
        
        @if (!empty($summary))
            <button data-section="summary" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100 border-t mt-4 pt-4">📊 Summary</button>
        @endif
    </nav>

    <div class="flex-1 p-6 space-y-8 overflow-y-auto">
        @if (!empty($models))
            <div id="section-models" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">🧱 Models ({{ count($models) }})</h2>
                @foreach ($models as $model)
                    @include('atlas::exports.partials.model-card', ['model' => $model])
                @endforeach
            </div>
        @endif

        @if (!empty($routes))
            <div id="section-routes" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">🛣️ Routes ({{ count($routes) }})</h2>
                @foreach ($routes as $route)
                    @include('atlas::exports.partials.route-card', ['route' => $route])
                @endforeach
            </div>
        @endif

        @if (!empty($controllers))
            <div id="section-controllers" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">🎮 Controllers ({{ count($controllers) }})</h2>
                @foreach ($controllers as $controller)
                    @include('atlas::exports.partials.controller-card', ['controller' => $controller])
                @endforeach
            </div>
        @endif

        @if (!empty($services))
            <div id="section-services" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">🔧 Services ({{ count($services) }})</h2>
                @foreach ($services as $service)
                    @include('atlas::exports.partials.service-card', ['service' => $service])
                @endforeach
            </div>
        @endif

        @if (!empty($commands))
            <div id="section-commands" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">💬 Commands ({{ count($commands) }})</h2>
                @foreach ($commands as $command)
                    @include('atlas::exports.partials.command-card', ['command' => $command])
                @endforeach
            </div>
        @endif

        @if (!empty($jobs))
            <div id="section-jobs" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">📬 Jobs ({{ count($jobs) }})</h2>
                @foreach ($jobs as $job)
                    @include('atlas::exports.partials.job-card', ['job' => $job])
                @endforeach
            </div>
        @endif

        @if (!empty($events))
            <div id="section-events" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">🔔 Events ({{ count($events) }})</h2>
                @foreach ($events as $event)
                    @include('atlas::exports.partials.event-card', ['event' => $event])
                @endforeach
            </div>
        @endif

        @if (!empty($listeners))
            <div id="section-listeners" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">👂 Listeners ({{ count($listeners) }})</h2>
                @foreach ($listeners as $listener)
                    @include('atlas::exports.partials.listener-card', ['listener' => $listener])
                @endforeach
            </div>
        @endif

        @if (!empty($middleware))
            <div id="section-middleware" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">🛡️ Middleware ({{ count($middleware) }})</h2>
                @foreach ($middleware as $middlewareItem)
                    @include('atlas::exports.partials.middleware-card', ['middleware' => $middlewareItem])
                @endforeach
            </div>
        @endif

        @if (!empty($summary))
            <div id="section-summary" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">📊 Application Summary</h2>
                @include('atlas::exports.partials.summary-card', ['summary' => $summary])
            </div>
        @endif

        @if (empty($models) && empty($routes) && empty($controllers) && empty($services) && empty($commands) && empty($jobs) && empty($events) && empty($listeners) && empty($middleware))
            <div class="text-center py-16">
                <div class="text-6xl mb-4">🗺️</div>
                <h2 class="text-2xl font-bold text-gray-600 mb-2">No Data Found</h2>
                <p class="text-gray-500">No components were analyzed or no data is available for display.</p>
            </div>
        @endif
    </div>
</div>
</body>
</html>
