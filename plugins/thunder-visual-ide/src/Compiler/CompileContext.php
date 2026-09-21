<?php

declare(strict_types=1);

namespace ThunderVisualIde\Compiler;

use RuntimeException;
use ThunderVisualIde\Node\NodeRegistry;
use ThunderVisualIde\FormTheme\FormThemeRegistry;

final class CompileContext
{
    /** @var array<string,mixed> */
    private array $project;
    /** @var array<string,array<string,mixed>> */
    private array $graphs;
    /** @var array<string,array<string,array<string,mixed>>> */
    private array $nodeIndexes = [];
    /** @var array<string,array<string,mixed>> */
    private array $expressionCache = [];
    /** @var array<string,array<string,bool>> */
    private array $requirements = [];
    /** @var array<string,list<array{node_id:string,bridge_key:string,js_variable:string,php_expression:string}>> */
    private array $javascriptPhpBridgeCache = [];
    /** @var array<string,bool> */
    private array $viewStack = [];
    /** @var array<string,bool> */
    private array $javascriptStack = [];
    /** @var array<string,array<string,string>> */
    private array $viewCssCollections = [];
    /** @var list<string> */
    private array $errors = [];
    /** @var list<string> */
    private array $warnings = [];

    private string $graphId = 'main';
    private string $mode = 'architecture';
    private ?string $outputPort = null;

    /** @param array<string,mixed> $project */
    public function __construct(
        array $project,
        private readonly NodeRegistry $registry,
        private readonly FormThemeRegistry $formThemes
    ) {
        $this->project = $project;
        $this->graphs = is_array($project['graphs'] ?? null) ? $project['graphs'] : [];
        foreach ($this->graphs as $graphId => $graph) {
            $this->nodeIndexes[(string)$graphId] = [];
            foreach ((array)($graph['nodes'] ?? []) as $node) {
                if (is_array($node) && !empty($node['id'])) {
                    $this->nodeIndexes[(string)$graphId][(string)$node['id']] = $node;
                }
            }
        }
    }

    public function registry(): NodeRegistry
    {
        return $this->registry;
    }

    /** @return array<string,mixed> */
    public function project(): array
    {
        return $this->project;
    }

    public function graphId(): string
    {
        return $this->graphId;
    }

    /** @return array<string,mixed>|null */
    public function graphOwnerNode(?string $graphId = null): ?array
    {
        $graph = $this->graph($graphId);
        $ownerId = trim((string)($graph['owner_node_id'] ?? ''));
        if ($ownerId === '') {
            return null;
        }

        foreach ($this->nodeIndexes as $nodes) {
            if (isset($nodes[$ownerId])) {
                return $nodes[$ownerId];
            }
        }

        return null;
    }


    /** @return array<string,mixed>|null */
    public function lookNodeForView(array $viewNode): ?array
    {
        $connected = $this->connectedNodesOfType(
            (string) ($viewNode['id'] ?? ''),
            'looks.look',
            'main'
        );

        return $connected[0] ?? $this->firstNodeOfType('looks.look');
    }

    /** @return array<string,mixed>|null */
    public function currentLookNode(): ?array
    {
        $owner = $this->graphOwnerNode();
        if (is_array($owner) && in_array((string) ($owner['type'] ?? ''), [
            'lifecycle.view',
            'lifecycle.reusable_view',
        ], true)) {
            return $this->lookNodeForView($owner);
        }

        return $this->firstNodeOfType('looks.look');
    }

    /** @return array<string,mixed> */
    public function formThemeForLook(array $lookNode): array
    {
        $data = is_array($lookNode['data'] ?? null) ? $lookNode['data'] : [];
        $themeId = trim((string) ($data['form_theme_id'] ?? 'classic')) ?: 'classic';
        return $this->formThemes->get($themeId);
    }

    public function formThemeScopeForLook(array $lookNode): string
    {
        $data = is_array($lookNode['data'] ?? null) ? $lookNode['data'] : [];
        $themeId = $this->slug((string) ($data['form_theme_id'] ?? 'classic'));
        $folder = $this->slug((string) ($data['folder'] ?? 'main'));
        return '.thv-form-theme-' . $folder . '-' . $themeId;
    }

    /** @return array<string,string> */
    public function formThemeColorsForLook(array $lookNode): array
    {
        $data = is_array($lookNode['data'] ?? null) ? $lookNode['data'] : [];
        $themeId = trim((string) ($data['form_theme_id'] ?? 'classic')) ?: 'classic';
        $paletteId = trim((string) ($data['form_theme_palette_id'] ?? 'default')) ?: 'default';
        $colors = $this->formThemes->paletteColors($themeId, $paletteId);
        $custom = $data['form_theme_colors'] ?? [];
        if (is_string($custom)) {
            $decoded = json_decode($custom, true);
            $custom = is_array($decoded) ? $decoded : [];
        }
        if (is_array($custom)) {
            foreach ($custom as $name => $value) {
                $name = strtolower(trim((string) $name));
                $value = trim((string) $value);
                if ($name !== '' && $value !== '') {
                    $colors[$name] = $value;
                }
            }
        }
        return $colors;
    }

    public function formThemeCssForLook(array $lookNode): string
    {
        $data = is_array($lookNode['data'] ?? null) ? $lookNode['data'] : [];
        $themeId = trim((string) ($data['form_theme_id'] ?? 'classic')) ?: 'classic';
        return $this->formThemes->compileCss(
            $themeId,
            $this->formThemeScopeForLook($lookNode),
            $this->formThemeColorsForLook($lookNode)
        );
    }

    public function formThemeJsForLook(array $lookNode): string
    {
        $data = is_array($lookNode['data'] ?? null) ? $lookNode['data'] : [];
        $themeId = trim((string) ($data['form_theme_id'] ?? 'classic')) ?: 'classic';
        return $this->formThemes->compileJs(
            $themeId,
            $this->formThemeScopeForLook($lookNode)
        );
    }

    public function currentFormThemeScopeClass(): string
    {
        $look = $this->currentLookNode();
        if (!is_array($look)) {
            return 'thv-form-theme-main-classic';
        }
        return ltrim($this->formThemeScopeForLook($look), '.');
    }

