<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Lifecycle;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ViewCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $viewPath = trim(
            (string) ($data['filename'] ?? 'frontend/page.php'),
            '/'
        );
        $lookFolder = $this->findLookFolder($node, $context);
        $visualOutput = $this->compileVisualGraph($data, $context);
        $javaScriptGraphId = trim((string) ($data['javascript_graph_id'] ?? ''));
        $javaScriptOutput = $this->compileJavaScriptGraph($data, $context);
        $javaScriptBootstrap = $javaScriptGraphId !== ''
            ? trim($context->compileJavaScriptPhpBootstrap($javaScriptGraphId))
            : '';
        $componentDirectory = $this->componentDirectory($viewPath);

        $files = $this->compileComponentFiles(
            $visualOutput,
            $lookFolder,
            $componentDirectory,
            $context
        );

        $htmlComponentCssPath = '';
        $htmlComponentCss = trim((string) ($visualOutput['component_css'] ?? ''));
        if ($htmlComponentCss !== '') {
            $htmlComponentCssPath = $this->htmlComponentCssAssetPath(
                $viewPath,
                $node
            );
            $files["looks/{$lookFolder}/{$htmlComponentCssPath}"] =
                "/* Generated HTML Component styles for this View. */\n"
                . $htmlComponentCss
                . "\n";
        }

        if ($this->viewUsesBundledChartJs($data, $context)) {
            $chartJsPath = dirname(__DIR__, 3) . '/assets/js/chart.min.js';
            if (!is_file($chartJsPath) || !is_readable($chartJsPath)) {
                $context->addError('Bundled Chart.js asset is missing from assets/js/chart.min.js.');
            } else {
                $chartJs = file_get_contents($chartJsPath);
                if ($chartJs === false || trim($chartJs) === '') {
                    $context->addError('Bundled Chart.js asset assets/js/chart.min.js is empty or unreadable.');
                } else {
                    $files['assets/js/chart.min.js'] = $chartJs;
                }
            }
        }

        $viewSource = (string) ($data['content'] ?? '');
        $textAssets = $this->textAssetsForView(
            $node,
            $context,
            $lookFolder
        );
        $viewSource = $this->placeTextAssets(
            $viewSource,
            $textAssets
        );
        $usesVisualNodes = !empty($visualOutput['uses_visual_nodes']);
        $requiresBaseCssAsset = $usesVisualNodes
            || !empty($visualOutput['requires_base_css_asset']);
        $documentSource = trim((string) ($visualOutput['document'] ?? ''));
        $deferStylesToDocument = $documentSource !== '';
        $viewSource = $this->placeVisualComponents(
            $viewSource,
            $visualOutput,
            $componentDirectory,
            $deferStylesToDocument ? false : $usesVisualNodes,
            $deferStylesToDocument ? '' : $htmlComponentCssPath,
            $deferStylesToDocument ? $documentSource : ''
        );

        $javaScriptMarkup = '';
        if ($javaScriptOutput !== '') {
            $javaScriptPath = $this->javaScriptAssetPath($viewPath, $node);
            $files["looks/{$lookFolder}/{$javaScriptPath}"] = $javaScriptOutput;
            if ($javaScriptBootstrap !== '') {
                $javaScriptMarkup .= $javaScriptBootstrap . "\n";
            }
            $javaScriptMarkup .= "<script src=\"<?= current_look_http("
                . var_export($javaScriptPath, true)
                . " ) ?>\" defer></script>";
        }

        if ($documentSource !== '') {
            $viewSource = $this->placeDocumentContent(
                $documentSource,
                $viewSource
            );

            /*
             * Boilerplate-connected HTML components are already part of the
             * document shell. Resolve any component markers they contain only
             * after the document and normal View source have been combined.
             * The first placement pass has already handled fallback output, so
             * this pass must never append unused components a second time.
             */
            $documentComponents = $visualOutput;
            $documentComponents['inline'] = '';
            $viewSource = $this->placeVisualComponents(
                $viewSource,
                $documentComponents,
                $componentDirectory,
                false,
                '',
                '',
                false
            );

            $viewSource = $this->placeDocumentStyles(
                $viewSource,
                $this->visualStylesheetLinks(
                    $requiresBaseCssAsset,
                    $htmlComponentCssPath
                )
            );
            if ($javaScriptMarkup !== '') {
                $viewSource = $this->placeDocumentScripts(
                    $viewSource,
                    $javaScriptMarkup
                );
            }
        } elseif ($javaScriptMarkup !== '') {
            $viewSource = rtrim($viewSource)
                . "\n"
                . $javaScriptMarkup
                . "\n";
        }

        if ($requiresBaseCssAsset) {
            $files[
                "looks/{$lookFolder}/assets/css/thunder-view-components.css"
            ] = ViewNodeSupport::baseCss();
        }

        $files["looks/{$lookFolder}/{$viewPath}"] = $this->compileTemplate(
            $viewSource
        );

        $routeNodes = $context->connectedNodesOfType(
            (string) $node['id'],
            'routing.route',
            'main'
        );

        return [
            'files' => $files,
            'hooks' => $this->compileRouteHooks(
                $node,
                $data,
                $viewPath,
                $context
            ),
            'load_all_routes' => $routeNodes === [],
        ];
    }

    private function findLookFolder(
        array $node,
        CompileContext $context
    ): string {
        $lookNodes = $context->connectedNodesOfType(
            (string) $node['id'],
            'looks.look',
            'main'
        );

        $folder = trim(
            (string) ($lookNodes[0]['data']['folder'] ?? 'main'),
            '/'
        );

        return $folder !== '' ? $folder : 'main';
    }

    /**
     * @param array<string, mixed> $data
     * @return array{inline:string,components:array<string,string>,document:string,component_css:string,uses_visual_nodes:bool,requires_base_css_asset:bool}
     */
    private function compileVisualGraph(
        array $data,
        CompileContext $context
    ): array {
        $graphId = trim((string) ($data['graph_id'] ?? ''));
        if ($graphId === '') {
            return [
                'inline' => '',
                'components' => [],
                'document' => '',
                'component_css' => '',
                'uses_visual_nodes' => false,
                'requires_base_css_asset' => false,
            ];
        }

        return $context->compileViewGraph($graphId);
    }

    /** @param array<string,mixed> $data */
    private function compileJavaScriptGraph(
        array $data,
        CompileContext $context
    ): string {
        $graphId = trim((string) ($data['javascript_graph_id'] ?? ''));
        return $graphId !== '' ? trim($context->compileJavaScriptGraph($graphId)) : '';
    }

    /** @param array<string,mixed> $node */
    private function javaScriptAssetPath(string $viewPath, array $node): string
    {
        $filename = pathinfo($viewPath, PATHINFO_FILENAME);
        $nodeId = preg_replace(
            '/[^A-Za-z0-9_-]+/',
            '-',
            (string) ($node['id'] ?? 'view')
        ) ?: 'view';
        $slug = ViewNodeSupport::componentName(
            $filename . '-' . substr($nodeId, -8)
        ) ?: 'view';
        return 'assets/js/thunder-view-' . $slug . '.js';
    }

    /** @param array<string,mixed> $node */
    private function htmlComponentCssAssetPath(
        string $viewPath,
        array $node
    ): string {
        $filename = pathinfo($viewPath, PATHINFO_FILENAME);
        $nodeId = preg_replace(
            '/[^A-Za-z0-9_-]+/',
            '-',
            (string) ($node['id'] ?? 'view')
        ) ?: 'view';
        $slug = ViewNodeSupport::componentName(
            $filename . '-' . substr($nodeId, -8)
        ) ?: 'view';

        return 'assets/css/thunder-html-components-' . $slug . '.css';
    }

    /** @param array<string,mixed> $data */
    private function viewUsesBundledChartJs(
        array $data,
        CompileContext $context
    ): bool {
        $graphId = trim((string) ($data['graph_id'] ?? ''));
        if ($graphId === '') {
            return false;
        }

        foreach ($context->nodesOfType('view.chart', $graphId) as $chartNode) {
            $chartData = is_array($chartNode['data'] ?? null)
                ? $chartNode['data']
                : [];
            if (trim((string) ($chartData['chart_js_source'] ?? '')) === '') {
                return true;
            }
        }

        return false;
    }

    private function componentDirectory(string $viewPath): string
    {
        $viewDirectory = trim(
            str_replace('\\', '/', dirname($viewPath)),
            '.'
        );

        return ($viewDirectory !== '' ? $viewDirectory . '/' : '')
            . 'components';
    }

    /**
     * @return array<string, array{type:string,path:string}>
     */
    private function textAssetsForView(
        array $viewNode,
        CompileContext $context,
        string $lookFolder
    ): array {
        $assets = [];

        foreach ($context->nodesOfType('looks.text_asset', 'main') as $assetNode) {
            $assetData = is_array($assetNode['data'] ?? null)
                ? $assetNode['data']
                : [];
            $assetLooks = $context->connectedNodesOfType(
                (string) ($assetNode['id'] ?? ''),
                'looks.look',
                'main'
            );
            $assetLookFolder = trim(
                (string) ($assetLooks[0]['data']['folder'] ?? 'main'),
                '/'
            );

            if (($assetLookFolder !== '' ? $assetLookFolder : 'main') !== $lookFolder) {
                continue;
            }

            $name = $this->assetName($assetData);
            $path = trim(
                str_replace('\\\\', '/', (string) ($assetData['filename'] ?? '')),
                '/'
            );
            if ($name === '' || $path === '') {
                continue;
            }

            $assets[$name] = [
                'type' => str_contains(
                    strtolower((string) ($assetData['asset_type'] ?? 'CSS')),
                    'java'
                ) ? 'js' : 'css',
                'path' => $path,
            ];
        }

        return $assets;
    }

    /** @param array<string, mixed> $data */
    private function assetName(array $data): string
    {
        $name = trim((string) ($data['asset_name'] ?? ''));
        if ($name === '') {
            $name = trim((string) ($data['title'] ?? 'asset'));
        }

        return strtolower(trim(
            preg_replace('/[^a-zA-Z0-9_-]+/', '-', $name) ?: '',
            '-'
        ));
    }

    /**
     * @param array<string, array{type:string,path:string}> $assets
     */
    private function placeTextAssets(string $viewSource, array $assets): string
    {
        return preg_replace_callback(
            '/<tvi-asset\\s+name=["\\\']([^"\\\']+)["\\\']\\s*\\/?\\s*>/i',
            function (array $matches) use ($assets): string {
                $name = strtolower(trim(
                    preg_replace(
                        '/[^a-zA-Z0-9_-]+/',
                        '-',
                        (string) ($matches[1] ?? '')
                    ) ?: '',
                    '-'
                ));

                if ($name === '' || !isset($assets[$name])) {
                    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                    return "<!-- Unknown Thunder Visual IDE asset: {$safeName} -->";
                }

                $asset = $assets[$name];
                $path = var_export($asset['path'], true);

                if ($asset['type'] === 'js') {
                    return '<script src="<?= current_look_http(' . $path
                        . ') ?>"></script>';
                }

                return '<link rel="stylesheet" href="<?= current_look_http('
                    . $path
                    . ') ?>">';
            },
            $viewSource
        ) ?? $viewSource;
    }

    /**
     * @param array{components:array<string,string>} $visualOutput
     * @return array<string, string>
     */
    private function compileComponentFiles(
        array $visualOutput,
        string $lookFolder,
        string $componentDirectory,
        CompileContext $context
    ): array {
        $files = [];
        $components = $this->normalisedComponents(
            (array) ($visualOutput['components'] ?? [])
        );
        $dependencies = $this->componentDependencies($components);
        $blockedEdges = $this->cyclicComponentEdges(
            $dependencies,
            $context
        );

        foreach ($components as $componentName => $html) {
            $resolvedHtml = $this->resolveNestedComponentMarkers(
                (string) $html,
                $componentName,
                $components,
                $componentDirectory,
                $blockedEdges,
                $context
            );
            $resolvedHtml = $this->guardComponentAssets(
                $componentName,
                $resolvedHtml
            );

            $componentPath = "{$componentDirectory}/{$componentName}.php";
            $files["looks/{$lookFolder}/{$componentPath}"] =
                $this->compileTemplate($resolvedHtml);
        }

        return $files;
    }

    /**
     * @param array<string, mixed> $components
     * @return array<string, string>
     */
    private function normalisedComponents(array $components): array
    {
        $normalised = [];

        foreach ($components as $name => $html) {
            $componentName = ViewNodeSupport::componentName((string) $name);
            if ($componentName !== '') {
                $normalised[$componentName] = (string) $html;
            }
        }

        return $normalised;
    }

    /**
     * @param array<string, string> $components
     * @return array<string, list<string>>
     */
    private function componentDependencies(array $components): array
    {
        $dependencies = [];

        foreach ($components as $name => $html) {
            $dependencies[$name] = [];

            if (!preg_match_all(
                '/<tvi-component\s+name=["\']([^"\']+)["\']\s*\/?\s*>/i',
                $html,
                $matches
            )) {
                continue;
            }

            foreach ((array) ($matches[1] ?? []) as $dependency) {
                $dependencyName = ViewNodeSupport::componentName(
                    (string) $dependency
                );
                if (
                    $dependencyName !== ''
                    && !in_array(
                        $dependencyName,
                        $dependencies[$name],
                        true
                    )
                ) {
                    $dependencies[$name][] = $dependencyName;
                }
            }
        }

        return $dependencies;
    }

    /**
     * @param array<string, list<string>> $dependencies
     * @return array<string, bool>
     */
    private function cyclicComponentEdges(
        array $dependencies,
        CompileContext $context
    ): array {
        $blocked = [];
        $reported = [];

        foreach ($dependencies as $parent => $children) {
            foreach ($children as $child) {
                if (!array_key_exists($child, $dependencies)) {
                    continue;
                }

                $path = $this->dependencyPath(
                    $child,
                    $parent,
                    $dependencies,
                    [],
                    0
                );
                if ($path === null) {
                    continue;
                }

                $edgeKey = $parent . '|' . $child;
                $blocked[$edgeKey] = true;
                $cycle = array_merge([$parent], $path);
                $cycleLabel = implode(' → ', $cycle);
                $cycleNames = array_values(array_unique($cycle));
                sort($cycleNames);
                $reportKey = implode('|', $cycleNames);
                if (!isset($reported[$reportKey])) {
                    $reported[$reportKey] = true;
                    $context->addError(
                        'Circular visual component dependency detected: '
                        . $cycleLabel
                        . '. The cyclic include was removed.'
                    );
                }
            }
        }

        return $blocked;
    }

    /**
     * @param array<string, list<string>> $dependencies
     * @param array<string, bool> $visited
     * @return list<string>|null
     */
    private function dependencyPath(
        string $current,
        string $target,
        array $dependencies,
        array $visited,
        int $depth
    ): ?array {
        if ($depth > 20) {
            return null;
        }
        if ($current === $target) {
            return [$current];
        }
        if (isset($visited[$current])) {
            return null;
        }

        $visited[$current] = true;
        foreach ($dependencies[$current] ?? [] as $child) {
            $path = $this->dependencyPath(
                $child,
                $target,
                $dependencies,
                $visited,
                $depth + 1
            );
            if ($path !== null) {
                return array_merge([$current], $path);
            }
        }

        return null;
    }

    /**
     * @param array<string, string> $components
     * @param array<string, bool> $blockedEdges
     */
    private function resolveNestedComponentMarkers(
        string $html,
        string $ownerName,
        array $components,
        string $componentDirectory,
        array $blockedEdges,
        CompileContext $context
    ): string {
        return preg_replace_callback(
            '/<tvi-component\s+name=["\']([^"\']+)["\']\s*\/?\s*>/i',
            function (array $matches) use (
                $ownerName,
                $components,
                $componentDirectory,
                $blockedEdges,
                $context
            ): string {
                $componentName = ViewNodeSupport::componentName(
                    (string) ($matches[1] ?? '')
                );
                $safeName = htmlspecialchars(
                    $componentName,
                    ENT_QUOTES,
                    'UTF-8'
                );

                if (
                    $componentName === ''
                    || !array_key_exists($componentName, $components)
                ) {
                    $context->addWarning(
                        "Visual component {$ownerName} references missing component {$componentName}."
                    );
                    return "<!-- Unknown Thunder Visual IDE component: {$safeName} -->";
                }

                if (isset($blockedEdges[$ownerName . '|' . $componentName])) {
                    return "<!-- Circular Thunder Visual IDE component reference blocked: {$safeName} -->";
                }

                $path = "{$componentDirectory}/{$componentName}.php";

                return '<?php require current_look('
                    . var_export($path, true)
                    . '); ?>';
            },
            $html
        ) ?? $html;
    }

    private function guardComponentAssets(
        string $componentName,
        string $html
    ): string {
        $assets = ViewNodeSupport::extractLoopAssets($html);
        $body = trim((string) ($assets['html'] ?? ''));
        $styles = implode("\n", (array) ($assets['styles'] ?? []));
        $scripts = implode("\n", (array) ($assets['scripts'] ?? []));
        $key = var_export($componentName, true);
        $parts = [];

        if ($styles !== '') {
            $parts[] = <<<PHP
<?php
\$__tvi_component_styles = is_array(\$__tvi_component_styles ?? null)
    ? \$__tvi_component_styles
    : [];
if (empty(\$__tvi_component_styles[{$key}])):
    \$__tvi_component_styles[{$key}] = true;
?>
{$styles}
<?php endif; ?>
PHP;
        }

        if ($body !== '') {
            $parts[] = $body;
        }

        if ($scripts !== '') {
            $parts[] = <<<PHP
<?php
\$__tvi_component_scripts = is_array(\$__tvi_component_scripts ?? null)
    ? \$__tvi_component_scripts
    : [];
if (empty(\$__tvi_component_scripts[{$key}])):
    \$__tvi_component_scripts[{$key}] = true;
?>
{$scripts}
<?php endif; ?>
PHP;
        }

        return implode("\n", $parts);
    }

    /**
     * @param array{inline:string,components:array<string,string>} $visualOutput
     */
    private function placeVisualComponents(
        string $viewSource,
        array $visualOutput,
        string $componentDirectory,
        bool $loadBaseCss,
        string $htmlComponentCssPath = '',
        string $additionalMarkerSource = '',
        bool $appendUnusedComponents = true
    ): string {
        $usedComponents = [];
        $styleLinks = [];
        if ($loadBaseCss) {
            $styleLinks[] = $this->componentBaseCssLink();
        }
        if ($htmlComponentCssPath !== '') {
            $styleLinks[] = $this->componentStylesheetLink(
                $htmlComponentCssPath
            );
        }
        $baseCssLink = implode("\n", $styleLinks);
        $baseCssPending = $baseCssLink !== '';
        $components = $this->normalisedComponents(
            (array) ($visualOutput['components'] ?? [])
        );
        $dependencies = $this->componentDependencies($components);
        $nestedComponents = [];
        foreach ($dependencies as $children) {
            foreach ($children as $child) {
                if (isset($components[$child])) {
                    $nestedComponents[$child] = true;
                }
            }
        }

        /*
         * A document shell may already contain compiled HTML children whose
         * source includes <tvi-component> markers. Count those references
         * before deciding which named root components require fallback output.
         */
        foreach ($this->componentMarkerNames($additionalMarkerSource) as $name) {
            if (!isset($components[$name])) {
                continue;
            }

            $usedComponents[$name] = true;
            foreach ($this->componentDependencyClosure($name, $dependencies) as $dependency) {
                $usedComponents[$dependency] = true;
            }
        }

        $viewSource = preg_replace_callback(
            '/(?P<indent>^[ \t]*)?<tvi-component\s+name=["\'](?P<name>[^"\']+)["\']\s*\/?\s*>/im',
            function (array $matches) use (
                &$usedComponents,
                &$baseCssPending,
                $baseCssLink,
                $components,
                $dependencies,
                $componentDirectory
            ): string {
                $componentName = ViewNodeSupport::componentName(
                    (string) ($matches['name'] ?? '')
                );

                if (
                    $componentName === ''
                    || !array_key_exists(
                        $componentName,
                        $components
                    )
                ) {
                    $safeName = htmlspecialchars(
                        $componentName,
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    return $this->indentComponentPlacement(
                        "<!-- Unknown Thunder Visual IDE component: {$safeName} -->",
                        (string) ($matches['indent'] ?? '')
                    );
                }

                $usedComponents[$componentName] = true;
                foreach (
                    $this->componentDependencyClosure(
                        $componentName,
                        $dependencies
                    ) as $dependency
                ) {
                    $usedComponents[$dependency] = true;
                }
                $path = "{$componentDirectory}/{$componentName}.php";

                $include = '<?php require current_look('
                    . var_export($path, true)
                    . '); ?>';

                return $this->indentComponentPlacement(
                    $this->prefixBaseCssLink(
                        $include,
                        $baseCssPending,
                        $baseCssLink
                    ),
                    (string) ($matches['indent'] ?? '')
                );
            },
            $viewSource
        ) ?? $viewSource;

        $append = [];
        $inlineHtml = trim((string) ($visualOutput['inline'] ?? ''));
        if ($inlineHtml !== '') {
            $append[] = $this->prefixBaseCssLink(
                $inlineHtml,
                $baseCssPending,
                $baseCssLink
            );
        }

        if ($appendUnusedComponents) {
            foreach (array_keys($components) as $name) {
                $componentName = ViewNodeSupport::componentName((string) $name);
                if (
                    $componentName === ''
                    || isset($usedComponents[$componentName])
                    || isset($nestedComponents[$componentName])
                ) {
                    continue;
                }

                $path = "{$componentDirectory}/{$componentName}.php";
                $include = '<?php require current_look('
                    . var_export($path, true)
                    . '); ?>';
                $append[] = $this->prefixBaseCssLink(
                    $include,
                    $baseCssPending,
                    $baseCssLink
                );
            }
        }

        if ($append !== []) {
            $viewSource = rtrim($viewSource)
                . "\n\n"
                . implode("\n\n", $append)
                . "\n";
        }

        return $viewSource;
    }

    /** @return list<string> */
    private function componentMarkerNames(string $source): array
    {
        if (trim($source) === '' || !preg_match_all(
            '/<tvi-component\s+name=["\']([^"\']+)["\']\s*\/?\s*>/i',
            $source,
            $matches
        )) {
            return [];
        }

        $names = [];
        foreach ((array) ($matches[1] ?? []) as $rawName) {
            $name = ViewNodeSupport::componentName((string) $rawName);
            if ($name !== '') {
                $names[$name] = true;
            }
        }

        return array_keys($names);
    }

    private function visualStylesheetLinks(
        bool $loadBaseCss,
        string $htmlComponentCssPath
    ): string {
        $styleLinks = [];

        if ($loadBaseCss) {
            $styleLinks[] = $this->componentBaseCssLink();
        }

        if ($htmlComponentCssPath !== '') {
            $styleLinks[] = $this->componentStylesheetLink(
                $htmlComponentCssPath
            );
        }

        return implode("\n", $styleLinks);
    }

    private function placeDocumentStyles(
        string $document,
        string $styles
    ): string {
        $pattern = '/(?P<indent>^[ \t]*)?<tvi-view-styles\s*\/?>/im';

        if (preg_match($pattern, $document) === 1) {
            return preg_replace_callback(
                $pattern,
                function (array $matches) use ($styles): string {
                    if (trim($styles) === '') {
                        return '';
                    }

                    return $this->indentComponentPlacement(
                        trim($styles),
                        (string) ($matches['indent'] ?? '')
                    );
                },
                $document,
                1
            ) ?? $document;
        }

        if (trim($styles) === '') {
            return $document;
        }

        $headPattern = '/(?P<indent>^[ \t]*)<\/head\s*>/im';
        if (preg_match($headPattern, $document) === 1) {
            return preg_replace_callback(
                $headPattern,
                function (array $matches) use ($styles): string {
                    $indent = (string) ($matches['indent'] ?? '');

                    return $this->indentComponentPlacement(
                        trim($styles),
                        $indent . '    '
                    ) . "\n" . $indent . '</head>';
                },
                $document,
                1
            ) ?? $document;
        }

        return trim($styles) . "\n" . $document;
    }

    private function placeDocumentScripts(
        string $document,
        string $scripts
    ): string {
        if (trim($scripts) === '') {
            return $document;
        }

        $pattern = '/(?P<indent>^[ \t]*)<\/body\s*>/im';
        if (preg_match($pattern, $document) !== 1) {
            return rtrim($document) . "\n" . trim($scripts) . "\n";
        }

        return preg_replace_callback(
            $pattern,
            function (array $matches) use ($scripts): string {
                $indent = (string) ($matches['indent'] ?? '');
                return $this->indentComponentPlacement(
                    trim($scripts),
                    $indent . '    '
                ) . "\n" . $indent . '</body>';
            },
            $document,
            1
        ) ?? $document;
    }

    private function placeDocumentContent(
        string $document,
        string $viewSource
    ): string {
        $pattern = '/(?P<indent>^[ \t]*)?<tvi-view-content\s*\/?>/im';
        if (preg_match($pattern, $document) !== 1) {
            if (preg_match(
                "/do_action\s*\(\s*['\"]html_content['\"]\s*\)/i",
                $document
            ) === 1) {
                return $document;
            }
            return rtrim($document) . "\n" . trim($viewSource) . "\n";
        }

        return preg_replace_callback(
            $pattern,
            function (array $matches) use ($viewSource): string {
                return $this->indentComponentPlacement(
                    trim($viewSource),
                    (string) ($matches['indent'] ?? '')
                );
            },
            $document,
            1
        ) ?? $document;
    }

    private function indentComponentPlacement(
        string $placement,
        string $indent
    ): string {
        if ($indent === '' || $placement === '') {
            return $placement;
        }

        return $indent . str_replace("\n", "\n" . $indent, $placement);
    }

    private function componentBaseCssLink(): string
    {
        return $this->componentStylesheetLink(
            'assets/css/thunder-view-components.css'
        );
    }

    private function componentStylesheetLink(string $path): string
    {
        $exportedPath = var_export($path, true);

        return <<<PHP
<?php
\$__tvi_component_css_href = current_look_http(
    {$exportedPath}
);
if (empty(\$GLOBALS['__tvi_loaded_component_css'][\$__tvi_component_css_href])):
    \$GLOBALS['__tvi_loaded_component_css'][\$__tvi_component_css_href] = true;
?>
<link rel="stylesheet" href="<?= esc(\$__tvi_component_css_href) ?>">
<?php
endif;
unset(\$__tvi_component_css_href);
?>
PHP;
    }

    private function prefixBaseCssLink(
        string $html,
        bool &$baseCssPending,
        string $baseCssLink
    ): string {
        if (!$baseCssPending || trim($html) === '') {
            return $html;
        }

        $baseCssPending = false;

        return $baseCssLink . "\n" . $html;
    }

    /**
     * @param array<string, list<string>> $dependencies
     * @return list<string>
     */
    private function componentDependencyClosure(
        string $componentName,
        array $dependencies,
        array $visited = [],
        int $depth = 0
    ): array {
        if ($depth > 20 || isset($visited[$componentName])) {
            return [];
        }

        $visited[$componentName] = true;
        $result = [];
        foreach ($dependencies[$componentName] ?? [] as $child) {
            if (isset($visited[$child])) {
                continue;
            }
            $result[$child] = true;
            foreach ($this->componentDependencyClosure(
                $child,
                $dependencies,
                $visited,
                $depth + 1
            ) as $nested) {
                $result[$nested] = true;
            }
        }

        return array_keys($result);
    }

    /**
     * @param array<string, mixed> $data
     * @return list<string>
     */
    private function compileRouteHooks(
        array $node,
        array $data,
        string $viewPath,
        CompileContext $context
    ): array {
        $hooks = [];
        $routeNodes = $context->connectedNodesOfType(
            (string) $node['id'],
            'routing.route',
            'main'
        );

        $hookName = trim((string) ($data['hook_name'] ?? 'view'));
        $fallbackPriority = (int) ($data['priority'] ?? 10);
        $routePriorities = is_array($data['route_priorities'] ?? null)
            ? $data['route_priorities']
            : [];

        foreach ($routeNodes as $routeNode) {
            $routeName = trim((string) ($routeNode['data']['route_name'] ?? ''));
            if ($routeName === '') {
                continue;
            }

            $priority = (int) (
                $routePriorities[(string) ($routeNode['id'] ?? '')]
                ?? $fallbackPriority
            );

            $hooks[] = $this->buildViewHook(
                $context,
                $hookName,
                $routeName,
                $priority,
                $viewPath
            );
        }

        if ($hooks === []) {
            $hooks[] = $this->buildViewHook(
                $context,
                $hookName,
                null,
                $fallbackPriority,
                $viewPath
            );
        }

        return $hooks;
    }

    private function buildViewHook(
        CompileContext $context,
        string $hookName,
        ?string $routeName,
        int $priority,
        string $viewPath
    ): string {
        $template = <<<'PHP'
add_action(%s, function ($data = []) {
    $__view_data = get_value();

    if (is_array($__view_data)) {
        extract($__view_data, EXTR_SKIP);
    }

    require current_look(%s);
}, %d%s);
PHP;

        $routeArgument = $routeName !== null
            ? ', ' . $context->export($routeName)
            : '';

        return sprintf(
            $template,
            $context->export($hookName),
            $context->export($viewPath),
            $priority,
            $routeArgument
        );
    }

    private function compileTemplate(string $content): string
    {
        $pathPattern = '[A-Za-z_][A-Za-z0-9_]*'
            . '(?:\.[A-Za-z_][A-Za-z0-9_]*)*';
        $constantPattern = '\\\\?[A-Za-z_][A-Za-z0-9_\\\\]*';
        $valuePattern = '(?:const\s*:\s*' . $constantPattern
            . '|' . $pathPattern . ')';

        $content = preg_replace_callback(
            '/\{!!\s*(' . $valuePattern . ')\s*!!\}/',
            function (array $matches): string {
                $expression = $this->templateValueExpression(
                    (string) $matches[1]
                );

                return "<?= ({$expression}) ?? '' ?>";
            },
            $content
        ) ?? $content;

        $content = preg_replace_callback(
            '/\{\{\s*(' . $valuePattern . ')\s*\}\}/',
            function (array $matches): string {
                $expression = $this->templateValueExpression(
                    (string) $matches[1]
                );

                return "<?= esc(({$expression}) ?? '') ?>";
            },
            $content
        ) ?? $content;

        $content = preg_replace_callback(
            '/\{%\s*if\s+(' . $valuePattern . ')\s*%\}/',
            function (array $matches): string {
                $expression = $this->templateValueExpression(
                    (string) $matches[1]
                );

                return "<?php if (!empty({$expression})): ?>";
            },
            $content
        ) ?? $content;

        $content = preg_replace(
            '/\{%\s*else\s*%\}/',
            '<?php else: ?>',
            $content
        ) ?? $content;

        $content = preg_replace(
            '/\{%\s*endif\s*%\}/',
            '<?php endif; ?>',
            $content
        ) ?? $content;

        $content = preg_replace_callback(
            '/\{%\s*foreach\s+(' . $valuePattern . ')\s+as\s+'
                . '([A-Za-z_][A-Za-z0-9_]*)\s*%\}/',
            function (array $matches): string {
                $collectionExpression = $this->templateValueExpression(
                    (string) $matches[1]
                );
                $itemName = (string) $matches[2];

                return '<?php foreach ((array)(('
                    . $collectionExpression
                    . ") ?? []) as \${$itemName}): ?>";
            },
            $content
        ) ?? $content;

        return preg_replace(
            '/\{%\s*endforeach\s*%\}/',
            '<?php endforeach; ?>',
            $content
        ) ?? $content;
    }

    private function templateValueExpression(string $token): string
    {
        $token = trim($token);

        if (preg_match(
            '/^const\s*:\s*(\\\\?[A-Za-z_][A-Za-z0-9_\\\\]*)$/',
            $token,
            $matches
        ) === 1) {
            $constant = ltrim((string) $matches[1], '\\');
            $exported = var_export($constant, true);

            return "(\\defined({$exported}) ? \\constant({$exported}) : null)";
        }

        $path = var_export($token, true);
        $variableExpression = '\\get_nested_value(get_defined_vars(), '
            . $path
            . ')';

        /*
         * A bare all-uppercase identifier is treated as a PHP constant when
         * it is defined. If it is not defined, it still falls back to the
         * normal View variable lookup for compatibility.
         */
        if (preg_match('/^[A-Z_][A-Z0-9_]*$/', $token) === 1) {
            return '(\\defined(' . $path . ')'
                . ' ? \\constant(' . $path . ')'
                . ' : ' . $variableExpression . ')';
        }

        return $variableExpression;
    }
}
