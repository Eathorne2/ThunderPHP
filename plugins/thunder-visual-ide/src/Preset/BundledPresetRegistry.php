<?php

declare(strict_types=1);

namespace ThunderVisualIde\Preset;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class BundledPresetRegistry
{
    /** @var list<array<string, mixed>> */
    private array $presets = [];

    public function __construct(private readonly string $root)
    {
        $this->load();
    }

    /** @return list<array<string, mixed>> */
    public function publicDefinitions(): array
    {
        return $this->presets;
    }

    private function load(): void
    {
        if (!is_dir($this->root)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->root, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $filename = strtolower($file->getFilename());
            if (!str_ends_with($filename, '.thunder-nodes.json') && !str_ends_with($filename, '.json')) {
                continue;
            }

            $preset = json_decode((string) file_get_contents($file->getPathname()), true);
            if (!is_array($preset) || !is_array($preset['nodes'] ?? null) || !is_array($preset['edges'] ?? null)) {
                continue;
            }

            $graphKind = trim((string) ($preset['graph_kind'] ?? ''));
            if ($graphKind === '') {
                continue;
            }

            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen(rtrim($this->root, '/\\')) + 1));
            $category = trim(str_replace('\\', '/', dirname($relative)), './');
            $category = $category === '' ? 'General' : str_replace(['-', '_'], ' ', $category);
            $category = ucwords($category);

            $name = trim((string) ($preset['name'] ?? ''));
            if ($name === '') {
                $name = preg_replace('/\.thunder-nodes\.json$|\.json$/i', '', $file->getFilename()) ?: 'Preset';
                $name = ucwords(str_replace(['-', '_'], ' ', $name));
            }

            $preset['name'] = $name;
            $preset['category'] = trim((string) ($preset['category'] ?? $category)) ?: $category;
            $preset['description'] = trim((string) ($preset['description'] ?? ''));
            $preset['order'] = (int) ($preset['order'] ?? 100);
            $preset['package_path'] = $relative;
            $preset['modified_at'] = gmdate(DATE_ATOM, max(0, (int) $file->getMTime()));
            $preset['bundled'] = true;
            $this->presets[] = $preset;
        }

        usort($this->presets, static fn (array $left, array $right): int =>
            ($left['order'] <=> $right['order'])
            ?: strcmp((string) $left['category'], (string) $right['category'])
            ?: strcmp((string) $left['name'], (string) $right['name'])
        );
    }
}
