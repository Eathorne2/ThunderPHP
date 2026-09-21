<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ExpressionCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $expression = trim((string) ($node['data']['expression'] ?? 'null'));
        $expression = rtrim($expression, "; \t\r\n");
        if ($expression === '') $expression = 'null';
        return ['expression' => $expression, 'outputs' => ['value' => $expression]];
    }
}
