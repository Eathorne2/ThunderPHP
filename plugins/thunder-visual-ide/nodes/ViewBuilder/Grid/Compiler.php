<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class GridCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $children = $context->compileViewChildren((string)$node['id']);
        return [
            'html' => '<div' . ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::classes($d, 'thv-grid'),
                'style' => ViewNodeSupport::mergeStyles(
                    '--thv-gap:' . trim((string) ($d['gap'] ?? '1rem')),
                    ViewNodeSupport::inlineStyle($d)
                ),
            ]) . ">\n" . $context->indent($children) . "\n</div>",
        ];
    }
}
