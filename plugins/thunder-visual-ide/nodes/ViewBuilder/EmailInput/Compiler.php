<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class EmailInputCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $name = (string) ($data['name'] ?? 'email');
        $id = 'thv-' . preg_replace('/[^a-z0-9_-]/i', '-', $name)
            . '-' . substr((string) $node['id'], -6);
        $path = ViewNodeSupport::path((string) ($data['value_path'] ?? ''), '');
        $value = $path !== ''
            ? '<?= esc(\get_nested_value(get_defined_vars(), '
                . var_export($path, true)
                . ') ?? \'\') ?>'
            : '';

        $controlHtml = '<input' . ViewNodeSupport::attributes([
            'type' => 'email',
            'id' => $id,
            'name' => $name,
            'aria-describedby' => ViewNodeSupport::inlineErrorId($data, $node, $id),
            'placeholder' => (string) ($data['placeholder'] ?? ''),
            'required' => (bool) ($data['required'] ?? false),
            'autocomplete' => (string) ($data['autocomplete'] ?? ''),
            'class' => ViewNodeSupport::controlClasses($data),
            'style' => ViewNodeSupport::inlineStyle($data, 'control_style'),
        ]) . ($value !== '' ? ' value="' . $value . '"' : '') . '>';

        $label = trim((string) ($data['label'] ?? ''));
        $labelHtml = $label !== ''
            ? '<label for="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
                . '</label>'
            : '';
        $errorHtml = ViewNodeSupport::inlineErrorHtml($data, $node, $id);
        $wrapperAttributes = ViewNodeSupport::themedWrapperAttributes(
            $data,
            $context,
            'thv-field'
        );
        $fallback = '<div' . $wrapperAttributes . '>'
            . $labelHtml . $controlHtml . $errorHtml . '</div>';

        return [
            'html' => ViewNodeSupport::themedComponent(
                $context,
                'email_input',
                [
                    'wrapper_attributes' => $wrapperAttributes,
                    'label_html' => $labelHtml,
                    'control_html' => $controlHtml,
                'error_html' => $errorHtml,
                    'error_html' => $errorHtml,
                    'children_html' => '',
                ],
                $fallback
            ),
        ];
    }
}
