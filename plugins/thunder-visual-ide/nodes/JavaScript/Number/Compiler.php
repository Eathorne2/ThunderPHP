<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class NumberCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $expression = $this->expression($node, $context);
        return ['expression' => $expression, 'outputs' => ['value' => $expression]];
    }

    private function expression(array $node, CompileContext $context): string
    {
        return is_numeric($node['data']['value'] ?? null) ? (string) (0 + $node['data']['value']) : '0';
    }
}
