<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class StringCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $e = $context->export((string)($node['data']['value'] ?? ''));
        return [
            'expression' => $e,
            'outputs' => [
                'value' => $e,
            ],
        ];
    }
}
