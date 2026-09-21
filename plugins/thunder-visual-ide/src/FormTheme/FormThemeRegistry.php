<?php

declare(strict_types=1);

namespace ThunderVisualIde\FormTheme;

use RuntimeException;

final class FormThemeRegistry
{
    /** @var array<string,array<string,mixed>>|null */
    private ?array $definitions = null;

    public function __construct(
        private readonly string $root,
        private readonly ?string $customRoot = null
    ) {
    }

    /** @return array<string,array<string,mixed>> */
    public function all(): array
    {
        if ($this->definitions !== null) {
            return $this->definitions;
        }

        $definitions = [];
        $sources = [
            ['root' => $this->root, 'source' => 'built-in', 'custom' => false],
        ];
        if ($this->customRoot !== null && trim($this->customRoot) !== '') {
            $sources[] = ['root' => $this->customRoot, 'source' => 'custom', 'custom' => true];
        }

        foreach ($sources as $source) {
            foreach (glob(rtrim((string) $source['root'], '/\\') . '/*/theme.json') ?: [] as $path) {
                $raw = json_decode((string) file_get_contents($path), true);
                if (!is_array($raw)) {
                    continue;
                }

                $directory = dirname($path);
                $folder = basename($directory);
                $id = $this->slug((string) ($raw['id'] ?? $folder));
                if ($id === '' || isset($definitions[$id])) {
                    continue;
                }

                $components = [];
                $componentCode = [];
                foreach ((array) ($raw['components'] ?? []) as $component => $filename) {
                    $component = $this->componentName((string) $component);
                    $filename = basename(trim((string) $filename));
                    if ($component !== '' && $filename !== '') {
                        $componentPath = $directory . '/components/' . $filename;
                        $components[$component] = $componentPath;
                        $componentCode[$component] = $this->readOptional($componentPath);
                    }
                }

                $palettes = [];
                foreach ((array) ($raw['palettes'] ?? []) as $paletteId => $palette) {
                    if (!is_array($palette)) {
                        continue;
                    }
                    $cleanPaletteId = $this->slug((string) ($palette['id'] ?? $paletteId));
                    if ($cleanPaletteId === '') {
                        continue;
                    }
                    $colors = [];
                    foreach ((array) ($palette['colors'] ?? []) as $name => $value) {
                        $name = $this->colorName((string) $name);
                        $value = trim((string) $value);
                        if ($name !== '' && $value !== '') {
                            $colors[$name] = $value;
                        }
                    }
                    $palettes[$cleanPaletteId] = [
                        'id' => $cleanPaletteId,
                        'name' => trim((string) ($palette['name'] ?? $cleanPaletteId)) ?: $cleanPaletteId,
                        'colors' => $colors,
                    ];
                }

                if (!isset($palettes['default'])) {
                    $palettes = ['default' => [
                        'id' => 'default',
                        'name' => 'Default',
                        'colors' => [],
                    ]] + $palettes;
                }

                $definitions[$id] = [
                    'id' => $id,
                    'name' => trim((string) ($raw['name'] ?? $id)) ?: $id,
                    'description' => trim((string) ($raw['description'] ?? '')),
                    'version' => trim((string) ($raw['version'] ?? '1.0.0')) ?: '1.0.0',
                    'author' => trim((string) ($raw['author'] ?? '')),
                    'website' => trim((string) ($raw['website'] ?? '')),
                    'license' => trim((string) ($raw['license'] ?? '')),
                    'category' => trim((string) ($raw['category'] ?? 'Forms')) ?: 'Forms',
                    'tags' => array_values(array_filter((array) ($raw['tags'] ?? []), 'is_string')),
                    'order' => (int) ($raw['order'] ?? 100),
                    'default_palette' => $this->slug((string) ($raw['default_palette'] ?? 'default')) ?: 'default',
                    'palettes' => $palettes,
                    'preview_html' => $this->readOptional($directory . '/' . basename((string) ($raw['preview'] ?? 'preview.html'))),
                    'css' => $this->readOptional($directory . '/' . basename((string) ($raw['css'] ?? 'style.css'))),
                    'js' => $this->readOptional($directory . '/' . basename((string) ($raw['js'] ?? 'script.js'))),
                    'components' => $components,
                    'component_code' => $componentCode,
                    'package_path' => $folder,
                    'source' => (string) $source['source'],
                    'custom' => (bool) $source['custom'],
                    'editable' => (bool) $source['custom'],
                ];
            }
        }

        uasort($definitions, static fn (array $a, array $b): int =>
            ($a['order'] <=> $b['order']) ?: strcmp((string) $a['name'], (string) $b['name'])
        );

        return $this->definitions = $definitions;
    }

