<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Logic;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class BooleanGroupCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $operator = strtoupper((string) ($node['data']['operator'] ?? 'AND'));
        $phpOperator = $operator === 'OR' ? '||' : '&&';
        $conditionCount = max(
            2,
            min(20, (int) ($node['data']['condition_count'] ?? 2))
        );
        $conditions = [];

        for ($index = 1; $index <= $conditionCount; $index++) {
            $conditions[] = $context->inputExpression(
                (string) $node['id'],
                'condition_' . $index,
                'false'
            );
        }

        $expression = '(' . implode(") {$phpOperator} (", $conditions) . ')';

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }
}
