<?php

declare(strict_types=1);

namespace LaravelAtlas\Support;

use Illuminate\Support\Str;

class ClassResolver
{
    /**
     * Resolve the fully qualified class name from a given path using PSR-4.
     */
    public static function resolveFromPath(string $filePath): ?string
    {
        $composerJsonPath = base_path('composer.json');
        if (! file_exists($composerJsonPath)) {
            return null;
        }

        $composerContent = file_get_contents($composerJsonPath);
        if ($composerContent === false) {
            return null;
        }

        $composer = json_decode($composerContent, true);
        $autoload = $composer['autoload']['psr-4'] ?? [];

        foreach ($autoload as $namespace => $baseDir) {
            $absoluteBase = rtrim(base_path($baseDir), '/');

            if (Str::startsWith($filePath, $absoluteBase)) {
                $relativePath = Str::replaceFirst($absoluteBase, '', $filePath);
                $relativeClass = str_replace(['/', '.php'], ['\\', ''], $relativePath);
                $fqcn = rtrim((string) $namespace, '\\') . '\\' . ltrim($relativeClass, '\\');

                if (class_exists($fqcn)) {
                    return $fqcn;
                }
            }
        }

        return null;
    }

    /**
     * Resolve the class declared in a file: PSR-4 first (from the app's
     * composer.json), then the `namespace` and class declared in the file
     * itself, for classes autoloaded outside the app's PSR-4 roots.
     */
    public static function resolveFromFile(string $filePath): ?string
    {
        $fqcn = self::resolveFromPath($filePath);

        if ($fqcn !== null) {
            return $fqcn;
        }

        $source = @file_get_contents($filePath);

        if ($source === false || ! preg_match('/^\s*(?:(?:abstract|final|readonly)\s+)*class\s+(\w+)/m', $source, $class)) {
            return null;
        }

        $namespace = preg_match('/^\s*namespace\s+([\w\\\\]+)\s*;/m', $source, $match) ? $match[1] . '\\' : '';
        $fqcn = $namespace . $class[1];

        return class_exists($fqcn) ? $fqcn : null;
    }
}
