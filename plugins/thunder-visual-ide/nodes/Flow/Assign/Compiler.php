<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Flow;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class AssignCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $name = preg_replace('/[^A-Za-z0-9_]/', '', (string)($node['data']['variable_name'] ?? 'value')) ? : 'value';
        $var = '$' . $name;
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
            'code' => $var . ' = ' . $context->inputExpression((string)$node['id'], 'value', 'null') . ';',
            'next_port' => 'exec',
            'outputs' => [
                'result' => $var,
            ],
        ];
    }
}
