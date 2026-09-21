<?php

declare(strict_types=1);

namespace ThunderVisualIde\Marketplace;

use RuntimeException;
use ThunderVisualIde\Compiler\ZipReader;
use ThunderVisualIde\FormTheme\FormThemeRegistry;
use ThunderVisualIde\Html\HtmlSnippetRegistry;
use ThunderVisualIde\ZipWriter;

final class MarketplaceAssetManager
{
    private const MAX_PACKAGE_BYTES = 20 * 1024 * 1024;

    /** @var array<string,string> */
    private const DEFAULT_THEME_COLORS = [
        'primary' => '#2563eb',
        'secondary' => '#64748b',
        'surface' => '#ffffff',
        'surface_alt' => '#f8fafc',
        'text' => '#0f172a',
        'muted' => '#64748b',
        'border' => '#cbd5e1',
        'danger' => '#b91c1c',
        'on_primary' => '#ffffff',
        'success' => '#16a34a',
        'warning' => '#d97706',
        'info' => '#0891b2',
    ];

    /** @var array<string,string> */
    private const DEFAULT_COMPONENTS = [
        'field' => '<div{{wrapper_attributes}}>{{label_html}}{{control_html}}{{error_html}}</div>',
        'textarea' => '<div{{wrapper_attributes}}>{{label_html}}{{control_html}}{{error_html}}</div>',
        'select' => '<div{{wrapper_attributes}}>{{label_html}}{{control_html}}{{error_html}}</div>',
        'checkbox' => '<div{{wrapper_attributes}}>{{control_html}}{{label_html}}{{error_html}}</div>',
        'button' => '<div{{wrapper_attributes}}>{{control_html}}</div>',
        'form' => "<form{{wrapper_attributes}}>\n{{children_html}}\n</form>",
    ];

    public function __construct(
        private readonly string $builtInThemeRoot,
        private readonly string $customThemeRoot,
        private readonly string $builtInSnippetRoot,
        private readonly string $customSnippetRoot
    ) {
    }

    /** @return array{themes:list<array<string,mixed>>,snippets:list<array<string,mixed>>,starter:array<string,mixed>} */
    public function studioData(): array
    {
        return [
            'themes' => (new FormThemeRegistry($this->builtInThemeRoot, $this->customThemeRoot))->publicDefinitions(true),
            'snippets' => (new HtmlSnippetRegistry($this->builtInSnippetRoot, $this->customSnippetRoot))->publicDefinitions(true),
            'starter' => [
                'theme_colors' => self::DEFAULT_THEME_COLORS,
                'theme_components' => self::DEFAULT_COMPONENTS,
            ],
        ];
    }

