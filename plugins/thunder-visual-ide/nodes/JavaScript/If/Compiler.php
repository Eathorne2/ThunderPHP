<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class IfCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        return ['control' => 'if', 'condition' => $context->inputJavaScriptExpression((string) $node['id'], 'condition', 'false'), 'true_port' => 'true', 'false_port' => 'false'];
    }
}
