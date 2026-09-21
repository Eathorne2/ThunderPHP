<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Custom;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ClassInstanceCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $classNodeId = (string)($data['class_node_id'] ?? '');
        $variable = $context->variableForNode($node, 'object');
        if ($context->mode() === 'expression')
        {
            return [
                'expression' => $variable,
                'outputs' => [
                    'object' => $variable,
                ],
            ];
        }
        if ($classNodeId === '')
        {
            $context->addError('New Class Instance requires a Class Definition.');
            return [
                'code' => '// Missing class definition.',
                'next_port' => 'exec',
            ];
        }
        $classNode = $context->node($classNodeId, 'main');
        $class = $context->studly((string)($classNode['data']['class_name'] ?? 'CustomClass'));
        $constructor = $context->classMethod($classNodeId, '__construct') ?? [];
        $args = $this->arguments(
            (string) $node['id'],
            (array) ($constructor['params'] ?? []),
            $context
        );
        return [
            'code' => $variable . ' = new ' . $class . '(' . implode(', ', $args) . ');',
            'next_port' => 'exec',
            'outputs' => [
                'object' => $variable,
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
