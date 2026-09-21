<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ArrayCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $count = max(0, min(30, (int) ($node['data']['item_count'] ?? 0)));
        $items = [];
        for ($i = 1; $i <= $count; $i++) {
            $items[] = $context->inputJavaScriptExpression((string) $node['id'], 'item_' . $i, 'null');
        }
        $expression = '[' . implode(', ', $items) . ']';
        return ['expression' => $expression, 'outputs' => ['value' => $expression]];
    }
}
