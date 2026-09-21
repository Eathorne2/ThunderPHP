<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Project;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PluginCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture')
        {
            return [];
        }
        return [
            'meta' => is_array($node['data'] ?? null) ? $node['data'] : [],
        ];
    }
}
