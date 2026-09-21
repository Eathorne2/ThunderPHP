<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class IfCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $path = ViewNodeSupport::path((string)($d['path'] ?? 'value'));
        $value = '\get_nested_value(get_defined_vars(), ' . var_export($path, true) . ')';
        $condition = match ((string)($d['condition'] ?? 'truthy'))
        {
            'empty' => 'empty(' . $value . ')',
            'not_empty' => '!empty(' . $value . ')',
            default => '!empty(' . $value . ')'
        };
        $yes = $context->compileViewChildren((string)$node['id'], 'true');
        $no = $context->compileViewChildren((string)$node['id'], 'false');
        $html = '<?php if (' . $condition . '): ?>' . "\n" . $yes . "\n<?php else: ?>\n" . $no . "\n<?php endif; ?>";
        return [
            'html' => '<div' . ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::widthClasses($d),
            ]) . '>' . $html . '</div>',
        ];
    }
}
