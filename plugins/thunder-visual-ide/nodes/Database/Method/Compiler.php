<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Database;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class QueryBuilderMethodCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('database');
        $method = $this->selectedMethod($node, $context);
        $resultVariable = $context->variableForNode($node, 'result');
        $builderExpression = $context->inputExpression((string) $node['id'], 'builder', '$db');
        $returnType = strtolower(trim((string) ($method['return_type'] ?? 'mixed')));
        $chainable = !empty($method['chainable']) || in_array($returnType, ['self', 'static'], true);
        $continuationExpression = $chainable ? $resultVariable : $builderExpression;

        if ($context->mode() === 'expression') {
            $port = $context->outputPort() ?: 'result';
            return [
                'expression' => $port === 'builder' ? ($continuationExpression ?? $builderExpression) : $resultVariable,
                'outputs' => ['result' => $resultVariable, 'builder' => $continuationExpression ?? $builderExpression],
            ];
        }

        if ($method === null) {
            $context->addError('Query Builder Method requires a valid method selection.');
            return [
                'code' => '// Invalid Query Builder method selection.',
                'next_port' => 'exec',
                'outputs' => ['result' => 'null', 'builder' => $builderExpression],
            ];
        }

        $arguments = $this->compileArguments($node, $method, $context);
        $name = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($method['name'] ?? ''));
        $call = '(' . $builderExpression . ')->' . $name . '(' . implode(', ', $arguments) . ')';
        if ($returnType === 'void' || $returnType === 'never') {
            return [
                'code' => $call . ';',
                'next_port' => 'exec',
                'terminal' => $returnType === 'never',
                'outputs' => ['result' => 'null', 'builder' => $builderExpression],
            ];
        }

        return [
            'code' => $resultVariable . ' = ' . $call . ';',
            'next_port' => 'exec',
            'outputs' => ['result' => $resultVariable, 'builder' => $continuationExpression],
        ];
    }

    /** @return list<string> */
    private function compileArguments(array $node, array $method, CompileContext $context): array
    {
        $metadata = [];
        foreach ((array) ($method['params'] ?? []) as $parameter) {
            if (!is_array($parameter)) continue;
            $name = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($parameter['name'] ?? ''));
            if ($name === '') continue;
            $metadata[] = [
                'port' => 'param_' . $name,
                'default' => trim((string) ($parameter['default'] ?? '')),
                'fallback' => trim((string) ($parameter['fallback'] ?? $parameter['default'] ?? 'null')),
                'optional' => !empty($parameter['optional']),
                'spread' => !empty($parameter['spread']),
            ];
        }

        $lastArgument = -1;
        foreach ($metadata as $index => $parameter) {
            $connected = $context->incomingEdges((string) $node['id'], (string) $parameter['port']) !== [];
            if ($connected || empty($parameter['optional'])) $lastArgument = $index;
        }

        $arguments = [];
        foreach ($metadata as $index => $parameter) {
            if ($index > $lastArgument) break;
            $fallback = (string) $parameter['fallback'];
            $expression = $context->inputExpression(
                (string) $node['id'],
                (string) $parameter['port'],
                $fallback !== '' ? rtrim($fallback, ';') : 'null'
            );
            $arguments[] = (!empty($parameter['spread']) ? '...' : '') . $expression;
        }
        return $arguments;
    }

    private function selectedMethod(array $node, CompileContext $context): ?array
    {
        $selected = (string) ($node['data']['method'] ?? 'table');
        $definition = $context->registry()->definition('database.query_builder_method');
        foreach ((array) ($definition['methods'] ?? []) as $method) {
            if (is_array($method) && (string) ($method['name'] ?? '') === $selected) return $method;
        }
        return null;
    }
}
