<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class LoopCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $collectionPath = ViewNodeSupport::path(
            (string) ($data['collection_path'] ?? 'items')
        );
        $itemVariable = ViewNodeSupport::variableName(
            (string) ($data['item_name'] ?? 'item')
        );
        $indexVariable = ViewNodeSupport::variableName(
            (string) ($data['index_name'] ?? 'index'),
            'index'
        );
        $compiledChildren = $context->compileViewChildren((string) $node['id']);
        $loopAssets = ViewNodeSupport::extractLoopAssets($compiledChildren);
        $childrenHtml = (string) ($loopAssets['html'] ?? '');
        $styleHtml = implode("\n", (array) ($loopAssets['styles'] ?? []));
        $scriptHtml = implode("\n", (array) ($loopAssets['scripts'] ?? []));
        $emptyHtml = (string) ($data['empty_html'] ?? '');
        $collectionExpression = '\get_nested_value(get_defined_vars(), '
            . var_export($collectionPath, true)
            . ')';

        $loopHtml = implode("\n", [
            '<?php $__tvi_collection = '
                . $collectionExpression
                . '; ?>',
            '<?php if (is_iterable($__tvi_collection)'
                . ' && !empty($__tvi_collection)): ?>',
            '<?php foreach ($__tvi_collection as $'
                . $indexVariable
                . ' => $'
                . $itemVariable
                . '): ?>',
            $childrenHtml,
            '<?php endforeach; ?>',
            '<?php else: ?>',
            $emptyHtml,
            '<?php endif; ?>',
        ]);

        $output = [];
        if ($styleHtml !== '') {
            $output[] = $styleHtml;
        }

        if (ViewNodeSupport::hasExplicitWrapper($data)) {
            $wrapperAttributes = ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::classes($data),
                'style' => ViewNodeSupport::inlineStyle($data),
            ]);
            $output[] = '<div'
                . $wrapperAttributes
                . '>'
                . $loopHtml
                . '</div>';
        } else {
            $output[] = $loopHtml;
        }

        if ($scriptHtml !== '') {
            $output[] = $scriptHtml;
        }

        return [
            'html' => implode("\n", $output),
        ];
    }
}
