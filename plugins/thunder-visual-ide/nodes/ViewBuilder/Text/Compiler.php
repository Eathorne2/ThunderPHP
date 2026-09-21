<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class TextCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $text = htmlspecialchars((string)($data['text'] ?? ''), ENT_QUOTES, 'UTF-8');
        $tag = (string)($data['wrapper_tag'] ?? 'p');
        if ($tag === 'none')
        {
            return [
                'html' => $text,
            ];
        }
        $tag = ViewNodeSupport::tag($tag, 'p');
        return [
            'html' => '<' . $tag . ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::classes($data),
                'style' => ViewNodeSupport::inlineStyle($data),
            ]) . '>' . $text . '</' . $tag . '>',
        ];
    }
}
