<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class MaximumCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $count = max(2, min(30, (int) ($node['data']['input_count'] ?? 2)));
        $values = [];
        for ($index = 1; $index <= $count; $index++) {
            $values[] = $context->inputExpression((string) $node['id'], 'value_' . $index, '0');
        }
        $expression = 'max([' . implode(', ', $values) . '])';
        return ['expression' => $expression, 'outputs' => ['value' => $expression]];
    }
}
