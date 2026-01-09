<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;

it('can render atlas export layout view with empty data', function (): void {
    $view = View::make('atlas::exports.layout', [
        'models' => [],
        'commands' => [],
        'routes' => [],
        'routes_grouping' => [],
        'services' => [],
        'notifications' => [],
        'middlewares' => [],
        'form_requests' => [],
        'events' => [],
        'controllers' => [],
        'resources' => [],
        'jobs' => [],
        'actions' => [],
        'policies' => [],
        'rules' => [],
        'listeners' => [],
        'observers' => [],
        'project_name' => 'Test Project',
        'project_description' => 'Test Description',
        'created_at' => '01/01/2024 12:00',
    ]);

    $html = $view->render();

    expect($html)->toBeString()
        ->and($html)->toContain('Test Project')
        ->and($html)->toContain('<html');
});

it('renders layout view with correct html structure', function (): void {
    $view = View::make('atlas::exports.layout', [
        'models' => [],
        'commands' => [],
        'routes' => [],
        'routes_grouping' => [],
        'services' => [],
        'notifications' => [],
        'middlewares' => [],
        'form_requests' => [],
        'events' => [],
        'controllers' => [],
        'resources' => [],
        'jobs' => [],
        'actions' => [],
        'policies' => [],
        'rules' => [],
        'listeners' => [],
        'observers' => [],
        'project_name' => 'Atlas Test',
        'project_description' => 'Atlas Description',
        'created_at' => '01/01/2024 12:00',
    ]);

    $html = $view->render();

    expect($html)->toBeString()
        ->and($html)->toContain('<!DOCTYPE html>')
        ->and($html)->toContain('</html>');
});

it('view includes dark mode toggle', function (): void {
    $view = View::make('atlas::exports.layout', [
        'models' => [],
        'commands' => [],
        'routes' => [],
        'routes_grouping' => [],
        'services' => [],
        'notifications' => [],
        'middlewares' => [],
        'form_requests' => [],
        'events' => [],
        'controllers' => [],
        'resources' => [],
        'jobs' => [],
        'actions' => [],
        'policies' => [],
        'rules' => [],
        'listeners' => [],
        'observers' => [],
        'project_name' => 'Test',
        'project_description' => 'Test',
        'created_at' => '01/01/2024 12:00',
    ]);

    $html = $view->render();

    expect($html)->toBeString()
        ->and($html)->toContain('dark');
});
