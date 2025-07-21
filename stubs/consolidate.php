<?php

/**
 * Script pour consolider les templates modulaires en un seul template
 */
function consolidateTemplates()
{
    $stubsDir = __DIR__;
    $partialsDir = $stubsDir . '/partials';
    $pagesDir = $stubsDir . '/pages';

    // Lire le contenu des différents templates
    $styles = file_get_contents($partialsDir . '/styles.blade.php');
    $scripts = file_get_contents($partialsDir . '/scripts.blade.php');
    $header = file_get_contents($partialsDir . '/header.blade.php');
    $sidebar = file_get_contents($partialsDir . '/sidebar.blade.php');

    // Lire toutes les pages
    $pages = [
        'overview' => file_get_contents($pagesDir . '/overview.blade.php'),
        'legend' => file_get_contents($pagesDir . '/legend.blade.php'),
        'routes' => file_get_contents($pagesDir . '/routes.blade.php'),
        'commands' => file_get_contents($pagesDir . '/commands.blade.php'),
        'flows' => file_get_contents($pagesDir . '/flows.blade.php'),
        'models' => file_get_contents($pagesDir . '/models.blade.php'),
        'observers' => file_get_contents($pagesDir . '/observers.blade.php'),
        'actions' => file_get_contents($pagesDir . '/actions.blade.php'),
        'services' => file_get_contents($pagesDir . '/services.blade.php'),
        'jobs' => file_get_contents($pagesDir . '/jobs.blade.php'),
        'events' => file_get_contents($pagesDir . '/events.blade.php'),
        'listeners' => file_get_contents($pagesDir . '/listeners.blade.php'),
        'controllers' => file_get_contents($pagesDir . '/controllers.blade.php'),
        'policies' => file_get_contents($pagesDir . '/policies.blade.php'),
        'middleware' => file_get_contents($pagesDir . '/middleware.blade.php'),
        'resources' => file_get_contents($pagesDir . '/resources.blade.php'),
        'notifications' => file_get_contents($pagesDir . '/notifications.blade.php'),
        'requests' => file_get_contents($pagesDir . '/requests.blade.php'),
        'rules' => file_get_contents($pagesDir . '/rules.blade.php'),
    ];

    // Créer le template consolidé
    $consolidatedTemplate = <<<BLADE
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \$title ?? 'Laravel Atlas - Architecture Report' }}</title>
    $styles
</head>
<body>
    <div class="container">
        $header
        $sidebar
        
        <!-- Main Content -->
        <main class="content">
BLADE;

    // Ajouter toutes les pages
    foreach ($pages as $page) {
        $consolidatedTemplate .= "\n" . $page . "\n";
    }

    $consolidatedTemplate .= <<<BLADE
        </main>
    </div>
    
    $scripts
</body>
</html>
BLADE;

    // Sauvegarder le template consolidé
    file_put_contents($stubsDir . '/intelligent-html-template-consolidated.blade.php', $consolidatedTemplate);

    echo "Template consolidé créé : intelligent-html-template-consolidated.blade.php\n";
}

consolidateTemplates();
