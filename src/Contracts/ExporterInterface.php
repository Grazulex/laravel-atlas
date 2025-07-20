<?php

declare(strict_types=1);

namespace Grazulex\LaravelAtlas\Contracts;

interface ExporterInterface
{
    /**
     * Export data to a specific format
     *
     * @param  array<string, mixed>  $data
     */
    public function export(array $data): string;

    /**
     * Get the file extension for this export format
     */
    public function getExtension(): string;

    /**
     * Get the MIME type for this export format
     */
    public function getMimeType(): string;
}
