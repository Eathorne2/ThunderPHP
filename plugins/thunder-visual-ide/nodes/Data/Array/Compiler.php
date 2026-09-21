<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ArrayCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $d = $node['data'] ?? [];
        $count = max(0, min(30, (int)($d['item_count'] ?? 0)));
        $keys = array_map('trim', explode(',', (string)($d['keys'] ?? '')));
        $items = [];
        for ($i = 1; $i <= $count; $i++)
        {
            $v = $context->inputExpression((string)$node['id'], 'item_' . $i, 'null');
            if (($d['mode'] ?? 'associative') === 'associative')
            {
                $key = $keys[$i - 1] ?? ('item_' . $i);
                $items[] = $context->export($key) . ' => ' . $v;
            } else
            {
                $items[] = $v;
            }
        }
        $e = '[' . implode(', ', $items) . ']';
        return [
            'expression' => $e,
            'outputs' => [
                'value' => $e,
            ],
        ];
    }
}
