<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ButtonCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $style = (string) ($data['style'] ?? 'primary');
        $base = $style === 'custom'
            ? 'thv-button'
            : 'thv-button thv-button--' . $style;
        $controlHtml = '<button' . ViewNodeSupport::attributes([
            'type' => (string) ($data['button_type'] ?? 'submit'),
            'name' => (string) ($data['name'] ?? ''),
            'value' => (string) ($data['value'] ?? ''),
            'class' => trim($base . ' ' . (string) ($data['css_class'] ?? '')),
            'style' => ViewNodeSupport::inlineStyle($data),
        ]) . '>' . htmlspecialchars(
            (string) ($data['text'] ?? 'Submit'),
            ENT_QUOTES,
            'UTF-8'
        ) . '</button>';
        $wrapperAttributes = ViewNodeSupport::attributes([
            'class' => trim(
                ViewNodeSupport::widthClasses($data)
                . ' '
                . $context->currentFormThemeScopeClass()
            ),
            'data-thv-form-theme' => $context->currentFormThemeScopeClass(),
        ]);
        $fallback = '<div' . $wrapperAttributes . '>' . $controlHtml . '</div>';

        return ['html' => ViewNodeSupport::themedComponent(
            $context,
            'button',
            [
                'wrapper_attributes' => $wrapperAttributes,
                'label_html' => '',
                'control_html' => $controlHtml,
                'children_html' => '',
            ],
            $fallback
        )];
    }
}
