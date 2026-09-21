<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Custom;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FunctionCallCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $d = $node['data'] ?? [];
        $def = $context->node((string)($d['function_node_id'] ?? ''), 'main');
        $fd = $def['data'] ?? [];
        $name = preg_replace('/[^A-Za-z0-9_]/', '', (string)($fd['function_name'] ?? ''));
        $params = array_values(array_filter(array_map('trim', explode(',', (string)($fd['parameters'] ?? '')))));
        $args = [];
        foreach ($params as $p)
        {
            $args[] = $context->inputExpression((string)$node['id'], 'param_' . preg_replace('/[^A-Za-z0-9_]/',
                '', $p), 'null');
        }
        $var = $context->variableForNode($node, 'result');
        if ($context->mode() === 'expression')
        {
            return [
                'expression' => $var,
                'outputs' => [
                    'result' => $var,
                ],
            ];
        }
        return [
            'code' => $var . ' = ' . $name . '(' . implode(', ', $args) . ');',
            'next_port' => 'exec',
            'outputs' => [
                'result' => $var,
            ],
        ];
    }
}
