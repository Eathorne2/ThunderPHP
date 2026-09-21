<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class FormCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $children = $context->compileViewChildren((string) $node['id']);
        $wrapperAttributes = ViewNodeSupport::attributes([
            'action' => (string) ($data['action'] ?? ''),
            'method' => strtolower((string) ($data['method'] ?? 'POST')),
            'enctype' => (string) ($data['enctype'] ?? ''),
            'class' => ViewNodeSupport::classes(
                $data,
                'thv-grid',
                'thv-form',
                $context->currentFormThemeScopeClass()
            ),
            'data-thv-form-theme' => $context->currentFormThemeScopeClass(),
            'style' => ViewNodeSupport::mergeStyles(
                '--thv-gap:' . trim((string) ($data['gap'] ?? '1rem')),
                ViewNodeSupport::inlineStyle($data)
            ),
        ]);
        $fallback = "<form{$wrapperAttributes}>\n"
            . $context->indent($children)
            . "\n</form>";

        return [
            'html' => ViewNodeSupport::themedComponent(
                $context,
                'form',
                [
                    'wrapper_attributes' => $wrapperAttributes,
                    'label_html' => '',
                    'control_html' => '',
                    'children_html' => $context->indent($children),
                ],
                $fallback
            ),
            'component_name' => (string) ($data['component_name'] ?? ''),
        ];
    }
}
