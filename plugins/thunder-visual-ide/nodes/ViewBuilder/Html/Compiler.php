<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class HtmlCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $content = (string)($d['content'] ?? '');
        if (!ViewNodeSupport::hasExplicitWrapper($d)) {
            return ['html' => $content];
        }

        return [
            'html' => '<div' . ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::classes($d),
                'style' => ViewNodeSupport::inlineStyle($d),
            ]) . '>' . $content . '</div>',
        ];
    }
}
