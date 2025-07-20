<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Facades;

use Grazulex\LaravelAtlas\Contracts\MapperInterface;
use Grazulex\LaravelAtlas\Contracts\ExporterInterface;
use Grazulex\LaravelAtlas\AtlasManager;
use Illuminate\Support\Facades\Facade;

/**
 * Laravel Atlas Facade
 *
 * Provides a convenient static interface to the Laravel Atlas functionality
 *
 * @method static array<string, mixed> scan(string $type, array<string, mixed> $options = [])
 * @method static string export(string $type, string $format, array<string, mixed> $options = [])
 * @method static string generate(array<string>|string $types, string $format, array<string, mixed> $options = [])
 * @method static MapperInterface mapper(string $type)
 * @method static ExporterInterface exporter(string $format)
 * @method static array<string> getAvailableTypes()
 * @method static array<string> getAvailableFormats()
 */
class Atlas extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return AtlasManager::class;
    }
}
