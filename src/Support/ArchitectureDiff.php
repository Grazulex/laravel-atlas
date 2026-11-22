<?php

declare(strict_types=1);

namespace LaravelAtlas\Support;

use InvalidArgumentException;
use JsonException;

/**
 * Compares two architecture scans (as produced by the JSON export) and
 * reports what was added, removed or changed for the main component types.
 */
class ArchitectureDiff
{
    /**
     * Component types covered by the diff.
     *
     * @var array<int, string>
     */
    public const TYPES = ['models', 'routes', 'jobs', 'events'];

    /**
     * Fields that differ between machines or checkouts without reflecting an
     * architecture change.
     *
     * @var array<int, string>
     */
    protected const IGNORED_FIELDS = ['file'];

    /**
     * @var array<string, array{added: array<int, string>, removed: array<int, string>, changed: array<string, array<int, string>>}>
     */
    protected array $changes = [];

    /**
     * Types found in only one of the two scans, which cannot be compared.
     *
     * @var array<int, string>
     */
    protected array $skipped = [];

    /**
     * Both scans accept either a full export (keyed by component type) or the
     * export of a single component type.
     *
     * @param  array<mixed>  $old
     * @param  array<mixed>  $new
     */
    public function __construct(array $old, array $new)
    {
        $oldComponents = $this->extract($old);
        $newComponents = $this->extract($new);

        foreach (self::TYPES as $type) {
            $inOld = array_key_exists($type, $oldComponents);
            $inNew = array_key_exists($type, $newComponents);

            if ($inOld && $inNew) {
                $this->changes[$type] = $this->compare(
                    $this->index($type, $oldComponents[$type]),
                    $this->index($type, $newComponents[$type]),
                );
            } elseif ($inOld || $inNew) {
                $this->skipped[] = $type;
            }
        }
    }

    /**
     * @param  array<mixed>  $old
     * @param  array<mixed>  $new
     */
    public static function between(array $old, array $new): self
    {
        return new self($old, $new);
    }

    /**
     * @throws InvalidArgumentException when a file is missing or is not a JSON scan
     */
    public static function fromFiles(string $oldPath, string $newPath): self
    {
        return new self(self::readScan($oldPath), self::readScan($newPath));
    }

    /**
     * Component types that were present in both scans and compared.
     *
     * @return array<int, string>
     */
    public function types(): array
    {
        return array_keys($this->changes);
    }

    /**
     * Component types present in only one of the two scans.
     *
     * @return array<int, string>
     */
    public function skipped(): array
    {
        return $this->skipped;
    }

    /**
     * @return array<int, string>
     */
    public function added(string $type): array
    {
        return $this->changes[$type]['added'] ?? [];
    }

    /**
     * @return array<int, string>
     */
    public function removed(string $type): array
    {
        return $this->changes[$type]['removed'] ?? [];
    }

    /**
     * Changed components, each with the names of the fields that differ.
     *
     * @return array<string, array<int, string>>
     */
    public function changed(string $type): array
    {
        return $this->changes[$type]['changed'] ?? [];
    }

    public function addedCount(): int
    {
        return $this->count('added');
    }

    public function removedCount(): int
    {
        return $this->count('removed');
    }

    public function changedCount(): int
    {
        return $this->count('changed');
    }

    public function totalChanges(): int
    {
        return $this->addedCount() + $this->removedCount() + $this->changedCount();
    }

    public function hasChanges(): bool
    {
        return $this->totalChanges() > 0;
    }

    /**
     * @return array<string, array{added: array<int, string>, removed: array<int, string>, changed: array<string, array<int, string>>}>
     */
    public function toArray(): array
    {
        return $this->changes;
    }

