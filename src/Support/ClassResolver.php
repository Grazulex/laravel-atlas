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
            $absoluteBase = base_path($baseDir);

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
}
