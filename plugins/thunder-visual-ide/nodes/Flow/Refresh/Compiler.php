<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Flow;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class RefreshCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        return [
            'code' => "redirect(current_url());\nexit;",
            'terminal' => true,
        ];
    }
}
