<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Models;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class MethodCallCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $modelId = (string)($data['model_node_id'] ?? '');
        $methodName = preg_replace('/[^A-Za-z0-9_]/', '', (string)($data['method_name'] ?? ''));
        $variable = $context->variableForNode($node, 'result');
        $meta = $context->modelMethod($modelId, $methodName) ?? [];
        $returnType = strtolower(trim((string)($meta['return_type'] ?? '')));
        $modelVariable = $context->inputExpression((string) $node['id'], 'model', $context->modelVariable($modelId));
        $chainable = !empty($meta['chainable']) || in_array($returnType, ['self', 'static'], true);
        $continuationExpression = $chainable ? $variable : $modelVariable;
        if ($context->mode() === 'expression')
        {
            return [
                'expression' => $returnType === 'void' || $returnType === 'never' ? 'null' : $variable,
                'outputs' => [
                    'result' => $variable,
                    'model' => $continuationExpression,
                ],
            ];
        }
        if ($modelId === '' || $methodName === '')
        {
            $context->addError('Model Method Call requires a Model and method.');
            return [
                'code' => '// Invalid model method call.',
                'next_port' => 'exec',
            ];
        }
        $args = $this->arguments(
            (string) $node['id'],
            (array) ($meta['params'] ?? []),
            $context
        );
        $call = $modelVariable . '->' . $methodName . '(' . implode(', ', $args) . ')';
        if ($returnType === 'void' || $returnType === 'never')
        {
            return [
                'code' => $call . ';',
                'next_port' => 'exec',
                'terminal' => $returnType === 'never',
                'outputs' => [
                    'result' => 'null',
                    'model' => $modelVariable,
                ],
            ];
        }
        return [
            'code' => $variable . ' = ' . $call . ';',
            'next_port' => 'exec',
            'outputs' => [
                'result' => $variable,
                'model' => $continuationExpression,
            ],
        ];
    }
    /**
     * @param array<int, mixed> $parameters
     * @return list<string>
     */
    private function arguments(
        string $nodeId,
        array $parameters,
        CompileContext $context
    ): array {
        $metadata = [];

        foreach ($parameters as $parameter) {
            $item = is_array($parameter)
                ? $parameter
                : ['name' => $parameter, 'default' => ''];
            $name = preg_replace(
                '/[^A-Za-z0-9_]/',
                '',
                (string) ($item['name'] ?? '')
            );
            if ($name === '') {
                continue;
            }

            $metadata[] = [
                'port' => 'param_' . $name,
                'default' => trim((string) ($item['default'] ?? '')),
            ];
        }

        $lastArgument = -1;
        foreach ($metadata as $index => $parameter) {
            $connected = $context->incomingEdges(
                $nodeId,
                (string) $parameter['port']
            ) !== [];
            if ($connected || (string) $parameter['default'] === '') {
                $lastArgument = $index;
            }
        }

        $arguments = [];
        foreach ($metadata as $index => $parameter) {
            if ($index > $lastArgument) {
                break;
            }

            $default = (string) $parameter['default'];
            $arguments[] = $context->inputExpression(
                $nodeId,
                (string) $parameter['port'],
                $default !== '' ? rtrim($default, ';') : 'null'
            );
        }

        return $arguments;
    }

}
