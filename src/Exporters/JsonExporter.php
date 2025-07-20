<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters;

class JsonExporter extends BaseExporter
{
    /**
     * {@inheritdoc}
     */
    public function export(array $data): string
    {
        $prettyPrint = $this->config('pretty_print', true);
        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

        if ($prettyPrint) {
            $flags |= JSON_PRETTY_PRINT;
        }

        return json_encode($data, $flags) ?: '{}';
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): string
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType(): string
    {
        return 'application/json';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getDefaultConfig(): array
    {
        return [
            'pretty_print' => true,
        ];
    }
}
