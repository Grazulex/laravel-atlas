<?php

declare(strict_types=1);

it('can run atlas:debug-commands command', function (): void {
    $this->artisan('atlas:debug-commands')
        ->assertSuccessful();
});

it('can run atlas:debug-commands with custom path option', function (): void {
    $this->artisan('atlas:debug-commands', ['--path' => 'app/Console/Commands'])
        ->assertSuccessful();
});

it('can run atlas:debug-commands with no-recursive option', function (): void {
    $this->artisan('atlas:debug-commands', ['--no-recursive' => true])
        ->assertSuccessful();
});

it('can run atlas:debug-models command', function (): void {
    $this->artisan('atlas:debug-models')
        ->assertSuccessful();
});

it('can run atlas:debug-models with custom path option', function (): void {
    $this->artisan('atlas:debug-models', ['--path' => 'app/Models'])
        ->assertSuccessful();
});

it('can run atlas:debug-models with no-recursive option', function (): void {
    $this->artisan('atlas:debug-models', ['--no-recursive' => true])
        ->assertSuccessful();
});
