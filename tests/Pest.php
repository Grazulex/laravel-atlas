<?php

declare(strict_types=1);

use LaravelAtlas\LaravelAtlasServiceProvider;
use Orchestra\Testbench\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

// Configure the package for testing
uses()->beforeEach(function (): void {
    $this->app->register(LaravelAtlasServiceProvider::class);
})->in('Feature', 'Unit');
