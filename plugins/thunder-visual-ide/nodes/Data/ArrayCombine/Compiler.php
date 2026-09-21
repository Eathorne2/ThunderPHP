<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ArrayCombineCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $count = max(2, min(20, (int)($data['array_count'] ?? 2)));
        $parts = [];
        for ($i = 1; $i <= $count; $i++)
        {
            $parts[] = $context->inputExpression((string)$node['id'], 'array_' . $i, '[]');
        }
        $fn = ($data['mode'] ?? 'replace') === 'merge' ? 'array_merge' : 'array_replace';
        $expr = $fn . '(' . implode(', ', $parts) . ')';
        return [
            'expression' => $expr,
            'outputs' => [
                'value' => $expr,
            ],
        ];
    }
}
