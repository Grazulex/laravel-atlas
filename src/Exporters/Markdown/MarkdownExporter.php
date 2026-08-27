<?php

declare(strict_types=1);

namespace LaravelAtlas\Exporters\Markdown;

use LaravelAtlas\Contracts\AtlasExporter;

class MarkdownExporter implements AtlasExporter
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(protected array $options = []) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function render(array $data): string
    {
        $sections = $this->normalize($data);

        $lines = ['# Laravel Atlas', ''];

        if ($sections === []) {
            return implode("\n", $lines);
        }

        if ($this->option('include_stats')) {
            foreach ($this->renderSummary($sections) as $line) {
                $lines[] = $line;
            }
        }

        foreach ($sections as $type => $section) {
            foreach ($this->renderSection($type, $section) as $line) {
                $lines[] = $line;
            }
        }

        return rtrim(implode("\n", $lines)) . "\n";
    }

    /**
     * Turn either a single type payload or a map of type payloads into a
     * uniform `type => list of components` structure.
     *
     * @param  array<string, mixed>  $data
     *
     * @return array<string, array<int, mixed>>
     */
    protected function normalize(array $data): array
    {
        if (isset($data['type']) && is_string($data['type']) && isset($data['data']) && is_array($data['data'])) {
            return [$data['type'] => array_values($data['data'])];
        }

        $sections = [];

        foreach ($data as $type => $payload) {
            if (! is_string($type)) {
                continue;
            }

            if (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
                $sections[$type] = array_values($payload['data']);

                continue;
            }

            if (is_array($payload)) {
                $sections[$type] = array_values($payload);
            }
        }

        return $sections;
    }

    /**
     * @param  array<string, array<int, mixed>>  $sections
     *
     * @return array<int, string>
     */
    protected function renderSummary(array $sections): array
    {
        $lines = ['## Summary', '', '| Type | Count |', '| --- | --- |'];
        $total = 0;

        foreach ($sections as $type => $components) {
            $count = count($components);
            $total += $count;
            $lines[] = "| {$type} | {$count} |";
        }

        $lines[] = '';
        $lines[] = "**Total components:** {$total}";
        $lines[] = '';

        return $lines;
    }

    /**
     * @param  array<int, mixed>  $components
     *
     * @return array<int, string>
     */
    protected function renderSection(string $type, array $components): array
    {
        $lines = ['## ' . $this->humanize($type), ''];

        if ($components === []) {
            $lines[] = '_No components found._';
            $lines[] = '';

            return $lines;
        }

        $count = count($components);
        $lines[] = '_' . $count . ' component' . ($count === 1 ? '' : 's') . '_';
        $lines[] = '';

        foreach ($components as $index => $component) {
            $title = $this->titleFor($component, $index);

            if (! $this->option('detailed_sections')) {
                $lines[] = '- ' . $title;

                continue;
            }

            $lines[] = '### ' . $title;
            $lines[] = '';

            if (is_array($component)) {
                foreach ($component as $key => $value) {
                    if ($key === $this->titleKey($component)) {
                        continue;
                    }

                    foreach ($this->renderValue((string) $key, $value, 0) as $line) {
                        $lines[] = $line;
                    }
                }
            } else {
                $lines[] = $this->scalar($component);
            }

            $lines[] = '';
        }

        if (! $this->option('detailed_sections')) {
            $lines[] = '';
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    protected function renderValue(string $key, mixed $value, int $depth): array
    {
        $indent = str_repeat('  ', $depth);

        if (! is_array($value)) {
            return [$indent . '- **' . $key . ':** ' . $this->scalar($value)];
        }

        if ($value === []) {
            return [$indent . '- **' . $key . ':** _none_'];
        }

        $lines = [$indent . '- **' . $key . ':**'];

        foreach ($value as $childKey => $childValue) {
            if (is_string($childKey)) {
                foreach ($this->renderValue($childKey, $childValue, $depth + 1) as $line) {
                    $lines[] = $line;
                }

                continue;
            }

            if (is_array($childValue)) {
                $lines[] = $indent . '  - ' . $this->inline($childValue);

                continue;
            }

            $lines[] = $indent . '  - ' . $this->scalar($childValue);
        }

        return $lines;
    }

    /**
     * Render a nested map on a single bullet, so lists of structures stay readable.
     *
     * @param  array<array-key, mixed>  $value
     */
    protected function inline(array $value): string
    {
        if ($value === []) {
            return '_none_';
        }

        $parts = [];

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $parts[] = $key . ': ' . ($item === [] ? '_none_' : '[' . implode(', ', array_map(fn (mixed $sub): string => is_array($sub) ? '…' : $this->scalar($sub), $item)) . ']');

                continue;
            }

            $parts[] = is_string($key) ? $key . ': ' . $this->scalar($item) : $this->scalar($item);
        }

        return implode(', ', $parts);
    }

    protected function scalar(mixed $value): string
    {
        if ($value === null) {
            return '_null_';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_object($value) && ! method_exists($value, '__toString')) {
            return '_object_';
        }

        if (is_array($value)) {
            return $this->inline($value);
        }

        $string = trim((string) preg_replace('/\s+/', ' ', (string) $value));

        return $string === '' ? '_empty_' : $string;
    }

    protected function titleFor(mixed $component, int|string $index): string
    {
        if (is_array($component)) {
            $key = $this->titleKey($component);

            if ($key !== null) {
                return $this->scalar($component[$key]);
            }
        }

        return 'Component #' . ((int) $index + 1);
    }

    /**
     * @param  array<array-key, mixed>  $component
     */
    protected function titleKey(array $component): ?string
    {
        foreach (['class', 'name', 'uri'] as $key) {
            if (isset($component[$key]) && ! is_array($component[$key]) && (string) $component[$key] !== '') {
                return $key;
            }
        }

        return null;
    }

    protected function humanize(string $type): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $type));
    }

    protected function option(string $key): bool
    {
        return (bool) ($this->options[$key] ?? true);
    }
}
