<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class DelayCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $fallback = (string) max(0, (int) ($node['data']['milliseconds'] ?? 250));
        return ['control' => 'delay', 'delay' => $context->inputJavaScriptExpression((string) $node['id'], 'milliseconds', $fallback), 'next_port' => 'exec'];
    }
}
