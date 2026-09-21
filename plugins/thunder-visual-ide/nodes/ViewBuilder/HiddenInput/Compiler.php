<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class HiddenInputCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $path = ViewNodeSupport::path((string)($d['value_path'] ?? ''), '');
        $value = $path !== ''
            ? '<?= esc(\get_nested_value(get_defined_vars(), '
                . var_export($path, true)
                . ') ?? \'\') ?>'
            : htmlspecialchars(
                (string) ($d['value'] ?? ''),
                ENT_QUOTES,
                'UTF-8'
            );
        return [
            'html' => '<input type="hidden" name="' . htmlspecialchars((string)($d['name'] ?? 'id'), ENT_QUOTES,
                'UTF-8') . '" value="' . $value . '">',
        ];
    }
}
