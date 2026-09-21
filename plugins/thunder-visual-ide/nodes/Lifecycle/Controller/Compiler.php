<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Lifecycle;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ControllerCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $graphId = (string) ($data['graph_id'] ?? '');
        $controllerCode = $graphId !== ''
            ? $context->compileFlowGraph($graphId)
            : '';

        $hookType = (string) ($data['hook_type'] ?? 'action') === 'filter'
            ? 'filter'
            : 'action';
        $dataVariable = $this->variableName((string) ($data['data_variable'] ?? 'data'));

        if ($hookType === 'filter') {
            $returnStatement = 'return $' . $dataVariable . ';';
            if (!preg_match('/return\\s+\\$' . preg_quote($dataVariable, '/') . '\\s*;\\s*$/', trim($controllerCode))) {
                $controllerCode = trim($controllerCode . ($controllerCode !== '' ? "\n" : '') . $returnStatement);
            }
        }

        $routeNodes = $context->connectedNodesOfType(
            (string) $node['id'],
            'routing.route',
            'main'
        );

        $hooks = [];
        $hookName = trim((string) ($data['hook_name'] ?? 'controller'));
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

            $hooks[] = $this->buildHook(
                $context,
                $hookType,
                $hookName,
                $dataVariable,
                $routeName,
                $priority,
                $controllerCode
            );
        }

        if ($hooks === []) {
            $hooks[] = $this->buildHook(
                $context,
                $hookType,
                $hookName,
                $dataVariable,
                null,
                $fallbackPriority,
                $controllerCode
            );
        }

        return [
            'hooks' => $hooks,
            'load_all_routes' => $routeNodes === [],
        ];
    }

    private function buildHook(
        CompileContext $context,
        string $hookType,
        string $hookName,
        string $dataVariable,
        ?string $routeName,
        int $priority,
        string $controllerCode
    ): string {
        $registrationFunction = $hookType === 'filter' ? 'add_filter' : 'add_action';
        $routeArgument = $routeName === null ? '' : ', ' . $context->export($routeName);

        return sprintf(
            "%s(%s, function ($%s = []) {\n%s\n}, %d%s);",
            $registrationFunction,
            $context->export($hookName),
            $dataVariable,
            $context->indent($controllerCode),
            $priority,
            $routeArgument
        );
    }

    private function variableName(string $value): string
    {
        $name = preg_replace('/^\\$+/', '', trim($value));
        $name = preg_replace('/[^A-Za-z0-9_]+/', '_', (string) $name) ?: 'data';
        if (preg_match('/^[0-9]/', $name)) {
            $name = '_' . $name;
        }
        return $name;
    }
}
