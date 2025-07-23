<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Atlas Export</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(function () {
            $('[data-section]').on('click', function () {
                const section = $(this).data('section');
                $('.content-section').hide();
                $('#section-' + section).show();
                $('[data-section]').removeClass('bg-indigo-600 text-white');
                $(this).addClass('bg-indigo-600 text-white');
            });
            $('[data-section]').first().click();
        });
    </script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">
<div class="flex">
    <nav class="w-64 bg-white shadow-md p-4 space-y-2">
        <h1 class="text-2xl font-bold text-indigo-700 mb-4">Laravel Atlas</h1>
        <button data-section="models" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">🧱 Models</button>
        <button data-section="commands" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">💬 Commands</button>
        <button data-section="services" class="block w-full text-left px-3 py-2 rounded hover:bg-indigo-100">🔧 Services</button>
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

        <div id="section-services" class="content-section hidden">
            <h2 class="text-xl font-bold mb-4">🔧 Services</h2>
            <p class="text-sm text-gray-500">No service data yet.</p>
        </div>
    </div>
</div>
</body>
</html>
