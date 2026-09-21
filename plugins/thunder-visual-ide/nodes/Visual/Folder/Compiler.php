<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Visual;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FolderCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        return [];
    }
}
