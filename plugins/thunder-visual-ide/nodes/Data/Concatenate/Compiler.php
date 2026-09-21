<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ConcatenateCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $count = max(2, min(30, (int) ($data['input_count'] ?? 2)));
        $separator = (string) ($data['separator'] ?? '');
        $values = [];

        for ($index = 1; $index <= $count; $index++) {
            $values[] = $context->inputExpression(
                (string) ($node['id'] ?? ''),
                'value_' . $index,
                "''"
            );
        }

        $expression = sprintf(
            "implode(%s, array_map(static fn (mixed \$value): string => \$value === null ? '' : ((is_scalar(\$value) || \$value instanceof \\Stringable) ? (string) \$value : (json_encode(\$value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '')), [%s]))",
            $context->export($separator),
            implode(', ', $values)
        );

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }
}
