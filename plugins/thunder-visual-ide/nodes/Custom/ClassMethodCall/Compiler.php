<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Custom;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ClassMethodCallCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $classNodeId = (string)($data['class_node_id'] ?? '');
        $methodName = preg_replace('/[^A-Za-z0-9_]/', '', (string)($data['method_name'] ?? ''));
        $variable = $context->variableForNode($node, 'result');
        $method = $context->classMethod($classNodeId, $methodName) ?? [];
        $returnType = strtolower(trim((string)($method['return_type'] ?? '')));
        if ($context->mode() === 'expression')
        {
            return [
                'expression' => $returnType === 'void' || $returnType === 'never' ? 'null' : $variable,
                'outputs' => [
                    'result' => $variable,
                ],
            ];
        }
        if ($classNodeId === '' || $methodName === '')
        {
            $context->addError('Class Method Call requires a class and method.');
            return [
                'code' => '// Invalid class method call.',
                'next_port' => 'exec',
            ];
        }
        $classNode = $context->node($classNodeId, 'main');
        $class = $context->studly((string)($classNode['data']['class_name'] ?? 'CustomClass'));
        $args = $this->arguments(
            (string) $node['id'],
            (array) ($method['params'] ?? []),
            $context
        );
        if (!empty($method['static']))
        {
            $call = $class . '::' . $methodName . '(' . implode(', ', $args) . ')';
        } else
        {
            $object = $context->inputExpression((string)$node['id'], 'object', 'null');
            if ($object === 'null')
            {
                $context->addError("Class Method Call {$class}::{$methodName} requires an object input.");
            }
            $call = $object . '->' . $methodName . '(' . implode(', ', $args) . ')';
        }
        if ($returnType === 'void' || $returnType === 'never')
        {
            return [
                'code' => $call . ';',
                'next_port' => 'exec',
                'terminal' => $returnType === 'never',
                'outputs' => [
                    'result' => 'null',
                ],
            ];
        }
        return [
            'code' => $variable . ' = ' . $call . ';',
            'next_port' => 'exec',
            'outputs' => [
                'result' => $variable,
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
