<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Flow;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ReturnCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $code = !empty($node['data']['return_value']) ? 'return ' . $context->inputExpression((string)$node['id'],
            'value', 'null') . ';' : 'return;';
        return [
            'code' => $code,
            'terminal' => true,
        ];
    }
}
