<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class VariableCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $path = ViewNodeSupport::path((string)($d['path'] ?? 'value'));
        $fallback = var_export((string)($d['fallback'] ?? ''), true);
        $expr = '\get_nested_value(get_defined_vars(), ' . var_export($path, true) . ') ?? ' . $fallback;
        $output = !empty($d['escape']) ? '<?= esc(' . $expr . ') ?>' : '<?= ' . $expr . ' ?>';
        $tag = (string)($d['wrapper_tag'] ?? 'span');
        if ($tag === 'none')
        {
            return [
                'html' => $output,
            ];
        }
        $tag = ViewNodeSupport::tag($tag, 'span');
        return [
            'html' => '<' . $tag . ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::classes($d),
                'style' => ViewNodeSupport::inlineStyle($d),
            ]) . '>' . $output . '</' . $tag . '>',
        ];
    }
}
