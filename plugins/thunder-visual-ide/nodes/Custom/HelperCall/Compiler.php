<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Custom;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class HelperCallCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $d = $node['data'] ?? [];
        $name = preg_replace('/[^A-Za-z0-9_\\\\]/', '', (string)($d['function_name'] ?? ''));
        $args = $context->inputArguments((string)$node['id']);
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
        $call = $name . '(' . $args . ')';
        $code = !empty($d['assign_result']) ? $var . ' = ' . $call . ';' : $call . ';';
        return [
            'code' => $code,
            'next_port' => 'exec',
            'outputs' => [
                'result' => $var,
            ],
        ];
    }
}