    /**
     * Markdown report, suitable for a pull request comment.
     */
    public function toMarkdown(): string
    {
        $lines = ['## Architecture diff', ''];

        if ($this->hasChanges()) {
            $total = $this->totalChanges();

            $lines[] = sprintf(
                '**%d %s**: %d added, %d removed, %d changed.',
                $total,
                $total === 1 ? 'change' : 'changes',
                $this->addedCount(),
                $this->removedCount(),
                $this->changedCount(),
            );
            $lines[] = '';
            $lines[] = '| Component | Added | Removed | Changed |';
            $lines[] = '|-----------|------:|--------:|--------:|';

            foreach ($this->types() as $type) {
                $lines[] = sprintf(
                    '| %s | %d | %d | %d |',
                    ucfirst($type),
                    count($this->added($type)),
                    count($this->removed($type)),
                    count($this->changed($type)),
                );
            }

            foreach ($this->types() as $type) {
                array_push($lines, ...$this->markdownSection($type));
            }
        } else {
            $lines[] = 'No architecture changes detected.';
        }

        if ($this->skipped !== []) {
            $lines[] = '';
            $lines[] = '> Not compared (present in only one of the two scans): ' . implode(', ', $this->skipped) . '.';
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * @return array<int, string>
     */
    protected function markdownSection(string $type): array
    {
        $added = $this->added($type);
        $removed = $this->removed($type);
        $changed = $this->changed($type);

        if ($added === [] && $removed === [] && $changed === []) {
            return [];
        }

        $lines = ['', '### ' . ucfirst($type)];

        if ($added !== []) {
            $lines[] = '';
            $lines[] = '**Added**';
            $lines[] = '';
            foreach ($added as $name) {
                $lines[] = '- `' . $name . '`';
            }
        }

        if ($removed !== []) {
            $lines[] = '';
            $lines[] = '**Removed**';
            $lines[] = '';
            foreach ($removed as $name) {
                $lines[] = '- `' . $name . '`';
            }
        }

        if ($changed !== []) {
            $lines[] = '';
            $lines[] = '**Changed**';
            $lines[] = '';
            foreach ($changed as $name => $fields) {
                $lines[] = '- `' . $name . '` — ' . implode(', ', array_map(
                    fn (string $field): string => '`' . $field . '`',
                    $fields,
                ));
            }
        }

        return $lines;
    }

    protected function count(string $kind): int
    {
        $count = 0;

        foreach ($this->changes as $change) {
            $count += match ($kind) {
                'added' => count($change['added']),
                'removed' => count($change['removed']),
                default => count($change['changed']),
            };
        }

        return $count;
    }

    /**
     * @return array<mixed>
     */
    protected static function readScan(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException("Scan file not found or not readable: {$path}");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new InvalidArgumentException("Unable to read scan file: {$path}");
        }

        try {
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException("Invalid JSON in scan file {$path}: {$e->getMessage()}", 0, $e);
        }

        if (! is_array($decoded)) {
            throw new InvalidArgumentException("Scan file does not contain an architecture scan: {$path}");
        }

        return $decoded;
    }

    /**
     * Component entries per type, for the types present in the scan.
     *
     * @param  array<mixed>  $scan
     *
     * @return array<string, array<mixed>>
     */
    protected function extract(array $scan): array
    {
        // Export of a single component type: {"type": "models", "data": [...]}
        if (is_string($scan['type'] ?? null) && is_array($scan['data'] ?? null)) {
            $scan = [$scan['type'] => $scan];
        }

        $components = [];

        foreach (self::TYPES as $type) {
            $section = $scan[$type] ?? null;

            if (! is_array($section)) {
                continue;
            }

            $data = $section['data'] ?? null;

            if (is_array($data)) {
                $components[$type] = $data;
            } elseif (array_is_list($section)) {
                $components[$type] = $section;
            } else {
                $components[$type] = [];
            }
        }

        return $components;
    }

    /**
     * @param  array<mixed>  $entries
     *
     * @return array<string, array<mixed>>
     */
    protected function index(string $type, array $entries): array
    {
        $indexed = [];

        foreach ($entries as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $key = $this->identify($type, $entry);

            if ($key !== null) {
                $indexed[$key] = $entry;
            }
        }

        return $indexed;
    }

    /**
     * Stable identity of a component across two scans.
     *
     * @param  array<mixed>  $entry
     */
    protected function identify(string $type, array $entry): ?string
    {
        if ($type === 'routes') {
            $uri = $entry['uri'] ?? null;

            if (! is_string($uri)) {
                return null;
            }

            $methods = is_array($entry['methods'] ?? null)
                ? implode('|', array_filter($entry['methods'], is_string(...)))
                : '';
            $domain = is_string($entry['domain'] ?? null) ? $entry['domain'] : '';

            return trim($methods . ' ' . $domain . '/' . ltrim($uri, '/'));
        }

        foreach (['class', 'name'] as $field) {
            $value = $entry[$field] ?? null;

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  array<string, array<mixed>>  $old
     * @param  array<string, array<mixed>>  $new
     *
     * @return array{added: array<int, string>, removed: array<int, string>, changed: array<string, array<int, string>>}
     */
    protected function compare(array $old, array $new): array
    {
        $added = [];
        $removed = [];
        $changed = [];

        foreach ($new as $key => $entry) {
            $key = (string) $key;

            if (! array_key_exists($key, $old)) {
                $added[] = $key;

                continue;
            }

            $fields = $this->changedFields($old[$key], $entry);

            if ($fields !== []) {
                $changed[$key] = $fields;
            }
        }

        foreach (array_keys($old) as $key) {
            if (! array_key_exists($key, $new)) {
                $removed[] = (string) $key;
            }
        }

        sort($added);
        sort($removed);
        ksort($changed);

        return [
            'added' => $added,
            'removed' => $removed,
            'changed' => $changed,
        ];
    }

    /**
     * Names of the top-level fields whose value differs between two entries.
     *
     * @param  array<mixed>  $old
     * @param  array<mixed>  $new
     *
     * @return array<int, string>
     */
    protected function changedFields(array $old, array $new): array
    {
        $fields = [];

        foreach (array_keys($old + $new) as $field) {
            if (in_array($field, self::IGNORED_FIELDS, true)) {
                continue;
            }

            if ($this->normalize($old[$field] ?? null) !== $this->normalize($new[$field] ?? null)) {
                $fields[] = (string) $field;
            }
        }

        sort($fields);

        return $fields;
    }

    /**
     * Sorts associative arrays by key so key order alone is never a change.
     * Lists keep their order, as it can be meaningful (middleware stacks).
     */
    protected function normalize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $normalized = array_map($this->normalize(...), $value);

        if (! array_is_list($normalized)) {
            ksort($normalized);
        }

        return $normalized;
    }
}
