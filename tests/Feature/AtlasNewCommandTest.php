<?php

declare(strict_types=1);

it('atlas:export command is registered', function (): void {
    $this->artisan('atlas:export', ['--format' => 'json'])
        ->assertSuccessful();
});

it('atlas:debug-commands command is registered', function (): void {
    $this->artisan('atlas:debug-commands')
        ->assertSuccessful();
});

it('atlas:debug-models command is registered', function (): void {
    $this->artisan('atlas:debug-models')
        ->assertSuccessful();
});

it('all atlas commands are available', function (): void {
    $this->artisan('list')
        ->expectsOutputToContain('atlas:export')
        ->expectsOutputToContain('atlas:debug-commands')
        ->expectsOutputToContain('atlas:debug-models')
        ->assertSuccessful();
});
