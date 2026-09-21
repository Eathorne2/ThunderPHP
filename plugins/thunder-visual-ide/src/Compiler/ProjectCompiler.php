<?php

declare(strict_types=1);

namespace ThunderVisualIde\Compiler;

use JsonException;
use ThunderVisualIde\Node\NodeRegistry;
use ThunderVisualIde\FormTheme\FormThemeRegistry;

final class ProjectCompiler
{
    private readonly FormThemeRegistry $formThemes;

    public function __construct(
        private readonly NodeRegistry $registry,
        ?FormThemeRegistry $formThemes = null
    ) {
        $this->formThemes = $formThemes ?? new FormThemeRegistry(
            dirname(__DIR__, 2) . '/form-themes',
            dirname(__DIR__, 2) . '/storage/marketplace/form-themes'
        );
    }

    /**
     * @param array<string,mixed> $project
     * @return array{files:array<string,string>,errors:list<string>,warnings:list<string>,summary:array<string,mixed>}
     */
    public function compile(array $project): array
    {
        $errors = [];
        $warnings = [];
        if ((int)($project['schema_version'] ?? 0) !== 4) {
            $errors[] = 'This compiler only accepts schema version 4 projects.';
        }
        if (!is_array($project['graphs']['main'] ?? null)) {
            $errors[] = 'The project does not contain a main architecture graph.';
        }

        $context = new CompileContext($project, $this->registry, $this->formThemes);
        $pluginNode = $context->firstNodeOfType('project.plugin');
        if ($pluginNode === null) {
            $errors[] = 'Add one Plugin node to the main graph.';
        }
        $lookNode = $context->firstNodeOfType('looks.look');
        if ($lookNode === null) {
            $errors[] = 'Add at least one Look node to the main graph.';
        }

        $files = [];
        $routes = [];
        $routeScopes = [];
        $hooks = [];
        $bootstrap = [];
        $functions = [];
        $meta = [];
        $looks = [];
        $permissions = [];
        $loadAllRoutes = false;

        if ($errors === []) {
            foreach ((array)($project['graphs']['main']['nodes'] ?? []) as $node) {
                if (!is_array($node) || ($node['type'] ?? '') === 'visual.folder' || $context->isNodeMuted($node)) {
                    continue;
                }
                try {
                    $result = $context->compileNode((string)$node['id'], 'architecture', null, 'main');
                    if (isset($result['meta']) && is_array($result['meta'])) {
                        $meta = array_merge($meta, $result['meta']);
                    }
                    if (isset($result['routes']) && is_array($result['routes'])) {
                        array_push($routes, ...$result['routes']);
                    }
                    if (isset($result['route_scope']) && is_array($result['route_scope'])) {
                        $routeScopes[] = $result['route_scope'];
                    }
                    if (isset($result['hooks']) && is_array($result['hooks'])) {
                        array_push($hooks, ...$result['hooks']);
                    }
                    if (isset($result['bootstrap']) && is_array($result['bootstrap'])) {
                        array_push($bootstrap, ...$result['bootstrap']);
                    }
                    if (isset($result['functions']) && is_array($result['functions'])) {
                        array_push($functions, ...$result['functions']);
                    }
                    if (isset($result['looks']) && is_array($result['looks'])) {
                        array_push($looks, ...$result['looks']);
                    }
                    if (isset($result['permissions']) && is_array($result['permissions'])) {
                        array_push($permissions, ...$result['permissions']);
                    }
                    if (!empty($result['load_all_routes'])) {
                        $loadAllRoutes = true;
                    }
                    if (isset($result['files']) && is_array($result['files'])) {
                        foreach ($result['files'] as $path => $content) {
                            $files[(string)$path] = (string)$content;
                        }
                    }
                } catch (\Throwable $exception) {
                    $errors[] = sprintf('Node %s failed: %s', (string)($node['data']['title'] ?? $node['type'] ?? $node['id']), $exception->getMessage());
                }
            }
        }

        $errors = array_values(array_unique(array_merge($errors, $context->errors())));
        $warnings = array_values(array_unique(array_merge($warnings, $context->warnings())));

        if ($errors !== []) {
            return [
                'files' => [],
                'errors' => $errors,
                'warnings' => $warnings,
                'summary' => $this->summary($meta, $project, [], $warnings),
            ];
        }

        $meta = $this->normalizeMeta($meta);
        $routes = $this->uniqueRoutes($routes);
        $hooks = array_values(array_unique(array_filter(array_map('trim', $hooks))));
        $bootstrap = array_values(array_unique(array_filter(array_map('trim', $bootstrap))));
        $lookName = (string)($meta['look'] ?? 'main');
        if ($looks === []) {
            $looks[] = [
                'folder' => $lookName,
                'name' => $meta['name'] . ' Look',
                'version' => $meta['version'],
                'plugin' => $meta['id'],
                'plugin_requires' => '^' . $meta['version'],
                'author' => $meta['author'],
                'thumbnail' => '',
                'description' => 'Default look for ' . $meta['name'],
            ];
        }
        foreach ($looks as $look) {
            $folder = trim((string)($look['folder'] ?? 'main'), '/');
            $files['looks/' . $folder . '/look.json'] = json_encode([
                'name' => (string)($look['name'] ?? $meta['name'] . ' Look'),
                'version' => (string)($look['version'] ?? $meta['version']),
                'plugin' => $meta['id'],
                'plugin_requires' => (string)($look['plugin_requires'] ?? '^' . $meta['version']),
                'author' => (string)($look['author'] ?? $meta['author']),
                'thumbnail' => (string)($look['thumbnail'] ?? ''),
                'description' => (string)($look['description'] ?? ''),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
        }

        $permissions = $this->uniquePermissions($permissions);
        $files['config.json'] = $this->compileConfig(
            $meta,
            $routes,
            $permissions,
            $loadAllRoutes,
            $routeScopes
        );
        if ($functions !== []) {
            $files['functions.php'] = $this->compileFunctions($meta, $functions);
        }
        $files['plugin.php'] = $this->compilePlugin($meta, $hooks, $bootstrap, $functions !== []);
        $files['thunder-project.json'] = json_encode($project, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
        $files['README.md'] = $this->compileReadme($meta, $routes);
        ksort($files);

        return [
            'files' => $files,
            'errors' => [],
            'warnings' => $warnings,
            'summary' => $this->summary($meta, $project, $files, $warnings),
        ];
    }

    /** @param array<string,mixed> $meta @return array<string,mixed> */
    private function normalizeMeta(array $meta): array
    {
        $name = trim((string)($meta['name'] ?? 'Generated Plugin')) ?: 'Generated Plugin';
        $id = trim((string)($meta['id'] ?? ''));
        $id = $id !== '' ? $this->slug($id) : $this->slug($name);
        $namespace = $this->cleanNamespace((string)($meta['namespace'] ?? ''));
        if ($namespace === 'GeneratedPlugin' && trim((string)($meta['namespace'] ?? '')) === '') {
            $namespace = $this->studly($id);
        }
        $dependencies = $meta['dependencies'] ?? [];
        if (is_string($dependencies)) {
            try {
                $dependencies = json_decode($dependencies, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $dependencies = [];
            }
        }
        return [
            'name' => $name,
            'id' => $id,
            'namespace' => $namespace,
            'type' => in_array(($meta['type'] ?? ''), ['Foundation', 'Embedded', 'Companion'], true) ? $meta['type'] : 'Companion',
            'version' => trim((string)($meta['version'] ?? '1.0.0')) ?: '1.0.0',
            'core_requires' => trim((string)($meta['core_requires'] ?? '^1.0.0')) ?: '^1.0.0',
            'description' => trim((string)($meta['description'] ?? 'Generated with Thunder Visual IDE')),
            'author' => trim((string)($meta['author'] ?? '')),
            'website' => trim((string)($meta['website'] ?? '')),
            'thumbnail' => trim((string)($meta['thumbnail'] ?? '')),
            'active' => (bool)($meta['active'] ?? true),
            'look' => trim((string)($meta['look'] ?? 'main')) ?: 'main',
            'index' => max(1, (int)($meta['index'] ?? 10)),
            'dependencies' => is_array($dependencies) ? $dependencies : [],
        ];
    }

    /**
     * @param list<array<string,mixed>> $routes
     * @param list<array<string,string>> $permissions
     * @param list<array{list:string,paths:list<string>}> $routeScopes
     */
    private function compileConfig(
        array $meta,
        array $routes,
        array $permissions,
        bool $loadAllRoutes,
        array $routeScopes
    ): string {
        $explicitOn = [];
        $off = [];
        $hasExplicitOn = false;
        foreach ($routeScopes as $scope) {
            $list = (string) ($scope['list'] ?? 'on') === 'off' ? 'off' : 'on';
            $paths = array_values(array_filter(array_map(
                static fn ($path): string => trim((string) $path),
                is_array($scope['paths'] ?? null) ? $scope['paths'] : []
            ), static fn (string $path): bool => $path !== ''));
            if ($list === 'off') {
                array_push($off, ...$paths);
                continue;
            }
            $hasExplicitOn = true;
            array_push($explicitOn, ...$paths);
        }

        if ($hasExplicitOn) {
            $on = array_values(array_unique($explicitOn));
        } else {
            $on = [];
            if (!$loadAllRoutes) {
                foreach ($routes as $route) {
                    $path = trim((string) ($route['pattern'] ?? ''), '/');
                    $segment = explode('/', $path)[0] ?? '';
                    if ($segment !== '' && !str_starts_with($segment, '{')) {
                        $on[] = $segment;
                    }
                }
            }
            if ($loadAllRoutes || $on === []) {
                $on = ['all'];
            }
            $on = array_values(array_unique($on));
        }
        $off = array_values(array_unique($off));

        $config = [
            'name' => $meta['name'],
            'type' => $meta['type'],
            'version' => $meta['version'],
            'core_requires' => $meta['core_requires'],
            'id' => $meta['id'],
            'description' => $meta['description'],
            'author' => $meta['author'],
            'website' => $meta['website'],
            'thumbnail' => $meta['thumbnail'],
            'active' => $meta['active'],
            'look' => $meta['look'],
            'index' => $meta['index'],
            'permissions' => $permissions,
            'routes' => [
                'on' => $on,
                'off' => $off,
                'routes' => $routes,
            ],
            'dependencies' => $meta['dependencies'] === [] ? (object)[] : $meta['dependencies'],
        ];
        return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    /** @param list<string> $hooks @param list<string> $bootstrap */
    private function compilePlugin(array $meta, array $hooks, array $bootstrap, bool $hasFunctions): string
    {
        $namespace = $meta['namespace'];
        $sections = [];
        if ($hasFunctions) {
            $sections[] = "require_once plugin_path('functions.php');";
        }
        $bootstrapCode = array_values(array_filter(array_map('trim', $bootstrap)));
        if ($bootstrapCode !== []) {
            $sections[] = implode("\n", $bootstrapCode);
        }
        $hookCode = array_values(array_filter(array_map('trim', $hooks)));
        if ($hookCode !== []) {
            $sections[] = implode("\n\n", $hookCode);
        }
        $body = implode("\n\n", $sections);
        return "<?php\n\ndeclare(strict_types=1);\n\nnamespace {$namespace};\n\n" . trim($body) . "\n";
    }

    /** @param list<string> $functions */
    private function compileFunctions(array $meta, array $functions): string
    {
        return "<?php\n\ndeclare(strict_types=1);\n\nnamespace {$meta['namespace']};\n\n" . implode("\n\n", $functions) . "\n";
    }


    /** @param list<array<string,mixed>> $permissions @return list<array<string,string>> */
    private function uniquePermissions(array $permissions): array
    {
        $seen = [];
        $result = [];
        foreach ($permissions as $permission) {
            $slug = trim((string) ($permission['slug'] ?? ''));
            if ($slug === '' || isset($seen[strtolower($slug)])) {
                continue;
            }
            $seen[strtolower($slug)] = true;
            $result[] = [
                'name' => trim((string) ($permission['name'] ?? $slug)) ?: $slug,
                'slug' => $slug,
                'group' => trim((string) ($permission['group'] ?? 'General')) ?: 'General',
                'description' => trim((string) ($permission['description'] ?? '')),
            ];
        }
        return $result;
    }

    /** @param list<array<string,mixed>> $routes @return list<array<string,mixed>> */
    private function uniqueRoutes(array $routes): array
    {
        $seen = [];
        $result = [];
        foreach ($routes as $route) {
            $key = strtoupper((string)($route['method'] ?? 'GET')) . '|' . (string)($route['pattern'] ?? '/') . '|' . (string)($route['name'] ?? '');
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $result[] = [
                'method' => strtoupper((string)($route['method'] ?? 'GET')),
                'pattern' => (string)($route['pattern'] ?? '/'),
                'name' => (string)($route['name'] ?? ''),
            ];
        }
        return $result;
    }

    /** @param array<string,string> $files @param list<string> $warnings @return array<string,mixed> */
    private function summary(array $meta, array $project, array $files, array $warnings): array
    {
        $nodeCount = 0;
        foreach ((array)($project['graphs'] ?? []) as $graph) {
            $nodeCount += count((array)($graph['nodes'] ?? []));
        }
        return [
            'plugin_name' => (string)($meta['name'] ?? 'Unknown'),
            'plugin_id' => (string)($meta['id'] ?? 'generated-plugin'),
            'graphs' => count((array)($project['graphs'] ?? [])),
            'nodes' => $nodeCount,
            'files' => count($files),
            'warnings' => count($warnings),
        ];
    }

    /** @param list<array<string,mixed>> $routes */
    private function compileReadme(array $meta, array $routes): string
    {
        $lines = [
            '# ' . $meta['name'],
            '',
            $meta['description'],
            '',
            'Generated by Thunder Visual IDE using editable node definitions.',
            '',
            '## Routes',
            '',
        ];
        foreach ($routes as $route) {
            $lines[] = '- `' . $route['method'] . ' ' . $route['pattern'] . '` — `' . $route['name'] . '`';
        }
        return implode("\n", $lines) . "\n";
    }

    private function cleanNamespace(string $namespace): string
    {
        $parts = array_values(array_filter(array_map(
            static fn (string $part): string => preg_replace('/[^A-Za-z0-9_]/', '', $part) ?: '',
            explode('\\', $namespace)
        ), static fn (string $part): bool => $part !== ''));
        return $parts !== [] ? implode('\\', $parts) : 'GeneratedPlugin';
    }

    private function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;
        return trim($value, '-') ?: 'generated-plugin';
    }

    private function studly(string $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9]+/', ' ', $value) ?? $value;
        return str_replace(' ', '', ucwords(strtolower(trim($value)))) ?: 'GeneratedPlugin';
    }
}