    /** @param array<string,mixed> $payload @return array<string,mixed> */
    public function saveTheme(array $payload): array
    {
        $id = $this->slug((string) ($payload['id'] ?? ''));
        $originalId = $this->slug((string) ($payload['original_id'] ?? ''));
        $name = trim((string) ($payload['name'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));
        $preview = trim((string) ($payload['preview_html'] ?? ''));
        $css = trim((string) ($payload['css'] ?? ''));
        $js = trim((string) ($payload['js'] ?? ''));
        $components = $this->normaliseComponents($payload['components'] ?? []);
        $palettes = $this->normalisePalettes($payload['palettes'] ?? []);

        $errors = [];
        $warnings = [];
        if ($id === '') {
            $errors[] = 'Theme ID is required and must contain letters or numbers.';
        }
        if ($name === '') {
            $errors[] = 'Theme name is required.';
        }
        if ($preview === '') {
            $errors[] = 'Preview HTML is required.';
        }
        if ($css === '') {
            $errors[] = 'Theme CSS is required.';
        } elseif (!str_contains($css, '{{scope}}')) {
            $errors[] = 'Theme CSS must use {{scope}} so generated selectors remain isolated.';
        }
        if ($originalId !== '' && $originalId !== $id) {
            $errors[] = 'A saved theme ID cannot be changed. Create a new theme when you need a different ID.';
        }
        if ($this->builtInThemeExists($id)) {
            $errors[] = 'That ID belongs to a built-in theme and cannot be replaced.';
        }
        $errors = array_merge($errors, $this->validateThemeComponents($components));
        if (!str_contains($preview, 'tvi-theme-preview')) {
            $preview = '<div class="tvi-theme-preview">' . "\n" . $preview . "\n</div>";
            $warnings[] = 'The preview was wrapped in .tvi-theme-preview automatically.';
        }
        if ($js !== '' && preg_match('/\bdocument\.(querySelector|querySelectorAll|getElementById)\s*\(/', $js) === 1) {
            $warnings[] = 'Theme JavaScript queries the whole document. Prefer {{scope}} or a scoped root to avoid affecting other Views.';
        }
        if ($errors !== []) {
            throw new RuntimeException(implode("\n", $errors));
        }

        $theme = [
            'format' => 'thunder-visual-ide-form-theme',
            'format_version' => 1,
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'version' => trim((string) ($payload['version'] ?? '1.0.0')) ?: '1.0.0',
            'author' => trim((string) ($payload['author'] ?? '')),
            'website' => trim((string) ($payload['website'] ?? '')),
            'license' => trim((string) ($payload['license'] ?? 'Commercial')) ?: 'Commercial',
            'category' => trim((string) ($payload['category'] ?? 'Forms')) ?: 'Forms',
            'tags' => $this->normaliseTags($payload['tags'] ?? []),
            'order' => (int) ($payload['order'] ?? 100),
            'palettes' => $palettes,
            'default_palette' => $this->slug((string) ($payload['default_palette'] ?? 'default')) ?: 'default',
            'preview' => 'preview.html',
            'css' => 'style.css',
            'js' => 'script.js',
            'components' => [
                'text_input' => 'field.html.tpl',
                'email_input' => 'field.html.tpl',
                'password_input' => 'field.html.tpl',
                'number_input' => 'field.html.tpl',
                'date_input' => 'field.html.tpl',
                'file_input' => 'field.html.tpl',
                'textarea' => 'textarea.html.tpl',
                'select' => 'select.html.tpl',
                'checkbox' => 'checkbox.html.tpl',
                'button' => 'button.html.tpl',
                'form' => 'form.html.tpl',
            ],
        ];
        if (!isset($theme['palettes'][$theme['default_palette']])) {
            $theme['default_palette'] = array_key_first($theme['palettes']) ?: 'default';
        }

        $files = [
            'theme.json' => $this->json($theme),
            'preview.html' => $preview . "\n",
            'style.css' => $css . "\n",
            'script.js' => $js . ($js === '' ? '' : "\n"),
            'components/field.html.tpl' => $components['field'] . "\n",
            'components/textarea.html.tpl' => $components['textarea'] . "\n",
            'components/select.html.tpl' => $components['select'] . "\n",
            'components/checkbox.html.tpl' => $components['checkbox'] . "\n",
            'components/button.html.tpl' => $components['button'] . "\n",
            'components/form.html.tpl' => $components['form'] . "\n",
            'README.md' => $this->themeReadme($theme),
        ];
        $this->writePackage($this->customThemeRoot, $id, $files);

        return ['id' => $id, 'warnings' => $warnings];
    }

    /** @param array<string,mixed> $payload @return array<string,mixed> */
    public function saveSnippet(array $payload): array
    {
        $id = $this->slug((string) ($payload['id'] ?? ''));
        $originalId = $this->slug((string) ($payload['original_id'] ?? ''));
        $name = trim((string) ($payload['name'] ?? ''));
        $html = trim((string) ($payload['html'] ?? ''));
        $css = trim((string) ($payload['css'] ?? ''));
        $js = trim((string) ($payload['js'] ?? ''));
        $errors = [];
        $warnings = [];

        if ($id === '') {
            $errors[] = 'Snippet ID is required and must contain letters or numbers.';
        }
        if ($name === '') {
            $errors[] = 'Snippet name is required.';
        }
        if ($html === '') {
            $errors[] = 'Snippet HTML is required.';
        }
        if ($originalId !== '' && $originalId !== $id) {
            $errors[] = 'A saved snippet ID cannot be changed. Create a new snippet when you need a different ID.';
        }
        if ($this->builtInSnippetExists($id)) {
            $errors[] = 'That ID belongs to a built-in snippet and cannot be replaced.';
        }
        if (preg_match('/<\/?(?:html|head|body)\b/i', $html) === 1) {
            $errors[] = 'Snippet HTML must be a reusable fragment, not a complete html/head/body document.';
        }
        if (preg_match('/<script\b/i', $html) === 1 || preg_match('/<style\b/i', $html) === 1) {
            $errors[] = 'Keep CSS and JavaScript in their dedicated editors instead of embedding style or script tags in HTML.';
        }
        if ($css !== '' && preg_match('/(^|[}\s,])(html|body|:root)\s*[{,]/mi', $css) === 1) {
            $warnings[] = 'The CSS contains a global html, body or :root selector. Component compilation scopes ordinary selectors, but global rules may behave unexpectedly.';
        }
        if ($js !== '' && preg_match('/\bdocument\.(querySelector|querySelectorAll|getElementById)\s*\(/', $js) === 1) {
            $warnings[] = 'Snippet JavaScript queries the whole document. Prefer root.querySelector(...) so each component instance stays isolated.';
        }
        if ($errors !== []) {
            throw new RuntimeException(implode("\n", $errors));
        }

        $definition = [
            'format' => 'thunder-visual-ide-html-snippet',
            'format_version' => 1,
            'id' => $id,
            'name' => $name,
            'description' => trim((string) ($payload['description'] ?? '')),
            'version' => trim((string) ($payload['version'] ?? '1.0.0')) ?: '1.0.0',
            'author' => trim((string) ($payload['author'] ?? '')),
            'website' => trim((string) ($payload['website'] ?? '')),
            'license' => trim((string) ($payload['license'] ?? 'Commercial')) ?: 'Commercial',
            'category' => trim((string) ($payload['category'] ?? 'General')) ?: 'General',
            'order' => (int) ($payload['order'] ?? 100),
            'tags' => $this->normaliseTags($payload['tags'] ?? []),
            'html' => 'snippet.html',
            'css' => 'style.css',
            'js' => 'script.js',
        ];
        $files = [
            'snippet.json' => $this->json($definition),
            'snippet.html' => $html . "\n",
            'style.css' => $css . ($css === '' ? '' : "\n"),
            'script.js' => $js . ($js === '' ? '' : "\n"),
            'README.md' => $this->snippetReadme($definition),
        ];
        $this->writePackage($this->customSnippetRoot, $id, $files);

        return ['id' => $id, 'warnings' => $warnings];
    }

    public function deleteTheme(string $id): void
    {
        $this->deleteCustomPackage($this->customThemeRoot, $id, 'theme');
    }

    public function deleteSnippet(string $id): void
    {
        $this->deleteCustomPackage($this->customSnippetRoot, $id, 'snippet');
    }

    public function exportTheme(string $id): string
    {
        return $this->exportCustomPackage($this->customThemeRoot, $id, 'form-theme');
    }

    public function exportSnippet(string $id): string
    {
        return $this->exportCustomPackage($this->customSnippetRoot, $id, 'html-snippet');
    }

    /** @return array{type:string,id:string,warnings:list<string>} */
    public function importArchive(string $archive, string $expectedType = ''): array
    {
        if ($archive === '' || strlen($archive) > self::MAX_PACKAGE_BYTES) {
            throw new RuntimeException('Marketplace packages must be non-empty ZIP archives smaller than 20 MB.');
        }
        $files = ZipReader::files($archive, true);
        if (isset($files['theme.json'])) {
            if ($expectedType !== '' && $expectedType !== 'theme') {
                throw new RuntimeException('The selected package is a form theme, not an HTML snippet.');
            }
            return $this->importThemeFiles($files);
        }
        if (isset($files['snippet.json'])) {
            if ($expectedType !== '' && $expectedType !== 'snippet') {
                throw new RuntimeException('The selected package is an HTML snippet, not a form theme.');
            }
            return $this->importSnippetFiles($files);
        }
        throw new RuntimeException('The ZIP must contain theme.json or snippet.json at its package root.');
    }

    /** @param array<string,string> $files @return array{type:string,id:string,warnings:list<string>} */
    private function importThemeFiles(array $files): array
    {
        $definition = json_decode((string) $files['theme.json'], true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($definition)) {
            throw new RuntimeException('theme.json is invalid.');
        }
        $id = $this->slug((string) ($definition['id'] ?? ''));
        if ($id === '' || $this->builtInThemeExists($id)) {
            throw new RuntimeException('The imported theme has a missing ID or conflicts with a built-in theme.');
        }
        $componentMap = is_array($definition['components'] ?? null) ? $definition['components'] : [];
        $payload = [
            'id' => $id,
            'name' => $definition['name'] ?? $id,
            'description' => $definition['description'] ?? '',
            'version' => $definition['version'] ?? '1.0.0',
            'author' => $definition['author'] ?? '',
            'website' => $definition['website'] ?? '',
            'license' => $definition['license'] ?? 'Commercial',
            'category' => $definition['category'] ?? 'Forms',
            'tags' => $definition['tags'] ?? [],
            'order' => $definition['order'] ?? 100,
            'default_palette' => $definition['default_palette'] ?? 'default',
            'palettes' => $definition['palettes'] ?? [],
            'preview_html' => $files[basename((string) ($definition['preview'] ?? 'preview.html'))] ?? '',
            'css' => $files[basename((string) ($definition['css'] ?? 'style.css'))] ?? '',
            'js' => $files[basename((string) ($definition['js'] ?? 'script.js'))] ?? '',
            'components' => [
                'field' => $files['components/' . basename((string) ($componentMap['text_input'] ?? 'field.html.tpl'))] ?? '',
                'textarea' => $files['components/' . basename((string) ($componentMap['textarea'] ?? 'textarea.html.tpl'))] ?? '',
                'select' => $files['components/' . basename((string) ($componentMap['select'] ?? 'select.html.tpl'))] ?? '',
                'checkbox' => $files['components/' . basename((string) ($componentMap['checkbox'] ?? 'checkbox.html.tpl'))] ?? '',
                'button' => $files['components/' . basename((string) ($componentMap['button'] ?? 'button.html.tpl'))] ?? '',
                'form' => $files['components/' . basename((string) ($componentMap['form'] ?? 'form.html.tpl'))] ?? '',
            ],
        ];
        $result = $this->saveTheme($payload);
        return ['type' => 'theme', 'id' => $result['id'], 'warnings' => $result['warnings']];
    }

    /** @param array<string,string> $files @return array{type:string,id:string,warnings:list<string>} */
    private function importSnippetFiles(array $files): array
    {
        $definition = json_decode((string) $files['snippet.json'], true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($definition)) {
            throw new RuntimeException('snippet.json is invalid.');
        }
        $id = $this->slug((string) ($definition['id'] ?? ''));
        if ($id === '' || $this->builtInSnippetExists($id)) {
            throw new RuntimeException('The imported snippet has a missing ID or conflicts with a built-in snippet.');
        }
        $payload = [
            'id' => $id,
            'name' => $definition['name'] ?? $id,
            'description' => $definition['description'] ?? '',
            'version' => $definition['version'] ?? '1.0.0',
            'author' => $definition['author'] ?? '',
            'website' => $definition['website'] ?? '',
            'license' => $definition['license'] ?? 'Commercial',
            'category' => $definition['category'] ?? 'General',
            'tags' => $definition['tags'] ?? [],
            'order' => $definition['order'] ?? 100,
            'html' => $files[basename((string) ($definition['html'] ?? 'snippet.html'))] ?? '',
            'css' => $files[basename((string) ($definition['css'] ?? 'style.css'))] ?? '',
            'js' => $files[basename((string) ($definition['js'] ?? 'script.js'))] ?? '',
        ];
        $result = $this->saveSnippet($payload);
        return ['type' => 'snippet', 'id' => $result['id'], 'warnings' => $result['warnings']];
    }

    /** @param mixed $value @return array<string,array{name:string,colors:array<string,string>}> */
    private function normalisePalettes(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }
        $palettes = [];
        foreach ((array) $value as $paletteId => $palette) {
            if (!is_array($palette)) {
                continue;
            }
            $id = $this->slug((string) ($palette['id'] ?? $paletteId));
            if ($id === '') {
                continue;
            }
            $colors = [];
            foreach ((array) ($palette['colors'] ?? []) as $name => $color) {
                $cleanName = $this->tokenName((string) $name);
                $cleanColor = trim((string) $color);
                if ($cleanName !== '' && $cleanColor !== '') {
                    $colors[$cleanName] = $cleanColor;
                }
            }
            $palettes[$id] = [
                'name' => trim((string) ($palette['name'] ?? ucfirst(str_replace('-', ' ', $id)))) ?: $id,
                'colors' => $colors,
            ];
        }
        if ($palettes === []) {
            $palettes['default'] = ['name' => 'Default', 'colors' => self::DEFAULT_THEME_COLORS];
        }
        return $palettes;
    }

