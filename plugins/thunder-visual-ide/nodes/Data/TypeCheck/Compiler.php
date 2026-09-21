<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class TypeCheckCompiler implements NodeCompilerInterface
{
    private const CHECKS = [
        'is_array',
        'is_bool',
        'is_callable',
        'is_countable',
        'is_float',
        'is_int',
        'is_iterable',
        'is_null',
        'is_numeric',
        'is_object',
        'is_resource',
        'is_scalar',
        'is_string',
    ];

    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $check = (string) ($data['check'] ?? 'is_numeric');
        if (!in_array($check, self::CHECKS, true)) {
            $context->addWarning('Type Check used an unsupported predicate; is_numeric was used instead.');
            $check = 'is_numeric';
        }

        $value = $context->inputExpression((string) ($node['id'] ?? ''), 'value', 'null');
        $expression = $check . '(' . $value . ')';

        return [
            'expression' => $expression,
            'outputs' => ['result' => $expression],
        ];
    }
}
