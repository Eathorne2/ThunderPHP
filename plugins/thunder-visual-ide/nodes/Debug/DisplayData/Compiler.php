<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Debug;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class DisplayDataCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = $context->inputExpression(
            (string) $node['id'],
            'data',
            'null'
        );

        return [
            'code' => 'dd(' . $data . ');',
            'next_port' => 'exec',
        ];
    }
}
