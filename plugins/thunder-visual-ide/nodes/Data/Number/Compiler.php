<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class NumberCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $v = $node['data']['value'] ?? 0;
        $e = is_numeric($v) ? (string)(0 + $v) : '0';
        return [
            'expression' => $e,
            'outputs' => [
                'value' => $e,
            ],
        ];
    }
}
