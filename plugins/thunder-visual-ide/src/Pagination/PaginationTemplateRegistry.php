<?php

declare(strict_types=1);

namespace ThunderVisualIde\Pagination;

use RuntimeException;

final class PaginationTemplateRegistry
{
    /** @var array<string,array<string,mixed>>|null */
    private ?array $definitions = null;

    public function __construct(private readonly string $root)
    {
    }

    /** @return array<string,array<string,mixed>> */
    public function all(): array
    {
        if ($this->definitions !== null) {
            return $this->definitions;
        }

        $definitions = [];
        foreach (glob(rtrim($this->root, '/\\') . '/*/template.json') ?: [] as $path) {
            $raw = json_decode((string) file_get_contents($path), true);
            if (!is_array($raw)) {
                continue;
            }

            $folder = basename(dirname($path));
            $id = $this->slug((string) ($raw['id'] ?? $folder));
            if ($id === '') {
                continue;
            }

            $directory = dirname($path);
            $definitions[$id] = [
                'id' => $id,
                'name' => trim((string) ($raw['name'] ?? $id)) ?: $id,
                'description' => trim((string) ($raw['description'] ?? '')),
                'order' => (int) ($raw['order'] ?? 100),
                'defaults' => is_array($raw['defaults'] ?? null) ? $raw['defaults'] : [],
                'palette' => is_array($raw['palette'] ?? null) ? $raw['palette'] : [],
                'preview_html' => $this->readOptional($directory . '/preview.html'),
                'css' => $this->readOptional($directory . '/style.css'),
                'renderer_path' => $directory . '/renderer.php.tpl',
                'package_path' => $folder,
            ];
        }

        uasort($definitions, static fn (array $a, array $b): int =>
            ($a['order'] <=> $b['order']) ?: strcmp((string) $a['name'], (string) $b['name'])
        );

        return $this->definitions = $definitions;
    }

    /** @return list<array<string,mixed>> */
    public function publicDefinitions(): array
    {
        return array_values(array_map(static function (array $definition): array {
            return [
                'id' => $definition['id'],
                'name' => $definition['name'],
                'description' => $definition['description'],
                'order' => $definition['order'],
                'defaults' => $definition['defaults'],
                'palette' => $definition['palette'],
                'preview_html' => $definition['preview_html'],
                'css' => $definition['css'],
                'package_path' => $definition['package_path'],
            ];
        }, $this->all()));
    }

    /** @return array<string,mixed> */
    public function get(string $id): array
    {
        $id = $this->slug($id);
        $definition = $this->all()[$id] ?? null;
        if ($definition === null) {
            $definition = reset($this->definitions ?? []);
        }
        if (!is_array($definition)) {
            throw new RuntimeException('No pagination templates are installed.');
        }
        return $definition;
    }

    public function renderer(string $id): string
    {
        $definition = $this->get($id);
        $path = (string) ($definition['renderer_path'] ?? '');
        if ($path === '' || !is_file($path)) {
            throw new RuntimeException('Pagination renderer template is missing for ' . $id . '.');
        }
        return (string) file_get_contents($path);
    }

    private function readOptional(string $path): string
    {
        return is_file($path) ? (string) file_get_contents($path) : '';
    }

    private function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }
}
