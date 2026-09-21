<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Authorization;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class HasPermissionCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $permission = $context->export((string) ($node['data']['permission'] ?? ''));
        $expression = "user_can({$permission})";

        return [
            'expression' => $expression,
            'outputs' => ['value' => $expression],
        ];
    }
}