    /** @param mixed $value @return array<string,string> */
    private function normaliseComponents(mixed $value): array
    {
        $input = is_array($value) ? $value : [];
        $result = [];
        foreach (self::DEFAULT_COMPONENTS as $name => $default) {
            $result[$name] = trim((string) ($input[$name] ?? $default)) ?: $default;
        }
        return $result;
    }

    /** @param array<string,string> $components @return list<string> */
    private function validateThemeComponents(array $components): array
    {
        $required = [
            'field' => ['{{wrapper_attributes}}', '{{label_html}}', '{{control_html}}', '{{error_html}}'],
            'textarea' => ['{{wrapper_attributes}}', '{{label_html}}', '{{control_html}}', '{{error_html}}'],
            'select' => ['{{wrapper_attributes}}', '{{label_html}}', '{{control_html}}', '{{error_html}}'],
            'checkbox' => ['{{wrapper_attributes}}', '{{label_html}}', '{{control_html}}', '{{error_html}}'],
            'button' => ['{{wrapper_attributes}}', '{{control_html}}'],
            'form' => ['{{wrapper_attributes}}', '{{children_html}}'],
        ];
        $errors = [];
        foreach ($required as $component => $tokens) {
            foreach ($tokens as $token) {
                if (!str_contains((string) ($components[$component] ?? ''), $token)) {
                    $errors[] = ucfirst($component) . " template must contain {$token}.";
                }
            }
        }
        return $errors;
    }

