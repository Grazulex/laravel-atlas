<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;

it('can render atlas export layout view', function (): void {
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
        ->and($html)->toContain('Test Description');
});

it('renders view with models data', function (): void {
    $models = [
        [
            'name' => 'User',
            'namespace' => 'App\\Models\\User',
            'table' => 'users',
            'fillable' => ['name', 'email'],
            'guarded' => [],
            'casts' => [],
            'relations' => [],
        ],
    ];

    $view = View::make('atlas::exports.layout', [
        'models' => $models,
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
        ->and($html)->toContain('User');
});

it('renders view with routes data', function (): void {
    $routes = [
        [
            'uri' => '/api/users',
            'method' => 'GET',
            'name' => 'users.index',
            'action' => 'UserController@index',
        ],
    ];

    $view = View::make('atlas::exports.layout', [
        'models' => [],
        'commands' => [],
        'routes' => $routes,
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
        ->and($html)->toContain('/api/users');
});
