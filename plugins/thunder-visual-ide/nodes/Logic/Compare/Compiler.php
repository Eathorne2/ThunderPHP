<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Logic;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class CompareCompiler implements NodeCompilerInterface
{
    private const ALLOWED_OPERATORS = [
        '===',
        '!==',
        '==',
        '!=',
        '>',
        '>=',
        '<',
        '<=',
    ];

    public function compile(array $node, CompileContext $context): array
    {
        $operator = (string) ($node['data']['operator'] ?? '===');
        if (!in_array($operator, self::ALLOWED_OPERATORS, true)) {
            $operator = '===';
        }

        $left = $context->inputExpression(
            (string) $node['id'],
            'left',
            'null'
        );
        $right = $context->inputExpression(
            (string) $node['id'],
            'right',
            'null'
        );
        $expression = "({$left} {$operator} {$right})";

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }
}
