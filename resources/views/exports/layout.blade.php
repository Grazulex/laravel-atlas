<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Atlas – {{ count($models) }} Models, {{ count($commands) }} Commands, {{ count($routes) }} Routes</title>
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

                $('[data-section]')
                    .removeClass('bg-indigo-600 text-white border-l-4 border-indigo-800 pl-2')
                    .addClass('hover:bg-indigo-100 dark:hover:bg-indigo-900');

                $(this)
                    .addClass('bg-indigo-600 text-white border-l-4 border-indigo-800 pl-2')
                    .removeClass('hover:bg-indigo-100 dark:hover:bg-indigo-900');
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
<div class="md:hidden bg-white dark:bg-gray-800 p-4 shadow">
    <button id="menu-toggle" class="text-indigo-700 dark:text-indigo-300">
        📂 Menu
    </button>
</div>

<div class="flex flex-col md:flex-row">
    <div id="sidebar" class="md:block hidden">
        <nav class="md:w-64 bg-white dark:bg-gray-800 shadow-md p-4 space-y-2 flex md:flex-col flex-row md:space-y-2 space-x-2 md:space-x-0 overflow-auto">
            <h1 class="text-xl font-bold text-indigo-700 dark:text-indigo-300 mb-2 md:mb-4 w-full">Laravel Atlas</h1>

            <button data-section="models" class="block text-left px-3 py-2 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900 w-full">
                🧱 Models
                <span class="ml-2 text-xs bg-gray-200 dark:bg-gray-700 dark:text-gray-100 text-gray-800 px-1.5 py-0.5 rounded">
                    {{ count($models) }}
                </span>
            </button>
            <button data-section="commands" class="block text-left px-3 py-2 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900 w-full">
                💬 Commands
                <span class="ml-2 text-xs bg-gray-200 dark:bg-gray-700 dark:text-gray-100 text-gray-800 px-1.5 py-0.5 rounded">
                    {{ count($commands) }}
                </span>
            </button>
            <button data-section="routes" class="block text-left px-3 py-2 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900 w-full">
                🛣️ Routes
                <span class="ml-2 text-xs bg-gray-200 dark:bg-gray-700 dark:text-gray-100 text-gray-800 px-1.5 py-0.5 rounded">
                    {{ count($routes) }}
                </span>
            </button>
            <button data-section="services" class="block text-left px-3 py-2 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900 w-full">
                🔧 Services
                <span class="ml-2 text-xs bg-gray-200 dark:bg-gray-700 dark:text-gray-100 text-gray-800 px-1.5 py-0.5 rounded">
                    {{ count($services) }}
                </span>
            </button>
            <button onclick="toggleDarkMode()" class="block text-left px-3 py-2 rounded hover:bg-indigo-100 dark:hover:bg-indigo-900 w-full">
                🌓 Toggle Theme
            </button>
        </nav>
    </div>

    <div class="flex-1 p-6">
        <div id="section-models" class="content-section hidden">
            <h2 class="text-xl font-bold">🧱 Models</h2>
            @foreach ($models as $model)
                @include('atlas::exports.partials.model-card', ['model' => $model])
            @endforeach
        </div>

        <div id="section-commands" class="content-section hidden">
            <h2 class="text-xl font-bold">💬 Commands</h2>
            @foreach ($commands as $command)
                @include('atlas::exports.partials.command-card', ['command' => $command])
            @endforeach
        </div>

        <div id="section-routes" class="content-section hidden">
            <h2 class="text-xl font-bold">🛣️ Routes</h2>
            @foreach ($routes as $route)
                @include('atlas::exports.partials.route-card', ['route' => $route])
            @endforeach
        </div>

        <div id="section-services" class="content-section hidden">
            <h2 class="text-xl font-bold">🔧 Services</h2>
            @foreach ($services as $service)
                @include('atlas::exports.partials.service-card', ['service' => $service])
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
