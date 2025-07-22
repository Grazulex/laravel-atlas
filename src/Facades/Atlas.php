<?php

declare(strict_types=1);

namespace LaravelAtlas\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, mixed> scan(string $type, array<string, mixed> $options = [])
 */
class Atlas extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'atlas';
    }
}
