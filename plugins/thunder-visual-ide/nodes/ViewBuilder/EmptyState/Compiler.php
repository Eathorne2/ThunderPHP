<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class EmptyStateCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $d = is_array($node['data'] ?? null) ? $node['data'] : [];
        $path = ViewNodeSupport::path((string)($d['path'] ?? 'items'));
        $content = (string)($d['content'] ?? '');
        if (empty($d['allow_html']))
        {
            $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        }
        $html = '<?php if (empty(\get_nested_value(get_defined_vars(), '
            . var_export($path, true)
            . '))): ?>'
            . $content
            . '<?php endif; ?>';
        return [
            'html' => '<div' . ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::classes($d, 'thv-empty-state'),
                'style' => ViewNodeSupport::inlineStyle($d),
            ]) . '>' . $html . '</div>',
        ];
    }
}