    /** @param mixed $value @return list<string> */
    private function normaliseTags(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/[,\n]+/', $value) ?: [];
        }
        $tags = [];
        foreach ((array) $value as $tag) {
            $tag = trim((string) $tag);
            if ($tag !== '') {
                $tags[strtolower($tag)] = $tag;
            }
        }
        return array_values($tags);
    }

    /** @param array<string,string> $files */
    private function writePackage(string $root, string $id, array $files): void
    {
        $this->ensureDirectory($root);
        $temporary = rtrim($root, '/\\') . DIRECTORY_SEPARATOR . '.tmp-' . bin2hex(random_bytes(8));
        $this->ensureDirectory($temporary);
        try {
            foreach ($files as $path => $contents) {
                $clean = $this->safeRelativePath($path);
                $target = $temporary . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $clean);
                $this->ensureDirectory(dirname($target));
                if (file_put_contents($target, $contents, LOCK_EX) === false) {
                    throw new RuntimeException('Unable to write marketplace package file ' . $clean . '.');
                }
            }
            $target = rtrim($root, '/\\') . DIRECTORY_SEPARATOR . $id;
            if (is_dir($target)) {
                $this->deleteDirectory($target);
            }
            if (!rename($temporary, $target)) {
                throw new RuntimeException('Unable to publish the custom marketplace package.');
            }
        } finally {
            if (is_dir($temporary)) {
                $this->deleteDirectory($temporary);
            }
        }
    }

    private function deleteCustomPackage(string $root, string $id, string $label): void
    {
        $id = $this->slug($id);
        $path = rtrim($root, '/\\') . DIRECTORY_SEPARATOR . $id;
        if ($id === '' || !is_dir($path)) {
            throw new RuntimeException('The custom ' . $label . ' was not found. Built-in packages cannot be deleted.');
        }
        $this->deleteDirectory($path);
    }

    private function exportCustomPackage(string $root, string $id, string $label): string
    {
        $id = $this->slug($id);
        $directory = rtrim($root, '/\\') . DIRECTORY_SEPARATOR . $id;
        if ($id === '' || !is_dir($directory)) {
            throw new RuntimeException('Only custom ' . $label . ' packages can be exported from the Asset Studio.');
        }
        return ZipWriter::create($this->directoryFiles($directory), $id);
    }

    /** @return array<string,string> */
    private function directoryFiles(string $directory): array
    {
        $result = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $item) {
            if (!$item->isFile() || $item->isLink()) {
                continue;
            }
            $relative = substr($item->getPathname(), strlen(rtrim($directory, '/\\')) + 1);
            $result[str_replace(DIRECTORY_SEPARATOR, '/', $relative)] = (string) file_get_contents($item->getPathname());
        }
        return $result;
    }

    private function builtInThemeExists(string $id): bool
    {
        foreach ((new FormThemeRegistry($this->builtInThemeRoot))->publicDefinitions() as $definition) {
            if ((string) ($definition['id'] ?? '') === $id) {
                return true;
            }
        }
        return false;
    }

    private function builtInSnippetExists(string $id): bool
    {
        foreach ((new HtmlSnippetRegistry($this->builtInSnippetRoot))->publicDefinitions() as $definition) {
            if ((string) ($definition['id'] ?? '') === $id) {
                return true;
            }
        }
        return false;
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0775, true) && !is_dir($path)) {
            throw new RuntimeException('Unable to create directory: ' . $path);
        }
        if (!is_writable($path)) {
            throw new RuntimeException('Directory is not writable: ' . $path);
        }
    }

    private function deleteDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            $path = $item->getPathname();
            if ($item->isLink() || $item->isFile()) {
                @unlink($path);
            } else {
                @rmdir($path);
            }
        }
        @rmdir($directory);
        if (is_dir($directory)) {
            throw new RuntimeException('Unable to remove custom marketplace package ' . basename($directory) . '.');
        }
    }

    private function safeRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                throw new RuntimeException('Unsafe package file path.');
            }
            $parts[] = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $part) ?? '';
        }
        $clean = implode('/', array_filter($parts));
        if ($clean === '') {
            throw new RuntimeException('Marketplace package file path is empty.');
        }
        return $clean;
    }

    private function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }

    private function tokenName(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]+/', '_', $value) ?? '';
        return trim(str_replace('-', '_', $value), '_');
    }

    /** @param array<string,mixed> $value */
    private function json(array $value): string
    {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n";
    }

    /** @param array<string,mixed> $theme */
    private function themeReadme(array $theme): string
    {
        return '# ' . $theme['name'] . "\n\n"
            . ($theme['description'] !== '' ? $theme['description'] . "\n\n" : '')
            . "Thunder Visual IDE form-theme package.\n\n"
            . "- ID: `{$theme['id']}`\n"
            . "- Version: `{$theme['version']}`\n"
            . "- Author: {$theme['author']}\n"
            . "- License: {$theme['license']}\n\n"
            . "Install by importing this ZIP through **Marketplace → Asset Studio**.\n";
    }

    /** @param array<string,mixed> $snippet */
    private function snippetReadme(array $snippet): string
    {
        return '# ' . $snippet['name'] . "\n\n"
            . ($snippet['description'] !== '' ? $snippet['description'] . "\n\n" : '')
            . "Thunder Visual IDE HTML-snippet package.\n\n"
            . "- ID: `{$snippet['id']}`\n"
            . "- Version: `{$snippet['version']}`\n"
            . "- Author: {$snippet['author']}\n"
            . "- License: {$snippet['license']}\n\n"
            . "Install by importing this ZIP through **Marketplace → Asset Studio**.\n";
    }
}
