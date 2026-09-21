<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ContainerCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $tag = ViewNodeSupport::tag((string)($d['tag'] ?? 'div'));
        $base = (string)($d['layout'] ?? 'block') === 'grid' ? 'thv-grid' : '';
        $class = ViewNodeSupport::classes($d, $base);
        $children = $context->compileViewChildren((string)$node['id']);
        return [
            'html' => '<' . $tag . ViewNodeSupport::attributes([
                'class' => $class,
                'style' => ViewNodeSupport::inlineStyle($d),
            ]) . ">\n" . $context->indent($children) . "\n</{$tag}>",
        ];
    }
}