    /** @return list<array<string,mixed>> */
    public function publicDefinitions(bool $includeComponentCode = false): array
    {
        return array_values(array_map(static function (array $definition) use ($includeComponentCode): array {
            $public = [
                'id' => $definition['id'],
                'name' => $definition['name'],
                'description' => $definition['description'],
                'version' => $definition['version'],
                'author' => $definition['author'],
                'website' => $definition['website'],
                'license' => $definition['license'],
                'category' => $definition['category'],
                'tags' => $definition['tags'],
                'order' => $definition['order'],
                'default_palette' => $definition['default_palette'],
                'palettes' => array_values($definition['palettes']),
                'preview_html' => $definition['preview_html'],
                'css' => $definition['css'],
                'js' => $definition['js'],
                'package_path' => $definition['package_path'],
                'source' => $definition['source'],
                'custom' => $definition['custom'],
                'editable' => $definition['editable'],
            ];
            if ($includeComponentCode) {
                $code = (array) ($definition['component_code'] ?? []);
                $public['components'] = [
                    'field' => (string) ($code['text_input'] ?? $code['field'] ?? ''),
                    'textarea' => (string) ($code['textarea'] ?? ''),
                    'select' => (string) ($code['select'] ?? ''),
                    'checkbox' => (string) ($code['checkbox'] ?? ''),
                    'button' => (string) ($code['button'] ?? ''),
                    'form' => (string) ($code['form'] ?? ''),
                ];
            }
            return $public;
        }, $this->all()));
    }

    /** @return array<string,mixed> */
    public function get(string $id): array
    {
        $definitions = $this->all();
        $id = $this->slug($id);
        $definition = $definitions[$id] ?? $definitions['classic'] ?? reset($definitions);
        if (!is_array($definition)) {
            throw new RuntimeException('No form input themes are installed.');
        }
        return $definition;
    }

    /** @param array<string,string> $tokens */
    public function renderComponent(string $themeId, string $component, array $tokens, string $fallback): string
    {
        $definition = $this->get($themeId);
        $component = $this->componentName($component);
        $path = (string) ($definition['components'][$component] ?? '');
        if ($path === '' || !is_file($path)) {
            return $fallback;
        }

        $template = (string) file_get_contents($path);
        $hasErrorSlot = str_contains($template, '{{error_html}}');
        $replace = [];
        foreach ($tokens as $name => $value) {
            $replace['{{' . $name . '}}'] = $value;
        }

        $rendered = strtr($template, $replace);
        $errorHtml = (string) ($tokens['error_html'] ?? '');
        if (!$hasErrorSlot && $errorHtml !== '') {
            $closingWrapper = strripos($rendered, '</div>');
            if ($closingWrapper !== false) {
                return substr($rendered, 0, $closingWrapper) . $errorHtml . substr($rendered, $closingWrapper);
            }
            return $rendered . $errorHtml;
        }

        return $rendered;
    }

    /** @param array<string,string> $colors */
    public function compileCss(string $themeId, string $scope, array $colors): string
    {
        $definition = $this->get($themeId);
        $variables = [];
        foreach ($colors as $name => $value) {
            $name = $this->colorName((string) $name);
            $value = trim((string) $value);
            if ($name !== '' && $value !== '') {
                $variables[] = '--thv-theme-' . str_replace('_', '-', $name) . ':' . $value;
            }
        }

        $prefix = $variables !== [] ? $scope . '{' . implode(';', $variables) . ";}\n" : '';
        return $prefix . str_replace('{{scope}}', $scope, (string) ($definition['css'] ?? ''));
    }

    public function compileJs(string $themeId, string $scope): string
    {
        $definition = $this->get($themeId);
        return str_replace('{{scope}}', $scope, (string) ($definition['js'] ?? ''));
    }

    /** @return array<string,string> */
    public function paletteColors(string $themeId, string $paletteId): array
    {
        $definition = $this->get($themeId);
        $paletteId = $this->slug($paletteId) ?: (string) ($definition['default_palette'] ?? 'default');
        $palette = $definition['palettes'][$paletteId]
            ?? $definition['palettes'][(string) ($definition['default_palette'] ?? 'default')]
            ?? reset($definition['palettes']);

        return is_array($palette) && is_array($palette['colors'] ?? null) ? $palette['colors'] : [];
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

    private function componentName(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_]+/', '_', $value) ?? '';
        return trim($value, '_');
    }

    private function colorName(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_\-]+/', '_', $value) ?? '';
        return trim(str_replace('-', '_', $value), '_');
    }
}
