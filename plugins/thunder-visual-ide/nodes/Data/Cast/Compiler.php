<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class CastCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $value = $context->inputExpression((string) $node['id'], 'value', 'null');
        $type = (string) ($node['data']['target_type'] ?? 'array');

        $expression = match ($type) {
            'object' => "(object) ({$value})",
            'string' => "(string) ({$value})",
            'int' => "(int) ({$value})",
            'float' => "(float) ({$value})",
            'bool' => "(bool) ({$value})",
            'json_to_array' => "(json_decode((string) ({$value}), true) ?: [])",
            'json_to_object' => "json_decode((string) ({$value}))",
            default => "(array) ({$value})",
        };

        return [
            'expression' => $expression,
            'outputs' => ['value' => $expression],
        ];
    }
}
