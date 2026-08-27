<?php

declare(strict_types=1);

namespace LaravelAtlas\Support;

/**
 * Helpers for the documented `Atlas::scan()` options.
 *
 * Every `include_*` option defaults to true, so scanning without options keeps
 * returning the full component description. Passing false drops the matching
 * keys from the scanned data.
 */
class ScanOptions
{
    /**
     * @param  array<string, mixed>  $options
     */
    public static function includes(array $options, string $key, bool $default = true): bool
    {
        return (bool) ($options[$key] ?? $default);
    }

    /**
     * Remove the data keys guarded by an option that was turned off.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $options
     * @param  array<string, array<int, string>>  $map  option name => guarded data keys
     *
     * @return array<string, mixed>
     */
    public static function filter(array $data, array $options, array $map): array
    {
        foreach ($map as $option => $keys) {
            if (self::includes($options, $option)) {
                continue;
            }

            foreach ($keys as $key) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
