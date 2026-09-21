<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class NullCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        return [
            'expression' => 'null',
            'outputs' => [
                'value' => 'null',
            ],
        ];
    }
}
