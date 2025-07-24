{{-- Enhanced Navigation Component --}}
<nav class="md:w-72 bg-white dark:bg-gray-800 shadow-lg border-r border-gray-200 dark:border-gray-700 flex flex-col h-screen">
    {{-- Header --}}
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">🗺️</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Laravel Atlas</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Project Architecture</p>
                </div>
            </div>
            <button onclick="toggleDarkMode()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <span class="text-lg">🌓</span>
            </button>
        </div>
    </div>

    {{-- Navigation Sections --}}
    <div class="flex-1 overflow-y-auto py-4 nav-scroll">
        {{-- Core Components --}}
        <div class="px-4 mb-6">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                Core Components
            </h3>
            <div class="space-y-1">
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'models',
                    'icon' => '🧱',
                    'label' => 'Models',
                    'count' => count($models),
                    'description' => 'Database entities'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'controllers',
                    'icon' => '🎮',
                    'label' => 'Controllers',
                    'count' => count($controllers),
                    'description' => 'Request handlers'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'routes',
                    'icon' => '🛣️',
                    'label' => 'Routes',
                    'count' => count($routes),
                    'description' => 'Application endpoints'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'middlewares',
                    'icon' => '🛡️',
                    'label' => 'Middlewares',
                    'count' => count($middlewares),
                    'description' => 'Request filters'
                ])
            </div>
        </div>

        {{-- Services & Logic --}}
        <div class="px-4 mb-6">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                Services & Logic
            </h3>
            <div class="space-y-1">
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'services',
                    'icon' => '🔧',
                    'label' => 'Services',
                    'count' => count($services),
                    'description' => 'Business logic'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'actions',
                    'icon' => '⚡',
                    'label' => 'Actions',
                    'count' => count($actions ?? []),
                    'description' => 'Single responsibilities'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'jobs',
                    'icon' => '⚙️',
                    'label' => 'Jobs',
                    'count' => count($jobs),
                    'description' => 'Background tasks'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'commands',
                    'icon' => '💬',
                    'label' => 'Commands',
                    'count' => count($commands),
                    'description' => 'Artisan commands'
                ])
            </div>
        </div>

        {{-- Events & Communication --}}
        <div class="px-4 mb-6">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                Events & Communication
            </h3>
            <div class="space-y-1">
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'events',
                    'icon' => '⚡',
                    'label' => 'Events',
                    'count' => count($events),
                    'description' => 'System events'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'listeners',
                    'icon' => '👂',
                    'label' => 'Listeners',
                    'count' => count($listeners ?? []),
                    'description' => 'Event handlers'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'observers',
                    'icon' => '👁️',
                    'label' => 'Observers',
                    'count' => count($observers ?? []),
                    'description' => 'Model watchers'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'notifications',
                    'icon' => '📢',
                    'label' => 'Notifications',
                    'count' => count($notifications),
                    'description' => 'User alerts'
                ])
            </div>
        </div>

        {{-- Validation & Security --}}
        <div class="px-4 mb-6">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                Validation & Security
            </h3>
            <div class="space-y-1">
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'form_requests',
                    'icon' => '📋',
                    'label' => 'Form Requests',
                    'count' => count($form_requests),
                    'description' => 'Input validation'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'rules',
                    'icon' => '📏',
                    'label' => 'Rules',
                    'count' => count($rules ?? []),
                    'description' => 'Validation rules'
                ])
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'policies',
                    'icon' => '🛡️',
                    'label' => 'Policies',
                    'count' => count($policies ?? []),
                    'description' => 'Authorization logic'
                ])
            </div>
        </div>

        {{-- API & Resources --}}
        <div class="px-4 mb-6">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                API & Resources
            </h3>
            <div class="space-y-1">
                @include('atlas::exports.partials.common.nav-item', [
                    'section' => 'resources',
                    'icon' => '🔗',
                    'label' => 'API Resources',
                    'count' => count($resources),
                    'description' => 'Data transformers'
                ])
            </div>
        </div>
    </div>

    {{-- Footer Stats --}}
    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <div class="grid grid-cols-2 gap-4 text-center">
            <div>
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                    {{ array_sum([
                        count($models),
                        count($controllers),
                        count($services),
                        count($actions ?? [])
                    ]) }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Core Components</div>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ count($routes) }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Endpoints</div>
            </div>
        </div>
    </div>
</nav>
