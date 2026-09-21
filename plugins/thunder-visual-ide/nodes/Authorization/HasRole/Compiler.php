<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Authorization;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class HasRoleCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $role = $context->export((string) ($node['data']['role'] ?? ''));
        $expression = "contains_role({$role})";

        return [
            'expression' => $expression,
            'outputs' => ['value' => $expression],
        ];
    }
}
