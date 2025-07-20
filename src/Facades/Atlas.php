<?php

declare(strict_types=1);

namespace LaravelAtlas\Facades;

use Illuminate\Support\Facades\Facade;
use LaravelAtlas\AtlasManager;
use LaravelAtlas\Contracts\ExporterInterface;
use LaravelAtlas\Contracts\MapperInterface;

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
