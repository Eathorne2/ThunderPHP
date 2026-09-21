<?php

declare(strict_types=1);

namespace ThunderVisualIde\Html;

use RuntimeException;

final class HtmlSnippetRegistry
{
    /** @var list<array<string, mixed>> */
    private array $definitions = [];

    public function __construct(
        private readonly string $snippetsPath,
        private readonly ?string $customSnippetsPath = null
    ) {
        $this->load();
    }

    /** @return list<array<string, mixed>> */
    public function publicDefinitions(bool $includeSourceCode = true): array
    {
        if ($includeSourceCode) {
            return $this->definitions;
        }
        return array_map(static function (array $definition): array {
            unset($definition['html'], $definition['css'], $definition['js']);
            return $definition;
        }, $this->definitions);
    }

    private function load(): void
    {
        $sources = [
            ['root' => $this->snippetsPath, 'source' => 'built-in', 'custom' => false],
        ];
        if ($this->customSnippetsPath !== null && trim($this->customSnippetsPath) !== '') {
            $sources[] = ['root' => $this->customSnippetsPath, 'source' => 'custom', 'custom' => true];
        }
        $seen = [];

        foreach ($sources as $source) {
            if (!is_dir((string) $source['root'])) {
                continue;
            }
            foreach (glob(rtrim((string) $source['root'], '/\\') . '/*/snippet.json') ?: [] as $definitionPath) {
                $directory = dirname($definitionPath);
                $definition = json_decode((string) file_get_contents($definitionPath), true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($definition)) {
                    continue;
                }

                $id = trim((string) ($definition['id'] ?? basename($directory)));
                if ($id === '') {
                    throw new RuntimeException("HTML snippet is missing an ID: {$definitionPath}");
                }
                $id = $this->slug($id);
                if ($id === '' || isset($seen[$id])) {
                    continue;
                }
                $seen[$id] = true;

                $htmlFile = trim((string) ($definition['html'] ?? 'snippet.html'));
                $cssFile = trim((string) ($definition['css'] ?? 'style.css'));
                $jsFile = trim((string) ($definition['js'] ?? 'script.js'));
                $previewFile = trim((string) ($definition['preview'] ?? 'preview.svg'));

                $this->definitions[] = [
                    'id' => $id,
                    'name' => trim((string) ($definition['name'] ?? $id)) ?: $id,
                    'description' => trim((string) ($definition['description'] ?? '')),
                    'version' => trim((string) ($definition['version'] ?? '1.0.0')) ?: '1.0.0',
                    'author' => trim((string) ($definition['author'] ?? '')),
                    'website' => trim((string) ($definition['website'] ?? '')),
                    'license' => trim((string) ($definition['license'] ?? '')),
                    'category' => trim((string) ($definition['category'] ?? 'General')) ?: 'General',
                    'order' => (int) ($definition['order'] ?? 100),
                    'tags' => array_values(array_filter((array) ($definition['tags'] ?? []), 'is_string')),
                    'html' => $this->readOptionalFile($directory, $htmlFile),
                    'css' => $this->readOptionalFile($directory, $cssFile),
                    'js' => $this->readOptionalFile($directory, $jsFile),
                    'preview_data_uri' => $this->previewDataUri($directory, $previewFile),
                    'package_path' => basename($directory),
                    'source' => (string) $source['source'],
                    'custom' => (bool) $source['custom'],
                    'editable' => (bool) $source['custom'],
                ];
            }
        }

        usort($this->definitions, static fn (array $left, array $right): int =>
            ($left['order'] <=> $right['order'])
            ?: strcmp((string) $left['category'], (string) $right['category'])
            ?: strcmp((string) $left['name'], (string) $right['name'])
        );
    }

    private function readOptionalFile(string $directory, string $filename): string
    {
        if ($filename === '') {
            return '';
        }
        $path = $directory . DIRECTORY_SEPARATOR . basename($filename);
        return is_file($path) ? (string) file_get_contents($path) : '';
    }

    private function previewDataUri(string $directory, string $filename): string
    {
        if ($filename === '') {
            return '';
        }
        $path = $directory . DIRECTORY_SEPARATOR . basename($filename);
        if (!is_file($path)) {
            return '';
        }
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => '',
        };
        return $mime === '' ? '' : 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    }

    private function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }
}
