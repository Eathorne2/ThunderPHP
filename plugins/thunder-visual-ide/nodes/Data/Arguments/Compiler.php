<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ArgumentsCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $count = max(0, min(20, (int)($node['data']['param_count'] ?? 0)));
        $args = [];
        for ($i = 1; $i <= $count; $i++)
        {
            $args[] = $context->inputExpression((string)$node['id'], 'arg_' . $i, 'null');
        }
        $e = implode(', ', $args);
        return [
            'arguments' => $e,
            'expression' => $e,
            'outputs' => [
                'arguments' => $e,
            ],
        ];
    }
}
