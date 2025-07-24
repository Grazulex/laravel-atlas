<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <title>Laravel Atlas – {{ count($models) }} Models, {{ count($commands) }} Commands, {{ count($routes) }} Routes, {{ count($services) }} Services, {{ count($notifications) }} Notifications, {{ count($middlewares) }} Middlewares, {{ count($form_requests) }} Form Requests, {{ count($events) }} Events, {{ count($controllers) }} Controllers, {{ count($resources) }} Resources, {{ count($jobs) }} Jobs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        };

        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('dark', document.documentElement.classList.contains('dark') ? '1' : '0');
        }

        if (localStorage.getItem('dark') === '1') {
            document.documentElement.classList.add('dark');
        }

        $(function () {
            $('[data-section]').on('click', function () {
                const section = $(this).data('section');
                $('.content-section').hide();
                $('#section-' + section).show();

                // Remove active state from all nav items
                $('.nav-item')
                    .removeClass('bg-indigo-600 dark:bg-indigo-600 text-white border-l-4 border-indigo-800')
                    .addClass('text-gray-900 dark:text-gray-100');

                // Add active state to clicked item
                $(this)
                    .addClass('bg-indigo-600 dark:bg-indigo-600 text-white border-l-4 border-indigo-800')
                    .removeClass('text-gray-900 dark:text-gray-100 hover:bg-indigo-50 dark:hover:bg-indigo-900/20');

                // Update badge colors for active item
                $(this).find('.inline-flex')
                    .removeClass('bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200')
                    .addClass('bg-indigo-500 text-white');

                // Reset badge colors for inactive items
                $('.nav-item').not(this).find('.inline-flex')
                    .removeClass('bg-indigo-500 text-white')
                    .addClass('bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200');
            });

            $('[data-section]').first().click();

            $('#menu-toggle').on('click', function () {
                $('#sidebar').toggleClass('hidden');
            });
        });
    </script>
    <style>
        code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            background-color: #f1f5f9;
            padding: 0.15rem 0.3rem;
            border-radius: 0.25rem;
        }
        .dark code {
            background-color: #1e293b;
            color: #f8fafc;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen">
{{-- Mobile Menu Toggle --}}
<div class="md:hidden bg-white dark:bg-gray-800 p-4 shadow border-b border-gray-200 dark:border-gray-700">
    <button id="menu-toggle" class="flex items-center space-x-2 text-indigo-700 dark:text-indigo-300 hover:text-indigo-900 dark:hover:text-indigo-100 transition-colors">
        <span class="text-lg">📂</span>
        <span class="font-medium">Menu</span>
    </button>
</div>

<div class="flex">
    {{-- Sidebar --}}
    <div id="sidebar" class="md:block hidden fixed md:relative inset-y-0 left-0 z-50 md:z-auto">
        @include('atlas::exports.partials.common.navigation')
    </div>

    {{-- Main Content --}}
    <div class="flex-1 md:ml-0">
        <div class="p-6 max-w-7xl mx-auto">
        <div id="section-models" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🧱</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Models</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($models) }} {{ count($models) === 1 ? 'Model' : 'Models' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Database entities and their relationships, attributes, and behaviors.</p>
            </div>
            @foreach ($models as $model)
                @include('atlas::exports.partials.model-card', ['model' => $model])
            @endforeach
        </div>

        <div id="section-commands" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">💬</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Commands</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($commands) }} {{ count($commands) === 1 ? 'Command' : 'Commands' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Artisan commands for CLI operations and automation tasks.</p>
            </div>
            @foreach ($commands as $command)
                @include('atlas::exports.partials.command-card', ['command' => $command])
            @endforeach
        </div>

        <div id="section-routes" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🛣️</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Routes</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($routes) }} {{ count($routes) === 1 ? 'Route' : 'Routes' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Application endpoints and URL patterns for handling HTTP requests.</p>
            </div>
            @foreach ($routes as $route)
                @include('atlas::exports.partials.route-card', ['route' => $route])
            @endforeach
        </div>

        <div id="section-services" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🔧</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Services</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($services) }} {{ count($services) === 1 ? 'Service' : 'Services' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Business logic components and application services.</p>
            </div>
            @foreach ($services as $service)
                @include('atlas::exports.partials.service-card', ['service' => $service])
            @endforeach
        </div>

        <div id="section-notifications" class="content-section hidden">
            <h2 class="text-xl font-bold">📢 Notifications</h2>
            @foreach ($notifications as $notification)
                @include('atlas::exports.partials.notification-card', ['notification' => $notification])
            @endforeach
        </div>

        <div id="section-middlewares" class="content-section hidden">
            <h2 class="text-xl font-bold">🛡️ Middlewares</h2>
            @foreach ($middlewares as $middleware)
                @include('atlas::exports.partials.middleware-card', ['middleware' => $middleware])
            @endforeach
        </div>

        <div id="section-form_requests" class="content-section hidden">
            <h2 class="text-xl font-bold">📋 Form Requests</h2>
            @foreach ($form_requests as $formRequest)
                @include('atlas::exports.partials.form-request-card', ['formRequest' => $formRequest])
            @endforeach
        </div>

        <div id="section-events" class="content-section hidden">
            <h2 class="text-xl font-bold">⚡ Events</h2>
            @foreach ($events as $event)
                @include('atlas::exports.partials.event-card', ['event' => $event])
            @endforeach
        </div>

        <div id="section-controllers" class="content-section hidden">
            <h2 class="text-xl font-bold">🎮 Controllers</h2>
            @foreach ($controllers as $controller)
                @include('atlas::exports.partials.controller-card', ['controller' => $controller])
            @endforeach
        </div>

        <div id="section-resources" class="content-section hidden">
            <h2 class="text-xl font-bold">🔗 API Resources</h2>
            @foreach ($resources as $resource)
                @include('atlas::exports.partials.resource-card', ['resource' => $resource])
            @endforeach
        </div>

        <div id="section-jobs" class="content-section hidden">
            <h2 class="text-xl font-bold">⚡ Jobs</h2>
            @foreach ($jobs as $job)
                @include('atlas::exports.partials.job-card', ['job' => $job])
            @endforeach
        </div>

        <div id="section-notifications" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">📢</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($notifications) }} {{ count($notifications) === 1 ? 'Notification' : 'Notifications' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">User notifications and alert systems for various channels.</p>
            </div>
            @foreach ($notifications as $notification)
                @include('atlas::exports.partials.notification-card', ['notification' => $notification])
            @endforeach
        </div>

        <div id="section-middlewares" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🛡️</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Middlewares</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($middlewares) }} {{ count($middlewares) === 1 ? 'Middleware' : 'Middlewares' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Request filters and HTTP middleware for authentication, authorization, and processing.</p>
            </div>
            @foreach ($middlewares as $middleware)
                @include('atlas::exports.partials.middleware-card', ['middleware' => $middleware])
            @endforeach
        </div>

        <div id="section-form_requests" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">📋</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Form Requests</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($form_requests) }} {{ count($form_requests) === 1 ? 'Form Request' : 'Form Requests' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Input validation classes for handling form data and request validation.</p>
            </div>
            @foreach ($form_requests as $form_request)
                @include('atlas::exports.partials.form-request-card', ['form_request' => $form_request])
            @endforeach
        </div>

        <div id="section-events" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">⚡</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Events</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($events) }} {{ count($events) === 1 ? 'Event' : 'Events' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">System events for decoupled communication and event-driven architecture.</p>
            </div>
            @foreach ($events as $event)
                @include('atlas::exports.partials.event-card', ['event' => $event])
            @endforeach
        </div>

        <div id="section-controllers" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🎮</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Controllers</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($controllers) }} {{ count($controllers) === 1 ? 'Controller' : 'Controllers' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">HTTP request handlers and application logic controllers.</p>
            </div>
            @foreach ($controllers as $controller)
                @include('atlas::exports.partials.controller-card', ['controller' => $controller])
            @endforeach
        </div>

        <div id="section-resources" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🔗</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">API Resources</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($resources) }} {{ count($resources) === 1 ? 'Resource' : 'Resources' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">API data transformers for consistent JSON responses and resource formatting.</p>
            </div>
            @foreach ($resources as $resource)
                @include('atlas::exports.partials.resource-card', ['resource' => $resource])
            @endforeach
        </div>

        <div id="section-jobs" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">⚙️</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Jobs</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($jobs) }} {{ count($jobs) === 1 ? 'Job' : 'Jobs' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Background tasks and queued jobs for asynchronous processing.</p>
            </div>
            @foreach ($jobs as $job)
                @include('atlas::exports.partials.job-card', ['job' => $job])
            @endforeach
        </div>

        <div id="section-actions" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">⚡</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Actions</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($actions ?? []) }} {{ count($actions ?? []) === 1 ? 'Action' : 'Actions' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Single responsibility actions for specific business operations.</p>
            </div>
            @foreach ($actions ?? [] as $action)
                @include('atlas::exports.partials.action-card', ['action' => $action])
            @endforeach
        </div>

        <div id="section-policies" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🛡️</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Policies</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($policies ?? []) }} {{ count($policies ?? []) === 1 ? 'Policy' : 'Policies' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Authorization logic and access control policies for resource protection.</p>
            </div>
            @foreach ($policies ?? [] as $policy)
                @include('atlas::exports.partials.policy-card', ['policy' => $policy])
            @endforeach
        </div>

        <div id="section-rules" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">📏</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Rules</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($rules ?? []) }} {{ count($rules ?? []) === 1 ? 'Rule' : 'Rules' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Custom validation rules for complex input validation scenarios.</p>
            </div>
            @foreach ($rules ?? [] as $rule)
                @include('atlas::exports.partials.rule-card', ['rule' => $rule])
            @endforeach
        </div>

        <div id="section-listeners" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">👂</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Listeners</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($listeners ?? []) }} {{ count($listeners ?? []) === 1 ? 'Listener' : 'Listeners' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Event handlers for responding to system events and notifications.</p>
            </div>
            @foreach ($listeners ?? [] as $listener)
                @include('atlas::exports.partials.listener-card', ['listener' => $listener])
            @endforeach
        </div>

        <div id="section-observers" class="content-section hidden">
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">👁️</span>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Observers</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ count($observers ?? []) }} {{ count($observers ?? []) === 1 ? 'Observer' : 'Observers' }}
                    </span>
                </div>
                <p class="text-gray-600 dark:text-gray-400">Model observers for monitoring and responding to Eloquent model events.</p>
            </div>
            @foreach ($observers ?? [] as $observer)
                @include('atlas::exports.partials.observer-card', ['observer' => $observer])
            @endforeach
        </div>
        </div>
    </div>
</div>
</body>
</html>
