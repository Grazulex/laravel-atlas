<?php

declare(strict_types=1);

namespace LaravelAtlas\Support;

use Throwable;
use RuntimeException;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class BladeRenderer
{
    protected Factory $viewFactory;

    protected string $cachePath;

    protected string $viewPath;

    public function __construct()
    {
        $this->cachePath = sys_get_temp_dir() . '/laravel-atlas-blade-cache';
        $this->viewPath = sys_get_temp_dir() . '/laravel-atlas-views';

        $this->ensureDirectoriesExist();
        $this->setupViewFactory();
    }

    /**
     * Render a Blade template string with the given data
     *
     * @param  array<string, mixed>  $data
     */
    public function render(string $template, array $data = []): string
    {
        // Create a unique filename for this template
        $templateHash = md5($template);
        $templateFile = $this->viewPath . "/{$templateHash}.blade.php";

        // Write template to temporary file
        file_put_contents($templateFile, $template);

        try {
            // Render the view
            $content = $this->viewFactory->make($templateHash, $data)->render();

            // Clean up
            if (file_exists($templateFile)) {
                unlink($templateFile);
            }

            return $content;
        } catch (Throwable $e) {
            // Clean up on error
            if (file_exists($templateFile)) {
                unlink($templateFile);
            }
            throw $e;
        }
    }

    /**
     * Render a Blade template file with the given data
     *
     * @param  array<string, mixed>  $data
     */
    public function renderFile(string $templatePath, array $data = []): string
    {
        if (! file_exists($templatePath)) {
            throw new RuntimeException("Template file not found: {$templatePath}");
        }

        $template = file_get_contents($templatePath);
        if ($template === false) {
            throw new RuntimeException("Failed to read template file: {$templatePath}");
        }

        return $this->render($template, $data);
    }

    /**
     * Setup the view factory with Blade compiler
     */
    protected function setupViewFactory(): void
    {
        $filesystem = new Filesystem;
        $eventDispatcher = new Dispatcher;

        // Setup view finder
        $viewFinder = new FileViewFinder($filesystem, [$this->viewPath]);

        // Setup engine resolver
        $resolver = new EngineResolver;

        // PHP Engine
        $resolver->register('php', fn(): PhpEngine => new PhpEngine($filesystem));

        // Blade Engine
        $resolver->register('blade', function () use ($filesystem): CompilerEngine {
            $compiler = new BladeCompiler($filesystem, $this->cachePath);

            return new CompilerEngine($compiler);
        });

        // Create view factory
        $this->viewFactory = new Factory($resolver, $viewFinder, $eventDispatcher);
    }

    /**
     * Ensure required directories exist
     */
    protected function ensureDirectoriesExist(): void
    {
        if (! is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }

        if (! is_dir($this->viewPath)) {
            mkdir($this->viewPath, 0755, true);
        }
    }

    /**
     * Clean up temporary files and directories
     */
    public function cleanup(): void
    {
        $filesystem = new Filesystem;

        if (is_dir($this->cachePath)) {
            $filesystem->deleteDirectory($this->cachePath);
        }

        if (is_dir($this->viewPath)) {
            $filesystem->deleteDirectory($this->viewPath);
        }
    }

    /**
     * Destructor to clean up temporary files
     */
    public function __destruct()
    {
        $this->cleanup();
    }
}
