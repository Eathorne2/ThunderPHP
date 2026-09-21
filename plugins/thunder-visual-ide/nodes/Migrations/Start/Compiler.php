<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class StartCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        return [];
    }
}
