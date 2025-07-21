<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Laravel Atlas - Architecture Report' }}</title>
    <?php include __DIR__ . '/partials/styles.blade.php'; ?>
</head>
<body>
    <div class="container">
        <?php include __DIR__ . '/partials/header.blade.php'; ?>
        <?php include __DIR__ . '/partials/sidebar.blade.php'; ?>
        
        <!-- Main Content -->
        <main class="content">
            <?php include __DIR__ . '/pages/overview.blade.php'; ?>
            <?php include __DIR__ . '/pages/legend.blade.php'; ?>
            <?php include __DIR__ . '/pages/routes.blade.php'; ?>
            <?php include __DIR__ . '/pages/commands.blade.php'; ?>
            <?php include __DIR__ . '/pages/flows.blade.php'; ?>
            <?php include __DIR__ . '/pages/models.blade.php'; ?>
            <?php include __DIR__ . '/pages/services.blade.php'; ?>
            <?php include __DIR__ . '/pages/jobs.blade.php'; ?>
            <?php include __DIR__ . '/pages/events.blade.php'; ?>
            <?php include __DIR__ . '/pages/listeners.blade.php'; ?>
            <?php include __DIR__ . '/pages/controllers.blade.php'; ?>
            <?php include __DIR__ . '/pages/policies.blade.php'; ?>
        </main>
    </div>
    
    <?php include __DIR__ . '/partials/scripts.blade.php'; ?>
</body>
</html>
