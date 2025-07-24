<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Atlas – {{ count($models) }} Models, {{ count($commands) }} Commands, {{ count($routes) }} Routes, {{ count($services) }} Services, {{ count($notifications) }} Notifications, {{ count($middlewares) }} Middlewares, {{ count($form_requests) }} Form Requests, {{ count($events) }} Events, {{ count($controllers) }} Controllers, {{ count($resources) }} Resources, {{ count($jobs) }} Jobs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        mono: ['JetBrains Mono', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', 'monospace'],
                    }
                }
            }
        };

        // Dark mode management
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('atlas-dark-mode', isDark ? '1' : '0');
            updateDarkModeButton(isDark);
        }

        function updateDarkModeButton(isDark) {
            const button = document.getElementById('dark-mode-toggle');
            const icon = document.getElementById('dark-mode-icon');
            if (button && icon) {
                icon.textContent = isDark ? '☀️' : '🌙';
                button.title = isDark ? 'Switch to light mode' : 'Switch to dark mode';
            }
        }

        // Initialize dark mode
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = localStorage.getItem('atlas-dark-mode') === '1';
            if (isDark) {
                document.documentElement.classList.add('dark');
            }
            updateDarkModeButton(isDark);
        });

        // Section navigation
        $(function () {
            // Hide all sections initially
            $('.content-section').removeClass('show');
            
            // Section navigation handler
            $('[data-section]').on('click', function () {
                const section = $(this).data('section');
                
                // Hide all sections
                $('.content-section').removeClass('show');
                
                // Show selected section
                $('#section-' + section).addClass('show');

                // Update navigation states
                updateNavigation($(this));
                
                // Update URL hash without scrolling
                if (history.replaceState) {
                    history.replaceState(null, null, '#' + section);
                }
            });

            // Mobile menu toggle
            $('#mobile-menu-toggle').on('click', function () {
                $('#sidebar').toggleClass('-translate-x-full');
            });

            // Auto-activate section from URL hash or first section
            setTimeout(function() {
                const hash = window.location.hash.substring(1);
                const targetSection = hash ? $('[data-section="' + hash + '"]') : $('[data-section]').first();
                if (targetSection.length) {
                    targetSection.click();
                } else {
                    $('[data-section]').first().click();
                }
            }, 100);
        });

        function updateNavigation(activeItem) {
            // Reset all navigation items
            $('.nav-item')
                .removeClass('bg-indigo-600 dark:bg-indigo-600 text-white border-l-4 border-indigo-800 shadow-lg')
                .addClass('text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20');

            // Activate selected item
            activeItem
                .addClass('bg-indigo-600 dark:bg-indigo-600 text-white border-l-4 border-indigo-800 shadow-lg')
                .removeClass('text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20');

            // Update badges
            $('.nav-item .count-badge')
                .removeClass('bg-indigo-500 text-white')
                .addClass('bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400');

            activeItem.find('.count-badge')
                .removeClass('bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400')
                .addClass('bg-indigo-500 text-white');
        }
    </script>
    <style>
        /* Code styling */
        code {
            font-family: 'JetBrains Mono', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', monospace;
            font-size: 0.875rem;
            background-color: #f1f5f9;
            color: #334155;
            padding: 0.125rem 0.375rem;
            border-radius: 0.375rem;
            font-weight: 500;
        }
        .dark code {
            background-color: #1e293b;
            color: #e2e8f0;
        }
        
        /* Content sections visibility */
        .content-section {
            display: none;
        }
        .content-section.show {
            display: block;
        }
        
        /* Scrollbar styling */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.8);
        }
        
        /* Fade transitions */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Card layout with footer at bottom */
        .card-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .card-content {
            flex: 1;
        }
        
        .card-footer {
            margin-top: auto;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans">
    {{-- Mobile Header --}}
    <div class="lg:hidden bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-3">
                <button id="mobile-menu-toggle" class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Laravel Atlas</h1>
            </div>
            <button id="dark-mode-toggle" onclick="toggleDarkMode()" 
                    class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    title="Toggle dark mode">
                <span id="dark-mode-icon" class="text-lg">🌙</span>
            </button>
        </div>
    </div>

    <div class="flex h-screen lg:h-auto lg:min-h-screen">
        {{-- Sidebar --}}
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-80 bg-white dark:bg-gray-800 shadow-xl lg:shadow-lg border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-lg">A</span>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Laravel Atlas</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Code Architecture</p>
                        </div>
                    </div>
                    <button id="dark-mode-toggle" onclick="toggleDarkMode()" 
                            class="hidden lg:flex p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            title="Toggle dark mode">
                        <span id="dark-mode-icon" class="text-lg">🌙</span>
                    </button>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto custom-scrollbar">
                    @if (count($models) > 0)
                        <a href="#models" data-section="models" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">🧱</span>
                                <span class="font-medium">Models</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($models) }}</span>
                        </a>
                    @endif

                    @if (count($controllers) > 0)
                        <a href="#controllers" data-section="controllers" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">🎮</span>
                                <span class="font-medium">Controllers</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($controllers) }}</span>
                        </a>
                    @endif

                    @if (count($routes) > 0)
                        <a href="#routes" data-section="routes" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">🛣️</span>
                                <span class="font-medium">Routes</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($routes) }}</span>
                        </a>
                    @endif

                    @if (count($commands) > 0)
                        <a href="#commands" data-section="commands" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">⚡</span>
                                <span class="font-medium">Commands</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($commands) }}</span>
                        </a>
                    @endif

                    @if (count($services) > 0)
                        <a href="#services" data-section="services" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">🔧</span>
                                <span class="font-medium">Services</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($services) }}</span>
                        </a>
                    @endif

                    @if (count($jobs) > 0)
                        <a href="#jobs" data-section="jobs" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">📋</span>
                                <span class="font-medium">Jobs</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($jobs) }}</span>
                        </a>
                    @endif

                    @if (count($events) > 0)
                        <a href="#events" data-section="events" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">📢</span>
                                <span class="font-medium">Events</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($events) }}</span>
                        </a>
                    @endif

                    @if (count($listeners) > 0)
                        <a href="#listeners" data-section="listeners" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">👂</span>
                                <span class="font-medium">Listeners</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($listeners) }}</span>
                        </a>
                    @endif

                    @if (count($notifications) > 0)
                        <a href="#notifications" data-section="notifications" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">📨</span>
                                <span class="font-medium">Notifications</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($notifications) }}</span>
                        </a>
                    @endif

                    @if (count($middlewares) > 0)
                        <a href="#middlewares" data-section="middlewares" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">🔄</span>
                                <span class="font-medium">Middlewares</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($middlewares) }}</span>
                        </a>
                    @endif

                    @if (count($form_requests) > 0)
                        <a href="#form-requests" data-section="form-requests" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">📝</span>
                                <span class="font-medium">Form Requests</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($form_requests) }}</span>
                        </a>
                    @endif

                    @if (count($resources) > 0)
                        <a href="#resources" data-section="resources" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">🗃️</span>
                                <span class="font-medium">Resources</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($resources) }}</span>
                        </a>
                    @endif

                    @if (count($policies) > 0)
                        <a href="#policies" data-section="policies" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">🛡️</span>
                                <span class="font-medium">Policies</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($policies) }}</span>
                        </a>
                    @endif

                    @if (count($rules) > 0)
                        <a href="#rules" data-section="rules" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">📏</span>
                                <span class="font-medium">Rules</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($rules) }}</span>
                        </a>
                    @endif

                    @if (count($observers) > 0)
                        <a href="#observers" data-section="observers" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">👁️</span>
                                <span class="font-medium">Observers</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($observers) }}</span>
                        </a>
                    @endif

                    @if (count($actions) > 0)
                        <a href="#actions" data-section="actions" class="nav-item flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">⚙️</span>
                                <span class="font-medium">Actions</span>
                            </div>
                            <span class="count-badge px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">{{ count($actions) }}</span>
                        </a>
                    @endif
                </nav>

                {{-- Footer --}}
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        <p class="font-medium">Laravel Atlas</p>
                        <p>Architecture Documentation</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 lg:ml-0 overflow-y-auto">
            <div class="p-6 max-w-7xl mx-auto">
                {{-- Models Section --}}
                @if (count($models) > 0)
                    <div id="section-models" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">🧱</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Models</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($models) }} models found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($models as $model)
                                @include('atlas::exports.partials.model-card', ['item' => $model])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Controllers Section --}}
                @if (count($controllers) > 0)
                    <div id="section-controllers" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">🎮</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Controllers</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($controllers) }} controllers found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($controllers as $controller)
                                @include('atlas::exports.partials.controller-card', ['item' => $controller])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Routes Section --}}
                @if (count($routes) > 0)
                    <div id="section-routes" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">🛣️</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Routes</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($routes) }} routes found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($routes as $route)
                                @include('atlas::exports.partials.route-card', ['item' => $route])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Commands Section --}}
                @if (count($commands) > 0)
                    <div id="section-commands" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">⚡</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Commands</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($commands) }} artisan commands found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($commands as $command)
                                @include('atlas::exports.partials.command-card', ['item' => $command])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Services Section --}}
                @if (count($services) > 0)
                    <div id="section-services" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">🔧</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Services</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($services) }} services found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($services as $service)
                                @include('atlas::exports.partials.service-card', ['item' => $service])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Jobs Section --}}
                @if (count($jobs) > 0)
                    <div id="section-jobs" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">📋</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Jobs</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($jobs) }} background jobs found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($jobs as $job)
                                @include('atlas::exports.partials.job-card', ['item' => $job])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Events Section --}}
                @if (count($events) > 0)
                    <div id="section-events" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">📢</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Events</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($events) }} events found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($events as $event)
                                @include('atlas::exports.partials.event-card', ['item' => $event])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Listeners Section --}}
                @if (count($listeners) > 0)
                    <div id="section-listeners" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">👂</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Listeners</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($listeners) }} event listeners found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($listeners as $listener)
                                @include('atlas::exports.partials.listener-card', ['item' => $listener])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Notifications Section --}}
                @if (count($notifications) > 0)
                    <div id="section-notifications" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">📨</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($notifications) }} notifications found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($notifications as $notification)
                                @include('atlas::exports.partials.notification-card', ['item' => $notification])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Middlewares Section --}}
                @if (count($middlewares) > 0)
                    <div id="section-middlewares" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">🔄</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Middlewares</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($middlewares) }} middleware classes found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($middlewares as $middleware)
                                @include('atlas::exports.partials.middleware-card', ['item' => $middleware])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Form Requests Section --}}
                @if (count($form_requests) > 0)
                    <div id="section-form-requests" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">📝</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Form Requests</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($form_requests) }} form request classes found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($form_requests as $form_request)
                                @include('atlas::exports.partials.form-request-card', ['item' => $form_request])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Resources Section --}}
                @if (count($resources) > 0)
                    <div id="section-resources" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">🗃️</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Resources</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($resources) }} API resources found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($resources as $resource)
                                @include('atlas::exports.partials.resource-card', ['item' => $resource])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Policies Section --}}
                @if (count($policies) > 0)
                    <div id="section-policies" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">🛡️</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Policies</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($policies) }} authorization policies found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($policies as $policy)
                                @include('atlas::exports.partials.policy-card', ['item' => $policy])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Rules Section --}}
                @if (count($rules) > 0)
                    <div id="section-rules" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">📏</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Rules</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($rules) }} validation rules found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($rules as $rule)
                                @include('atlas::exports.partials.rule-card', ['item' => $rule])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Observers Section --}}
                @if (count($observers) > 0)
                    <div id="section-observers" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">👁️</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Observers</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($observers) }} model observers found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($observers as $observer)
                                @include('atlas::exports.partials.observer-card', ['item' => $observer])
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Actions Section --}}
                @if (count($actions) > 0)
                    <div id="section-actions" class="content-section fade-in">
                        <div class="mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <span class="text-3xl">⚙️</span>
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Actions</h1>
                                    <p class="text-gray-600 dark:text-gray-400">{{ count($actions) }} action classes found in your application</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3 auto-rows-fr">
                            @foreach ($actions as $action)
                                @include('atlas::exports.partials.action-card', ['item' => $action])
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>

    {{-- Overlay for mobile --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

    <script>
        // Handle mobile sidebar overlay
        $(function() {
            $('#mobile-menu-toggle').on('click', function() {
                $('#sidebar-overlay').toggleClass('hidden');
            });

            $('#sidebar-overlay').on('click', function() {
                $('#sidebar').addClass('-translate-x-full');
                $(this).addClass('hidden');
            });

            // Close mobile menu when clicking navigation items
            $('.nav-item').on('click', function() {
                if (window.innerWidth < 1024) {
                    $('#sidebar').addClass('-translate-x-full');
                    $('#sidebar-overlay').addClass('hidden');
                }
            });
        });
    </script>
</body>
</html>
