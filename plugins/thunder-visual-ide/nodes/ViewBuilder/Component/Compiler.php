<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ComponentCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $children = $context->compileViewChildren((string)$node['id']);
        $tag = (string)($d['wrapper_tag'] ?? 'div');
        if ($tag === 'none')
        {
            $html = $children;
        } else
        {
            $tag = ViewNodeSupport::tag($tag);
            $html = '<' . $tag . ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::classes($d),
                'style' => ViewNodeSupport::inlineStyle($d),
            ]) . ">\n" . $context->indent($children) . "\n</{$tag}>";
        }
        return [
            'html' => $html,
            'component_name' => (string)($d['component_name'] ?? ''),
        ];
    }
}
