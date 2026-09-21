<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class CheckboxCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $checkedPath = ViewNodeSupport::path(
            (string) ($data['checked_path'] ?? ''),
            ''
        );
        $checkedAttribute = $checkedPath !== ''
            ? ' <?= !empty(\get_nested_value(get_defined_vars(), '
                . var_export($checkedPath, true)
                . ")) ? 'checked' : '' ?>"
            : '';
        $controlHtml = '<input' . ViewNodeSupport::attributes([
            'type' => 'checkbox',
            'name' => (string) ($data['name'] ?? 'agree'),
            'aria-describedby' => ViewNodeSupport::inlineErrorId($data, $node, $id),
            'value' => (string) ($data['value'] ?? '1'),
            'required' => (bool) ($data['required'] ?? false),
            'class' => ViewNodeSupport::controlClasses($data),
            'style' => ViewNodeSupport::inlineStyle($data, 'control_style'),
        ]) . $checkedAttribute . '>';
        $label = trim((string) ($data['label'] ?? ''));
        $labelHtml = $label !== ''
            ? '<span class="thv-label">'
                . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
                . '</span>'
            : '';
        $errorHtml = ViewNodeSupport::inlineErrorHtml($data, $node, $id);
        $wrapperAttributes = ViewNodeSupport::themedWrapperAttributes(
            $data,
            $context,
            'thv-field',
            'thv-checkbox'
        );
        $fallback = '<div' . $wrapperAttributes . '>'
            . $controlHtml . $labelHtml . $errorHtml . '</div>';

        return ['html' => ViewNodeSupport::themedComponent(
            $context,
            'checkbox',
            [
                'wrapper_attributes' => $wrapperAttributes,
                'label_html' => $labelHtml,
                'control_html' => $controlHtml,
                'error_html' => $errorHtml,
                'children_html' => '',
            ],
            $fallback
        )];
    }
}