    /** @param array<string,string> $tokens */
    public function renderFormThemeComponent(
        string $component,
        array $tokens,
        string $fallback
    ): string {
        $look = $this->currentLookNode();
        $themeId = is_array($look)
            ? trim((string) ($look['data']['form_theme_id'] ?? 'classic'))
            : 'classic';
        return $this->formThemes->renderComponent(
            $themeId !== '' ? $themeId : 'classic',
            $component,
            $tokens,
            $fallback
        );
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function outputPort(): ?string
    {
        return $this->outputPort;
    }

    /** @return array<string,mixed> */
    public function graph(?string $graphId = null): array
    {
        $id = $graphId ?? $this->graphId;
        return $this->graphs[$id] ?? ['id' => $id, 'nodes' => [], 'edges' => []];
    }

    /** @return array<string,mixed> */
    public function node(string $nodeId, ?string $graphId = null): array
    {
        $id = $graphId ?? $this->graphId;
        if (!isset($this->nodeIndexes[$id][$nodeId])) {
            throw new RuntimeException("Node {$nodeId} was not found in graph {$id}.");
        }
        return $this->nodeIndexes[$id][$nodeId];
    }

    /** @param array<string,mixed>|string $node */
    public function isNodeMuted(array|string $node, ?string $graphId = null): bool
    {
        if (is_string($node)) {
            $id = $graphId ?? $this->graphId;
            $node = $this->nodeIndexes[$id][$node] ?? [];
        }
        return !empty($node['data']['muted']);
    }

    /** @return list<array<string,mixed>> */
    public function nodesOfType(string $type, string $graphId = 'main'): array
    {
        return array_values(array_filter(
            $this->nodeIndexes[$graphId] ?? [],
            fn (array $node): bool => (string)($node['type'] ?? '') === $type && !$this->isNodeMuted($node)
        ));
    }

    /** @return array<string,mixed>|null */
    public function firstNodeOfType(string $type, string $graphId = 'main'): ?array
    {
        foreach ($this->nodeIndexes[$graphId] ?? [] as $node) {
            if (($node['type'] ?? '') === $type && !$this->isNodeMuted($node)) {
                return $node;
            }
        }
        return null;
    }

    /** @return list<array<string,mixed>> */
    public function incomingEdges(string $nodeId, ?string $port = null, ?string $graphId = null): array
    {
        $graph = $this->graph($graphId);
        return array_values(array_filter((array)($graph['edges'] ?? []), static function ($edge) use ($nodeId, $port): bool {
            if (!is_array($edge) || (string)($edge['to'] ?? '') !== $nodeId) {
                return false;
            }
            return $port === null || (string)($edge['to_port'] ?? '') === $port;
        }));
    }

    /** @return list<array<string,mixed>> */
    public function outgoingEdges(string $nodeId, ?string $port = null, ?string $graphId = null): array
    {
        $graph = $this->graph($graphId);
        return array_values(array_filter((array)($graph['edges'] ?? []), static function ($edge) use ($nodeId, $port): bool {
            if (!is_array($edge) || (string)($edge['from'] ?? '') !== $nodeId) {
                return false;
            }
            return $port === null || (string)($edge['from_port'] ?? '') === $port;
        }));
    }

    /** @return list<array<string,mixed>> */
    public function connectedNodes(string $nodeId, string $port, string $direction = 'out', string $graphId = 'main'): array
    {
        $edges = $direction === 'in'
            ? $this->incomingEdges($nodeId, $port, $graphId)
            : $this->outgoingEdges($nodeId, $port, $graphId);
        $nodes = [];
        foreach ($edges as $edge) {
            $otherId = $direction === 'in' ? (string)$edge['from'] : (string)$edge['to'];
            if (isset($this->nodeIndexes[$graphId][$otherId]) && !$this->isNodeMuted($this->nodeIndexes[$graphId][$otherId])) {
                $nodes[] = $this->nodeIndexes[$graphId][$otherId];
            }
        }
        return $nodes;
    }

    /** @return list<array<string,mixed>> */
    public function connectedNodesOfType(string $nodeId, string $type, string $graphId = 'main'): array
    {
        $nodes = [];
        $seen = [];
        foreach ((array)($this->graph($graphId)['edges'] ?? []) as $edge) {
            if (!is_array($edge)) continue;
            $otherId = null;
            if ((string)($edge['from'] ?? '') === $nodeId) $otherId = (string)($edge['to'] ?? '');
            elseif ((string)($edge['to'] ?? '') === $nodeId) $otherId = (string)($edge['from'] ?? '');
            if ($otherId === null || $otherId === '' || isset($seen[$otherId])) continue;
            $other = $this->nodeIndexes[$graphId][$otherId] ?? null;
            if (is_array($other) && (string)($other['type'] ?? '') === $type && !$this->isNodeMuted($other)) {
                $seen[$otherId] = true;
                $nodes[] = $other;
            }
        }
        return $nodes;
    }

    public function inputExpression(string $nodeId, string $inputPort, string $default = 'null'): string
    {
        return $this->inputExpressionInternal($nodeId, $inputPort, $default, []);
    }

    /** @param array<string,bool> $visited */
    private function inputExpressionInternal(string $nodeId, string $inputPort, string $default, array $visited): string
    {
        $visitKey = $this->graphId . '|' . $nodeId . '|' . $inputPort;
        if (isset($visited[$visitKey])) {
            return $default;
        }
        $visited[$visitKey] = true;

        $edge = $this->incomingEdges($nodeId, $inputPort)[0] ?? null;
        if (!is_array($edge)) {
            return $default;
        }
        $sourceId = (string)($edge['from'] ?? '');
        $sourcePort = (string)($edge['from_port'] ?? 'value');
        $sourceNode = $this->nodeIndexes[$this->graphId][$sourceId] ?? null;
        if (is_array($sourceNode) && $this->isNodeMuted($sourceNode)) {
            $passthrough = $this->mutedPassthroughInputPort($sourceNode, $sourcePort);
            if ($passthrough === null) {
                return $default;
            }
            return $this->inputExpressionInternal($sourceId, $passthrough, $default, $visited);
        }

        $result = $this->compileNode($sourceId, 'expression', $sourcePort, $this->graphId);
        if (isset($result['expression'])) {
            return (string)$result['expression'];
        }
        if (isset($result['outputs'][$sourcePort])) {
            return (string)$result['outputs'][$sourcePort];
        }
        return $default;
    }

    /** @param array<string,mixed> $node */
    private function mutedPassthroughInputPort(array $node, string $outputPort): ?string
    {
        $type = (string)($node['type'] ?? '');
        if ($type === 'custom.class_method_call' && $outputPort === 'result') {
            $data = is_array($node['data'] ?? null) ? $node['data'] : [];
            $method = $this->classMethod(
                (string)($data['class_node_id'] ?? ''),
                (string)($data['method_name'] ?? '')
            );
            $returnType = strtolower(trim((string)($method['return_type'] ?? '')));
            $chainable = !empty($method['chainable']) || in_array($returnType, ['self', 'static'], true);
            return $chainable && empty($method['static']) ? 'object' : null;
        }

        $maps = [
            'database.query_builder_method' => ['builder' => 'builder'],
            'session.method' => ['session' => 'session'],
            'request.method' => ['request' => 'request'],
            'images.method' => ['image' => 'image'],
            'models.method_call' => ['model' => 'model'],
            'data.cast' => ['value' => 'value'],
        ];
        if (isset($maps[$type][$outputPort])) {
            return $maps[$type][$outputPort];
        }

        try {
            $definition = $this->registry->definition($type);
        } catch (\Throwable) {
            return null;
        }
        $outputs = (array)($definition['ports']['outputs'] ?? []);
        $inputs = (array)($definition['ports']['inputs'] ?? []);
        $output = null;
        foreach ($outputs as $port) {
            if (is_array($port) && (string)($port['id'] ?? '') === $outputPort) {
                $output = $port;
                break;
            }
        }
        if (!is_array($output) || (string)($output['type'] ?? '') === 'exec') {
            return null;
        }
        $typeName = (string)($output['type'] ?? '');
        $matches = [];
        foreach ($inputs as $port) {
            if (!is_array($port) || (string)($port['type'] ?? '') === 'exec') continue;
            if ((string)($port['id'] ?? '') === $outputPort) return $outputPort;
            if ($typeName !== '' && (string)($port['type'] ?? '') === $typeName) {
                $matches[] = (string)($port['id'] ?? '');
            }
        }
        return count($matches) === 1 && $matches[0] !== '' ? $matches[0] : null;
    }

    public function inputJavaScriptExpression(
        string $nodeId,
        string $inputPort,
        string $default = 'null'
    ): string {
        return $this->inputJavaScriptExpressionInternal(
            $nodeId,
            $inputPort,
            $default,
            []
        );
    }

    /** @param array<string,bool> $visited */
    private function inputJavaScriptExpressionInternal(
        string $nodeId,
        string $inputPort,
        string $default,
        array $visited
    ): string {
        $visitKey = $this->graphId . '|' . $nodeId . '|' . $inputPort;
        if (isset($visited[$visitKey])) {
            return $default;
        }
        $visited[$visitKey] = true;

        $edge = $this->incomingEdges($nodeId, $inputPort)[0] ?? null;
        if (!is_array($edge)) {
            return $default;
        }

        $sourceId = (string) ($edge['from'] ?? '');
        $sourcePort = (string) ($edge['from_port'] ?? 'value');
        $sourceNode = $this->nodeIndexes[$this->graphId][$sourceId] ?? null;
        if (is_array($sourceNode) && $this->isNodeMuted($sourceNode)) {
            return $default;
        }

        $result = $this->compileNode(
            $sourceId,
            'javascript_expression',
            $sourcePort,
            $this->graphId
        );
        if (isset($result['expression'])) {
            return (string) $result['expression'];
        }
        if (isset($result['outputs'][$sourcePort])) {
            return (string) $result['outputs'][$sourcePort];
        }

        return $default;
    }

    public function inputArguments(string $nodeId, string $inputPort = 'arguments'): string
    {
        $edge = $this->incomingEdges($nodeId, $inputPort)[0] ?? null;
        if (!is_array($edge)) {
            return '';
        }
        $sourceId = (string)($edge['from'] ?? '');
        $sourcePort = (string)($edge['from_port'] ?? 'arguments');
        $sourceNode = $this->nodeIndexes[$this->graphId][$sourceId] ?? null;
        if (is_array($sourceNode) && $this->isNodeMuted($sourceNode)) {
            return '';
        }
        $result = $this->compileNode($sourceId, 'expression', $sourcePort, $this->graphId);
        return (string)($result['arguments'] ?? $result['expression'] ?? '');
    }

    /** @return array<string,mixed> */
    public function compileNode(string $nodeId, string $mode, ?string $outputPort = null, ?string $graphId = null): array
    {
        $graph = $graphId ?? $this->graphId;
        $cacheKey = $graph . '|' . $nodeId . '|' . $mode . '|' . ($outputPort ?? '');
        if ($mode === 'expression' && isset($this->expressionCache[$cacheKey])) {
            return $this->expressionCache[$cacheKey];
        }

        $previousGraph = $this->graphId;
        $previousMode = $this->mode;
        $previousPort = $this->outputPort;
        $this->graphId = $graph;
        $this->mode = $mode;
        $this->outputPort = $outputPort;

        try {
            $node = $this->node($nodeId, $graph);
            if ($this->isNodeMuted($node)) {
                $result = match ($mode) {
                    'view' => ['html' => '', 'muted' => true],
                    'migration' => ['up' => '', 'down' => '', 'next_port' => 'exec', 'muted' => true],
                    'statement' => ['code' => '', 'next_port' => 'exec', 'muted' => true],
                    'expression', 'javascript_expression' => ['expression' => 'null', 'outputs' => [], 'muted' => true],
                    'javascript_statement' => ['code' => '', 'next_port' => 'exec', 'muted' => true],
                    default => ['muted' => true],
                };
            } else {
                $compiler = $this->registry->compiler((string)$node['type']);
                $result = $compiler->compile($node, $this);
            }
        } finally {
            $this->graphId = $previousGraph;
            $this->mode = $previousMode;
            $this->outputPort = $previousPort;
        }

        if (in_array($mode, ['expression', 'javascript_expression'], true)) {
            $this->expressionCache[$cacheKey] = $result;
        }
        return $result;
    }

    public function compileJavaScriptGraph(string $graphId): string
    {
        if (!isset($this->graphs[$graphId])) {
            $this->addWarning("JavaScript graph {$graphId} was not found.");
            return '';
        }

        $parts = [];
        $bridgeKey = $this->javascriptPhpBridgeGraphKey($graphId);
        foreach ($this->javascriptPhpBridges($graphId) as $bridge) {
            $graphExpression = '((window.__thunderVisualIdePhpValues || {})['
                . $this->javascriptExport($bridgeKey)
                . '] || {})';
            $parts[] = 'const ' . $bridge['js_variable'] . ' = ('
                . $graphExpression . '[' . $this->javascriptExport($bridge['bridge_key']) . '] ?? null);';
        }

        foreach ($this->nodeIndexes[$graphId] ?? [] as $node) {
            if ($this->isNodeMuted($node)) {
                continue;
            }
            $type = (string) ($node['type'] ?? '');
            $nodeId = (string) ($node['id'] ?? '');
            if ($type === 'javascript.start') {
                $first = $this->nextNodeId($nodeId, 'exec', $graphId);
                if ($first !== null) {
                    $parts[] = $this->compileJavaScriptSequence(
                        $graphId,
                        $first,
                        [$nodeId => true]
                    );
                }
                continue;
            }

            if ($type === 'javascript.event_listener') {
                $parts[] = $this->compileJavaScriptSequence(
                    $graphId,
                    $nodeId,
                    []
                );
                continue;
            }

            if ($type === 'javascript.ajax_request') {
                $data = is_array($node['data'] ?? null) ? $node['data'] : [];
                $hasExecutionInput = $this->incomingEdges($nodeId, 'exec_in', $graphId) !== [];
                if (!$hasExecutionInput && !empty($data['intercept_submit'])) {
                    $selector = trim((string) ($data['form_selector'] ?? 'form')) ?: 'form';
                    $eventVar = $this->javascriptVariable($node, 'submit_event');
                    $formVar = $this->javascriptVariable($node, 'form');
                    $body = $this->compileJavaScriptSequence($graphId, $nodeId, []);
                    $parts[] = "document.addEventListener('submit', function ({$eventVar}) {\n"
                        . "    const {$formVar} = {$eventVar}.target.closest(" . $this->javascriptExport($selector) . ");\n"
                        . "    if (!{$formVar}) return;\n"
                        . "    {$eventVar}.preventDefault();\n"
                        . $this->indent($body) . "\n"
                        . "});";
                }
            }
        }

        $parts = array_values(array_filter(array_map('trim', $parts)));
        if ($parts === []) {
            return '';
        }

        $guard = '__tvi_js_flow_' . substr(
            preg_replace('/[^A-Za-z0-9_]+/', '_', $graphId) ?: 'graph',
            -32
        );

        return "(function () {\n"
            . "    'use strict';\n"
            . "    window.__thunderVisualIdeFlows = window.__thunderVisualIdeFlows || {};\n"
            . "    if (window.__thunderVisualIdeFlows[" . $this->javascriptExport($guard) . "]) return;\n"
            . "    window.__thunderVisualIdeFlows[" . $this->javascriptExport($guard) . "] = true;\n\n"
            . $this->indent(implode("\n\n", $parts)) . "\n"
            . "})();\n";
    }

    public function compileJavaScriptPhpBootstrap(string $graphId): string
    {
        $bridges = $this->javascriptPhpBridges($graphId);
        if ($bridges === []) {
            return '';
        }

        $entries = [];
        foreach ($bridges as $bridge) {
            $temporary = '__tvi_php_json_' . substr(
                preg_replace('/[^A-Za-z0-9_]+/', '_', $bridge['node_id']) ?: 'value',
                -18
            );
            $entries[] = $this->javascriptExport($bridge['bridge_key'])
                . ": <?= ((\${$temporary} = json_encode(("
                . $bridge['php_expression']
                . "), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) === false ? 'null' : \${$temporary}) ?>";
        }

        $graphKey = $this->javascriptPhpBridgeGraphKey($graphId);

        return "<script>\n"
            . "window.__thunderVisualIdePhpValues = window.__thunderVisualIdePhpValues || {};\n"
            . "window.__thunderVisualIdePhpValues[" . $this->javascriptExport($graphKey) . "] = {\n"
            . $this->indent(implode(",\n", $entries)) . "\n"
            . "};\n"
            . "</script>";
    }

    /** @return list<array{node_id:string,bridge_key:string,js_variable:string,php_expression:string}> */
    private function javascriptPhpBridges(string $graphId): array
    {
        if (isset($this->javascriptPhpBridgeCache[$graphId])) {
            return $this->javascriptPhpBridgeCache[$graphId];
        }

        $bridges = [];
        $variables = [];
        foreach ($this->nodeIndexes[$graphId] ?? [] as $node) {
            if ($this->isNodeMuted($node) || (string) ($node['type'] ?? '') !== 'javascript.php_value') {
                continue;
            }
            $result = $this->compileNode(
                (string) ($node['id'] ?? ''),
                'javascript_expression',
                'value',
                $graphId
            );
            $variable = (string) ($result['js_variable'] ?? '');
            if ($variable === '') {
                continue;
            }
            if (isset($variables[$variable])) {
                $this->addError("PHP Value to JS variable {$variable} is declared more than once in JavaScript graph {$graphId}.");
                continue;
            }
            $variables[$variable] = true;
            $bridges[] = [
                'node_id' => (string) ($node['id'] ?? 'php_value'),
                'bridge_key' => (string) ($result['bridge_key'] ?? $node['id'] ?? 'php_value'),
                'js_variable' => $variable,
                'php_expression' => (string) ($result['php_expression'] ?? 'null'),
            ];
        }

        return $this->javascriptPhpBridgeCache[$graphId] = $bridges;
    }

    private function javascriptPhpBridgeGraphKey(string $graphId): string
    {
        return 'graph_' . substr(hash('sha256', $graphId), 0, 16);
    }

    /** @param array<string,bool> $visited */
    public function compileJavaScriptSequence(
        string $graphId,
        string $nodeId,
        array $visited = []
    ): string {
        if (isset($visited[$nodeId])) {
            $this->addError("JavaScript execution cycle detected at node {$nodeId} in {$graphId}.");
            return '';
        }
        $visited[$nodeId] = true;

        $node = $this->node($nodeId, $graphId);
        if ($this->isNodeMuted($node)) {
            $nextPort = $this->singleJavaScriptExecutionOutputPort($node);
            if ($nextPort === null) {
                $this->addWarning("Muted JavaScript node {$nodeId} stops execution because it does not have exactly one JS execution output.");
                return '';
            }
            $next = $this->nextNodeId($nodeId, $nextPort, $graphId);
            return $next !== null
                ? $this->compileJavaScriptSequence($graphId, $next, $visited)
                : '';
        }

        $result = $this->compileNode(
            $nodeId,
            'javascript_statement',
            null,
            $graphId
        );
        $control = (string) ($result['control'] ?? '');

        if ($control === 'if') {
            $condition = (string) ($result['condition'] ?? 'false');
            $truePort = (string) ($result['true_port'] ?? 'true');
            $falsePort = (string) ($result['false_port'] ?? 'false');
            $trueId = $this->nextNodeId($nodeId, $truePort, $graphId);
            $falseId = $this->nextNodeId($nodeId, $falsePort, $graphId);
            $trueCode = $trueId !== null
                ? $this->compileJavaScriptSequence($graphId, $trueId, $visited)
                : '';
            $falseCode = $falseId !== null
                ? $this->compileJavaScriptSequence($graphId, $falseId, $visited)
                : '';
            return "if ({$condition}) {\n"
                . $this->indent($trueCode) . "\n"
                . "} else {\n"
                . $this->indent($falseCode) . "\n"
                . "}";
        }

        if ($control === 'event_listener') {
            $eventName = trim((string) ($result['event_name'] ?? 'click')) ?: 'click';
            $selector = trim((string) ($result['selector'] ?? ''));
            if ($selector === '') {
                $this->addWarning("JavaScript Event Listener {$nodeId} has no target selector.");
                return '';
            }
            $eventVar = (string) ($result['event_variable'] ?? 'event');
            $elementVar = (string) ($result['element_variable'] ?? 'element');
            $callbackId = $this->nextNodeId(
                $nodeId,
                (string) ($result['event_port'] ?? 'event'),
                $graphId
            );
            $callback = $callbackId !== null
                ? $this->compileJavaScriptSequence($graphId, $callbackId, $visited)
                : '';
            $options = [];
            if (!empty($result['capture'])) $options[] = 'capture: true';
            if (!empty($result['once'])) $options[] = 'once: true';
            if (!empty($result['passive'])) $options[] = 'passive: true';
            $optionCode = $options !== [] ? ', {' . implode(', ', $options) . '}' : '';
            $prevent = !empty($result['prevent_default'])
                ? "\n    {$eventVar}.preventDefault();"
                : '';
            $stop = !empty($result['stop_propagation'])
                ? "\n    {$eventVar}.stopPropagation();"
                : '';

            return "document.addEventListener(" . $this->javascriptExport($eventName)
                . ", function ({$eventVar}) {\n"
                . "    const {$elementVar} = {$eventVar}.target instanceof Element\n"
                . "        ? {$eventVar}.target.closest(" . $this->javascriptExport($selector) . ")\n"
                . "        : null;\n"
                . "    if (!{$elementVar}) return;"
                . $prevent . $stop . "\n"
                . $this->indent($callback) . "\n"
                . "}{$optionCode});";
        }

        if ($control === 'ajax') {
            return $this->compileJavaScriptAjax($node, $result, $graphId, $visited);
        }

        if ($control === 'delay') {
            $delay = (string) ($result['delay'] ?? '0');
            $next = $this->nextNodeId(
                $nodeId,
                (string) ($result['next_port'] ?? 'exec'),
                $graphId
            );
            $tail = $next !== null
                ? $this->compileJavaScriptSequence($graphId, $next, $visited)
                : '';
            return "window.setTimeout(function () {\n"
                . $this->indent($tail) . "\n"
                . "}, Math.max(0, Number({$delay}) || 0));";
        }

        $code = trim((string) ($result['code'] ?? ''));
        if (!empty($result['terminal'])) {
            return $code;
        }
        $next = $this->nextNodeId(
            $nodeId,
            (string) ($result['next_port'] ?? 'exec'),
            $graphId
        );
        $tail = $next !== null
            ? $this->compileJavaScriptSequence($graphId, $next, $visited)
            : '';

        return trim($code . ($code !== '' && $tail !== '' ? "\n" : '') . $tail);
    }

    /** @param array<string,mixed> $node @param array<string,mixed> $result @param array<string,bool> $visited */
    private function compileJavaScriptAjax(
        array $node,
        array $result,
        string $graphId,
        array $visited
    ): string {
        $nodeId = (string) ($node['id'] ?? 'ajax');
        $xhr = (string) ($result['xhr_variable'] ?? $this->javascriptVariable($node, 'xhr'));
        $response = (string) ($result['response_variable'] ?? $this->javascriptVariable($node, 'response'));
        $error = (string) ($result['error_variable'] ?? $this->javascriptVariable($node, 'error'));
        $progress = (string) ($result['progress_variable'] ?? $this->javascriptVariable($node, 'progress'));
        $status = (string) ($result['status_variable'] ?? $this->javascriptVariable($node, 'status'));
        $method = (string) ($result['method'] ?? "'POST'");
        $url = (string) ($result['url'] ?? "''");
        $body = (string) ($result['body'] ?? 'null');
        $headers = (string) ($result['headers'] ?? 'null');
        $timeout = (string) ($result['timeout'] ?? '30000');
        $responseType = trim((string) ($result['response_type'] ?? 'text'));
        $withCredentials = !empty($result['with_credentials']) ? 'true' : 'false';
        $parseJson = !empty($result['parse_json']);

        $branch = function (string $port) use ($nodeId, $graphId, $visited): string {
            $next = $this->nextNodeId($nodeId, $port, $graphId);
            return $next !== null
                ? $this->compileJavaScriptSequence($graphId, $next, $visited)
                : '';
        };

        $before = $branch('before_send');
        $upload = $branch('upload_progress');
        $download = $branch('download_progress');
        $success = $branch('success');
        $httpError = $branch('http_error');
        $networkError = $branch('network_error');
        $timeoutCode = $branch('timeout');
        $aborted = $branch('aborted');
        $complete = $branch('complete');

        $parse = $parseJson
            ? "try { {$response} = {$xhr}.responseType === 'json' ? {$xhr}.response : JSON.parse({$xhr}.responseText || 'null'); } catch (__tvi_parse_error) { {$error} = __tvi_parse_error; {$response} = null; }"
            : "{$response} = {$xhr}.responseType && {$xhr}.responseType !== 'text' ? {$xhr}.response : {$xhr}.responseText;";

        $progressHandler = function (string $code) use ($xhr, $progress): string {
            return "{$progress} = {\n"
                . "    loaded: event.loaded || 0,\n"
                . "    total: event.lengthComputable ? event.total : 0,\n"
                . "    percent: event.lengthComputable && event.total > 0 ? Math.round((event.loaded / event.total) * 100) : null,\n"
                . "    lengthComputable: Boolean(event.lengthComputable),\n"
                . "    xhr: {$xhr}\n"
                . "};\n"
                . $code;
        };

        $lines = [];
        $lines[] = "const {$xhr} = new XMLHttpRequest();";
        $lines[] = "let {$response} = null;";
        $lines[] = "let {$error} = null;";
        $lines[] = "let {$progress} = null;";
        $lines[] = "let {$status} = 0;";
        $lines[] = "{$xhr}.open(String({$method} || 'GET').toUpperCase(), String({$url} || ''), true);";
        $lines[] = "{$xhr}.timeout = Math.max(0, Number({$timeout}) || 0);";
        $lines[] = "{$xhr}.withCredentials = {$withCredentials};";
        if ($responseType !== '' && $responseType !== 'text') {
            $lines[] = "{$xhr}.responseType = " . $this->javascriptExport($responseType) . ";";
        }
        $headersVariable = $this->javascriptVariable($node, 'headers');
        $lines[] = "const {$headersVariable} = {$headers};";
        $lines[] = "if ({$headersVariable} && typeof {$headersVariable} === 'object') { Object.entries({$headersVariable}).forEach(function (entry) { {$xhr}.setRequestHeader(String(entry[0]), String(entry[1])); }); }";
        if ($upload !== '') {
            $lines[] = "{$xhr}.upload.addEventListener('progress', function (event) {\n"
                . $this->indent($progressHandler($upload)) . "\n});";
        }
        if ($download !== '') {
            $lines[] = "{$xhr}.addEventListener('progress', function (event) {\n"
                . $this->indent($progressHandler($download)) . "\n});";
        }
        $lines[] = "{$xhr}.addEventListener('load', function () {\n"
            . "    {$status} = {$xhr}.status;\n"
            . "    {$parse}\n"
            . "    if ({$status} >= 200 && {$status} < 300) {\n"
            . $this->indent($success, 2) . "\n"
            . "    } else {\n"
            . $this->indent($httpError, 2) . "\n"
            . "    }\n"
            . "});";
        if ($networkError !== '') {
            $lines[] = "{$xhr}.addEventListener('error', function (event) { {$error} = event;\n"
                . $this->indent($networkError) . "\n});";
        }
        if ($timeoutCode !== '') {
            $lines[] = "{$xhr}.addEventListener('timeout', function (event) { {$error} = event;\n"
                . $this->indent($timeoutCode) . "\n});";
        }
        if ($aborted !== '') {
            $lines[] = "{$xhr}.addEventListener('abort', function (event) { {$error} = event;\n"
                . $this->indent($aborted) . "\n});";
        }
        if ($complete !== '') {
            $lines[] = "{$xhr}.addEventListener('loadend', function () {\n"
                . $this->indent($complete) . "\n});";
        }
        if ($before !== '') $lines[] = $before;
        $lines[] = "{$xhr}.send({$body});";

        return "{\n" . $this->indent(implode("\n", $lines)) . "\n}";
    }

    /** @param array<string,mixed> $node */
    private function singleJavaScriptExecutionOutputPort(array $node): ?string
    {
        try {
            $definition = $this->registry->definition((string) ($node['type'] ?? ''));
        } catch (\Throwable) {
            return null;
        }
        $ports = [];
        foreach ((array) ($definition['ports']['outputs'] ?? []) as $port) {
            if (!is_array($port) || (string) ($port['type'] ?? '') !== 'js-exec') continue;
            $id = trim((string) ($port['id'] ?? ''));
            if ($id !== '') $ports[] = $id;
        }
        return count($ports) === 1 ? $ports[0] : null;
    }

    /** @param array<string,mixed> $node */
    public function javascriptVariable(array $node, string $prefix = 'value'): string
    {
        $id = preg_replace('/[^A-Za-z0-9_]+/', '_', (string) ($node['id'] ?? 'node')) ?: 'node';
        if (preg_match('/^[0-9]/', $id)) $id = '_' . $id;
        return '__tvi_' . preg_replace('/[^A-Za-z0-9_]+/', '_', $prefix) . '_' . substr($id, -18);
    }

    public function javascriptExport(mixed $value): string
    {
        $json = json_encode(
            $value,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );
        return $json === false ? 'null' : $json;
    }

    public function viewNodeSelector(string $viewNodeId): string
    {
        return '[data-tvi-view-node="' . addcslashes($viewNodeId, "\\\"") . '"]';
    }

    public function compileFlowGraph(string $graphId): string
    {
        if (!isset($this->graphs[$graphId])) {
            $this->addWarning("Flow graph {$graphId} was not found.");
            return '';
        }

        $start = null;
        foreach ($this->nodeIndexes[$graphId] ?? [] as $node) {
            if (($node['type'] ?? '') === 'flow.start') {
                $start = $node;
                break;
            }
        }
        if ($start === null) {
            $this->addWarning("Flow graph {$graphId} has no Start node.");
            return '';
        }

        $first = $this->nextNodeId((string)$start['id'], 'exec', $graphId);
        $code = $first !== null ? $this->compileSequence($graphId, $first, []) : '';
        $services = $this->servicePrelude($graphId);
        return trim($services . ($services !== '' && $code !== '' ? "\n\n" : '') . $code);
    }

    /** @param array<string,bool> $visited */
    private function compileSequence(string $graphId, string $nodeId, array $visited): string
    {
        if (isset($visited[$nodeId])) {
            $this->addError("Execution cycle detected at node {$nodeId} in {$graphId}.");
            return '';
        }
        $visited[$nodeId] = true;

        $node = $this->node($nodeId, $graphId);
        if ($this->isNodeMuted($node)) {
            $nextPort = $this->singleExecutionOutputPort($node);
            if ($nextPort === null) {
                $this->addWarning("Muted node {$nodeId} stops execution because it does not have exactly one execution output.");
                return '';
            }
            $next = $this->nextNodeId($nodeId, $nextPort, $graphId);
            return $next !== null ? $this->compileSequence($graphId, $next, $visited) : '';
        }

        $result = $this->compileNode($nodeId, 'statement', null, $graphId);
        if (($result['control'] ?? '') === 'if') {
            $before = trim((string)($result['before'] ?? ''));
            $condition = (string)($result['condition'] ?? 'false');
            $truePort = (string)($result['true_port'] ?? 'true');
            $falsePort = (string)($result['false_port'] ?? 'false');
            $trueId = $this->nextNodeId($nodeId, $truePort, $graphId);
            $falseId = $this->nextNodeId($nodeId, $falsePort, $graphId);
            $trueCode = $trueId !== null ? $this->compileSequence($graphId, $trueId, $visited) : '';
            $falseCode = $falseId !== null ? $this->compileSequence($graphId, $falseId, $visited) : '';
            $falseBefore = trim((string)($result['false_before'] ?? ''));
            if ($falseBefore !== '') {
                $falseCode = trim($falseBefore . ($falseCode !== '' ? "\n" . $falseCode : ''));
            }
            $block = ($before !== '' ? $before . "\n" : '')
                . "if ({$condition}) {\n" . $this->indent($trueCode) . "\n} else {\n" . $this->indent($falseCode) . "\n}";
            return trim($block);
        }

        $code = trim((string)($result['code'] ?? ''));
        if (!empty($result['terminal'])) {
            return $code;
        }
        $nextPort = (string)($result['next_port'] ?? 'exec');
        $next = $this->nextNodeId($nodeId, $nextPort, $graphId);
        $tail = $next !== null ? $this->compileSequence($graphId, $next, $visited) : '';
        return trim($code . ($code !== '' && $tail !== '' ? "\n" : '') . $tail);
    }

    /** @param array<string,mixed> $node */
    private function singleExecutionOutputPort(array $node): ?string
    {
        try {
            $definition = $this->registry->definition((string)($node['type'] ?? ''));
        } catch (\Throwable) {
            return null;
        }
        $ports = [];
        foreach ((array)($definition['ports']['outputs'] ?? []) as $port) {
            if (is_array($port) && (string)($port['type'] ?? '') === 'exec') {
                $id = trim((string)($port['id'] ?? ''));
                if ($id !== '') $ports[] = $id;
            }
        }
        return count($ports) === 1 ? $ports[0] : null;
    }

    public function nextNodeId(string $nodeId, string $port = 'exec', ?string $graphId = null): ?string
    {
        $edge = $this->outgoingEdges($nodeId, $port, $graphId)[0] ?? null;
        return is_array($edge) ? (string)($edge['to'] ?? '') : null;
    }

    /** @return array{up:string,down:string} */
    public function compileMigrationGraph(string $graphId): array
    {
        if (!isset($this->graphs[$graphId])) {
            $this->addWarning("Migration graph {$graphId} was not found.");
            return ['up' => '', 'down' => ''];
        }
        $start = null;
        foreach ($this->nodeIndexes[$graphId] ?? [] as $node) {
            if (($node['type'] ?? '') === 'migration.start') { $start = $node; break; }
        }
        if ($start === null) {
            $this->addWarning("Migration graph {$graphId} has no Migration Start node.");
            return ['up' => '', 'down' => ''];
        }
        $current = $this->nextNodeId((string)$start['id'], 'exec', $graphId);
        $visited = [];
        $up = [];
        $down = [];
        while ($current !== null && $current !== '') {
            if (isset($visited[$current])) {
                $this->addError("Migration cycle detected at node {$current} in {$graphId}.");
                break;
            }
            $visited[$current] = true;
            $node = $this->node($current, $graphId);
            if ($this->isNodeMuted($node)) {
                $nextPort = $this->singleExecutionOutputPort($node);
                if ($nextPort === null) {
                    $this->addWarning("Muted migration node {$current} stops the migration chain because it does not have exactly one execution output.");
                    break;
                }
                $current = $this->nextNodeId($current, $nextPort, $graphId);
                continue;
            }
            $result = $this->compileNode($current, 'migration', null, $graphId);
            $upCode = trim((string)($result['up'] ?? ''));
            $downCode = trim((string)($result['down'] ?? ''));
            if ($upCode !== '') $up[] = $upCode;
            if ($downCode !== '') array_unshift($down, $downCode);
            $current = $this->nextNodeId($current, (string)($result['next_port'] ?? 'exec'), $graphId);
        }
        return ['up' => implode("
", $up), 'down' => implode("
", $down)];
    }

    /**
     * Compile a nested visual View graph.
     *
     * @return array{inline:string,components:array<string,string>,document:string,component_css:string,uses_visual_nodes:bool,requires_base_css_asset:bool}
     */
    public function compileViewGraph(string $graphId): array
    {
        if (!isset($this->graphs[$graphId])) {
            $this->addWarning("View graph {$graphId} was not found.");
            return [
                'inline' => '',
                'components' => [],
                'document' => '',
                'component_css' => '',
                'uses_visual_nodes' => false,
                'requires_base_css_asset' => false,
            ];
        }

        $graph = $this->graph($graphId);
        $this->viewCssCollections[$graphId] = [];
        $childIds = [];
        foreach ((array)($graph['edges'] ?? []) as $edge) {
            if (!is_array($edge)) {
                continue;
            }
            $isViewChild = (string)($edge['port_type'] ?? '') === 'view-child'
                || (
                    (string)($edge['from_port'] ?? '') === 'children'
                    && (string)($edge['to_port'] ?? '') === 'parent'
                );
            if (!$isViewChild) {
                continue;
            }
            $to = (string)($edge['to'] ?? '');
            if ($to !== '') {
                $childIds[$to] = true;
            }
        }

        $roots = array_values(array_filter((array)($graph['nodes'] ?? []), static function ($node) use ($childIds): bool {
            return is_array($node)
                && (string)($node['type'] ?? '') !== 'visual.folder'
                && !isset($childIds[(string)($node['id'] ?? '')]);
        }));
        usort($roots, static fn (array $a, array $b): int => ((float)($a['y'] ?? 0) <=> (float)($b['y'] ?? 0)) ?: ((float)($a['x'] ?? 0) <=> (float)($b['x'] ?? 0)));

        $inline = [];
        $components = [];
        $document = '';
        $usesBaseCss = false;
        $requiresBaseCssAsset = false;
        foreach ($roots as $root) {
            $result = $this->compileViewNode((string)$root['id'], $graphId);
            $html = trim((string)($result['html'] ?? ''));

            if (!empty($result['is_document'])) {
                if ($document !== '') {
                    $this->addWarning(
                        "View graph {$graphId} contains more than one HTML Boilerplate; only the first document shell was used."
                    );
                    continue;
                }
                $document = $html;
                if (!empty($result['requires_base_css_asset'])) {
                    $requiresBaseCssAsset = true;
                }
                continue;
            }

            if ($html !== '' && ($result['uses_base_css'] ?? true)) {
                $usesBaseCss = true;
                $requiresBaseCssAsset = true;
            }
            if (!empty($result['requires_base_css_asset'])) {
                $requiresBaseCssAsset = true;
            }
            $componentName = ViewNodeSupport::componentName((string)($result['component_name'] ?? ''));
            if ($componentName !== '') {
                if (isset($components[$componentName])) {
                    $this->addWarning("Duplicate visual component name {$componentName} in graph {$graphId}; the last root component was used.");
                }
                $components[$componentName] = $html;
            } elseif ($html !== '') {
                $inline[] = $html;
            }
        }

        return [
            'inline' => implode("\n\n", $inline),
            'components' => $components,
            'document' => $document,
            'component_css' => implode("\n\n", array_values($this->viewCssCollections[$graphId] ?? [])),
            'uses_visual_nodes' => $usesBaseCss,
            'requires_base_css_asset' => $requiresBaseCssAsset,
        ];
    }

    /** @return array<string,mixed> */
    public function compileViewNode(string $nodeId, ?string $graphId = null): array
    {
        $graph = $graphId ?? $this->graphId;
        $key = $graph . '|' . $nodeId;
        if (isset($this->viewStack[$key])) {
            $this->addError("Visual view cycle detected at node {$nodeId} in {$graph}.");
            return ['html' => ''];
        }
        $this->viewStack[$key] = true;
        try {
            $result = $this->compileNode($nodeId, 'view', null, $graph);
            if (isset($result['html']) && is_string($result['html'])) {
                $result['html'] = $this->decorateViewNodeHtml($result['html'], $nodeId);
            }
            $css = trim((string) ($result['css'] ?? ''));
            if ($css !== '') {
                $this->viewCssCollections[$graph][hash('sha256', $css)] = $css;
            }
            return $result;
        } finally {
            unset($this->viewStack[$key]);
        }
    }

    private function decorateViewNodeHtml(string $html, string $nodeId): string
    {
        if ($html === '' || str_contains($html, 'data-tvi-view-node=')) {
            return $html;
        }
        $attribute = ' data-tvi-view-node="'
            . htmlspecialchars($nodeId, ENT_QUOTES, 'UTF-8')
            . '"';
        $decorated = preg_replace(
            '/<(?![!?])([A-Za-z][A-Za-z0-9:_-]*)(?=\s|>)/',
            '<$1' . $attribute,
            $html,
            1
        );
        return is_string($decorated) ? $decorated : $html;
    }

    public function compileViewChildren(string $nodeId, string $port = 'children', ?string $graphId = null): string
    {
        return $this->compileViewChildrenDetailed(
            $nodeId,
            $port,
            $graphId
        )['html'];
    }

    /** @return array{html:string,uses_base_css:bool} */
    public function compileViewChildrenDetailed(
        string $nodeId,
        string $port = 'children',
        ?string $graphId = null
    ): array {
        $graph = $graphId ?? $this->graphId;
        $edges = $this->outgoingEdges($nodeId, $port, $graph);
        usort($edges, function (array $a, array $b) use ($graph): int {
            $nodeA = $this->nodeIndexes[$graph][(string)($a['to'] ?? '')] ?? [];
            $nodeB = $this->nodeIndexes[$graph][(string)($b['to'] ?? '')] ?? [];
            return ((float)($nodeA['y'] ?? 0) <=> (float)($nodeB['y'] ?? 0))
                ?: ((float)($nodeA['x'] ?? 0) <=> (float)($nodeB['x'] ?? 0));
        });

        $parts = [];
        $usesBaseCss = false;
        foreach ($edges as $edge) {
            $childId = (string)($edge['to'] ?? '');
            if ($childId === '') {
                continue;
            }
            $result = $this->compileViewNode($childId, $graph);
            $html = trim((string)($result['html'] ?? ''));
            if ($html === '') {
                continue;
            }
            $parts[] = $html;
            if ($result['uses_base_css'] ?? true) {
                $usesBaseCss = true;
            }
            if (!empty($result['requires_base_css_asset'])) {
                $usesBaseCss = true;
            }
        }

        return [
            'html' => implode("\n", $parts),
            'uses_base_css' => $usesBaseCss,
        ];
    }

    /** @return list<array<string,mixed>> */
    public function formSchemas(?string $viewNodeId = null): array
    {
        $schemas = [];
        foreach ($this->nodesOfType('lifecycle.view', 'main') as $view) {
            $viewId = (string)($view['id'] ?? '');
            if ($viewNodeId !== null && $viewNodeId !== '' && $viewId !== $viewNodeId) {
                continue;
            }
            $graphId = trim((string)($view['data']['graph_id'] ?? ''));
            if ($graphId === '' || !isset($this->graphs[$graphId])) {
                continue;
            }
            $graph = $this->graph($graphId);
            foreach ((array)($graph['nodes'] ?? []) as $form) {
                if (!is_array($form) || (string)($form['type'] ?? '') !== 'view.form') {
                    continue;
                }
                $formId = (string)($form['id'] ?? '');
                $data = is_array($form['data'] ?? null) ? $form['data'] : [];
                $schemaName = ViewNodeSupport::componentName((string)($data['schema_name'] ?? ''));
                if ($schemaName === '') {
                    $schemaName = ViewNodeSupport::componentName((string)($data['component_name'] ?? ''));
                }
                if ($schemaName === '') {
                    $schemaName = ViewNodeSupport::componentName((string)($data['title'] ?? 'form')) ?: 'form';
                }
                $reachable = $this->viewDescendants($formId, $graphId);
                $fields = [];
                foreach ($reachable as $field) {
                    if ($this->isNodeMuted($field)) continue;
                    $fieldType = (string)($field['type'] ?? '');
                    $fieldData = is_array($field['data'] ?? null) ? $field['data'] : [];
                    $name = trim((string)($fieldData['name'] ?? ''));
                    if ($name === '' || !in_array($fieldType, [
                        'view.text_input','view.email_input','view.password_input','view.number_input',
                        'view.date_input','view.file_input','view.textarea','view.select','view.checkbox','view.hidden_input'
                    ], true)) {
                        continue;
                    }
                    $rules = [];
                    if (!empty($fieldData['required'])) $rules[] = 'required';
                    if ($fieldType === 'view.email_input') $rules[] = 'email';
                    elseif ($fieldType === 'view.number_input') $rules[] = 'numeric';
                    elseif ($fieldType === 'view.date_input') $rules[] = 'date';
                    $additional = preg_split('/\s*,\s*/', trim((string)($fieldData['validation_rules'] ?? ''))) ?: [];
                    foreach ($additional as $rule) {
                        $rule = trim((string)$rule);
                        if ($rule !== '') $rules[] = $rule;
                    }
                    $fields[] = [
                        'node_id' => (string)($field['id'] ?? ''),
                        'node_type' => $fieldType,
                        'name' => $name,
                        'label' => (string)($fieldData['label'] ?? $name),
                        'port_type' => $fieldType === 'view.number_input' ? 'number' : 'string',
                        'rules' => array_values(array_unique($rules)),
                    ];
                }
                $schemas[] = [
                    'view_node_id' => $viewId,
                    'view_title' => (string)($view['data']['title'] ?? $view['data']['filename'] ?? 'View'),
                    'form_node_id' => $formId,
                    'schema_name' => $schemaName,
                    'component_name' => ViewNodeSupport::componentName((string)($data['component_name'] ?? '')),
                    'method' => strtoupper((string)($data['method'] ?? 'POST')),
                    'fields' => $fields,
                ];
            }
        }
        return $schemas;
    }

    /** @return array<string,mixed>|null */
    public function formSchema(string $viewNodeId, string $schemaName = ''): ?array
    {
        $schemas = $this->formSchemas($viewNodeId);
        if ($schemas === []) return null;
        $wanted = ViewNodeSupport::componentName($schemaName);
        if ($wanted === '') return $schemas[0];
        foreach ($schemas as $schema) {
            if ((string)($schema['schema_name'] ?? '') === $wanted || (string)($schema['component_name'] ?? '') === $wanted) {
                return $schema;
            }
        }
        return null;
    }

    /** @return list<array<string,mixed>> */
    private function viewDescendants(string $nodeId, string $graphId): array
    {
        $result = [];
        $visited = [];
        $walk = function (string $parentId) use (&$walk, &$result, &$visited, $graphId): void {
            if (isset($visited[$parentId])) return;
            $visited[$parentId] = true;
            foreach ($this->outgoingEdges($parentId, null, $graphId) as $edge) {
                if ((string)($edge['port_type'] ?? '') !== 'view-child') continue;
                $childId = (string)($edge['to'] ?? '');
                if ($childId === '' || isset($visited[$childId])) continue;
                try {
                    $child = $this->node($childId, $graphId);
                } catch (\Throwable) {
                    continue;
                }
                $result[] = $child;
                $walk($childId);
            }
        };
        $walk($nodeId);
        return $result;
    }

    public function requireService(string $service): void
    {
        $this->requirements[$this->graphId][$service] = true;
    }

    public function modelVariable(string $modelNodeId): string
    {
        $this->requirements[$this->graphId]['model:' . $modelNodeId] = true;
        $model = $this->node($modelNodeId, 'main');
        $class = $this->studly((string)($model['data']['class_name'] ?? $model['data']['name'] ?? 'Model'));
        return '$' . lcfirst($class) . 'Model';
    }

    private function servicePrelude(string $graphId): string
    {
        $lines = [];
        foreach (array_keys($this->requirements[$graphId] ?? []) as $service) {
            if ($service === 'request') {
                $lines[] = '$request = new \\Core\\Request();';
            } elseif ($service === 'session') {
                $lines[] = '$session = new \\Core\\Session();';
            } elseif ($service === 'database') {
                $lines[] = '$db = new \\Core\\Database();';
            } elseif (str_starts_with($service, 'model:')) {
                $modelId = substr($service, 6);
                if (!isset($this->nodeIndexes['main'][$modelId])) {
                    continue;
                }
                $model = $this->nodeIndexes['main'][$modelId];
                $class = $this->studly((string)($model['data']['class_name'] ?? $model['data']['name'] ?? 'Model'));
                $lines[] = '$' . lcfirst($class) . 'Model = new ' . $class . '();';
            }
        }
        return implode("\n", array_values(array_unique($lines)));
    }

    public function variableForNode(
        array $node,
        string $suffix = 'result',
        ?string $property = 'result_variable'
    ): string {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];

        if ($property !== null) {
            $requested = $this->cleanVariableName((string) ($data[$property] ?? ''));
            if ($requested !== '') {
                return '$' . $requested;
            }
        }

        $id = preg_replace(
            '/[^A-Za-z0-9_]+/',
            '_',
            (string) ($node['id'] ?? 'node')
        ) ?: 'node';

        return '$' . $suffix . '_' . substr($id, -12);
    }

    public function cleanVariableName(string $name): string
    {
        $name = ltrim(trim($name), '$');
        $name = preg_replace('/[^A-Za-z0-9_]+/', '_', $name) ?: '';
        if ($name !== '' && preg_match('/^[0-9]/', $name)) {
            $name = '_' . $name;
        }

        return $name;
    }

    /** @return list<array<string,mixed>> */
    public function modelMethods(string $modelNodeId): array
    {
        try {
            $model = $this->node($modelNodeId, 'main');
        } catch (\Throwable) {
            return [];
        }
        $raw = $model['data']['methods_json'] ?? '[]';
        if (is_array($raw)) {
            return $raw;
        }
        $methods = json_decode((string)$raw, true);
        return is_array($methods) ? array_values(array_filter($methods, 'is_array')) : [];
    }

    /** @return array<string,mixed>|null */
    public function modelMethod(string $modelNodeId, string $methodName): ?array
    {
        foreach ($this->modelMethods($modelNodeId) as $method) {
            if ((string)($method['name'] ?? '') === $methodName) {
                return $method;
            }
        }
        return null;
    }

    /** @return list<array<string,mixed>> */
    public function classMethods(string $classNodeId): array
    {
        try {
            $class = $this->node($classNodeId, 'main');
        } catch (\Throwable) {
            return [];
        }
        $raw = $class['data']['methods_json'] ?? '[]';
        if (is_array($raw)) {
            return array_values(array_filter($raw, 'is_array'));
        }
        $methods = json_decode((string)$raw, true);
        return is_array($methods) ? array_values(array_filter($methods, 'is_array')) : [];
    }

    /** @return array<string,mixed>|null */
    public function classMethod(string $classNodeId, string $methodName): ?array
    {
        foreach ($this->classMethods($classNodeId) as $method) {
            if ((string)($method['name'] ?? '') === $methodName) {
                return $method;
            }
        }
        return null;
    }

    public function pluginNamespace(): string
    {
        $plugin = $this->firstNodeOfType('project.plugin');
        $data = is_array($plugin['data'] ?? null) ? $plugin['data'] : [];
        $namespace = trim((string)($data['namespace'] ?? ''));
        if ($namespace === '') {
            $namespace = $this->studly((string)($data['id'] ?? $data['name'] ?? 'GeneratedPlugin'));
        }
        return $this->cleanNamespace($namespace);
    }

    public function cleanNamespace(string $namespace): string
    {
        $parts = array_values(array_filter(array_map(
            static fn (string $part): string => preg_replace('/[^A-Za-z0-9_]/', '', $part) ?: '',
            explode('\\', $namespace)
        ), static fn (string $part): bool => $part !== ''));
        return $parts !== [] ? implode('\\', $parts) : 'GeneratedPlugin';
    }

    public function addError(string $message): void
    {
        if (!in_array($message, $this->errors, true)) {
            $this->errors[] = $message;
        }
    }

    public function addWarning(string $message): void
    {
        if (!in_array($message, $this->warnings, true)) {
            $this->warnings[] = $message;
        }
    }

    /** @return list<string> */
    public function errors(): array
    {
        return $this->errors;
    }

    /** @return list<string> */
    public function warnings(): array
    {
        return $this->warnings;
    }

    public function export(mixed $value): string
    {
        return var_export($value, true);
    }

    public function studly(string $value): string
    {
        $value = trim($value);
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value) === 1) {
            return ucfirst($value);
        }
        $value = preg_replace('/[^A-Za-z0-9]+/', ' ', $value) ?? $value;
        return str_replace(' ', '', ucwords(strtolower(trim($value)))) ?: 'Generated';
    }

    public function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;
        return trim($value, '-') ?: 'generated-plugin';
    }

    public function indent(string $code, int $level = 1): string
    {
        if ($code === '') {
            return '    // No connected statements.';
        }
        $prefix = str_repeat('    ', $level);
        return $prefix . str_replace("\n", "\n" . $prefix, $code);
    }
}
