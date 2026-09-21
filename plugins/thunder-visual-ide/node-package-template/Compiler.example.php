<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Custom;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ExampleCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null)
            ? $node['data']
            : [];
        $value = (string) ($data['value'] ?? '');
        $expression = $context->export($value);

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }
}
