<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <title>Laravel Atlas – {{ count($models) }} Models, {{ count($commands) }} Commands, {{ count($routes) }} Routes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Tailwind dark mode config
        tailwind.config = {
            darkMode: 'class',
        };

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
<div class="flex flex-col md:flex-row">
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
        </button>
    </nav>

    <div class="flex-1 p-6 space-y-8">
        <div id="section-models" class="content-section hidden">
            <h2 class="text-xl font-bold mb-4">🧱 Models</h2>
            @foreach ($models as $model)
                @include('atlas::exports.partials.model-card', ['model' => $model])
            @endforeach
        </div>

        <div id="section-commands" class="content-section hidden">
            <h2 class="text-xl font-bold mb-4">💬 Commands</h2>
            @foreach ($commands as $command)
                @include('atlas::exports.partials.command-card', ['command' => $command])
            @endforeach
        </div>

        <div id="section-routes" class="content-section hidden">
            <h2 class="text-xl font-bold mb-4">🛣️ Routes</h2>
            @foreach ($routes as $route)
                @include('atlas::exports.partials.route-card', ['route' => $route])
            @endforeach
        </div>

        <div id="section-services" class="content-section hidden">
            <h2 class="text-xl font-bold mb-4">🔧 Services</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">No service data yet.</p>
        </div>
    </div>
</div>
</body>
</html>
