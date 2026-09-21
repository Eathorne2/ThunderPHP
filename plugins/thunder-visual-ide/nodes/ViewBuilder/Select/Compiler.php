<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class SelectCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $name = (string) ($data['name'] ?? 'status');
        $id = 'thv-' . preg_replace('/[^a-z0-9_-]/i', '-', $name)
            . '-' . substr((string) $node['id'], -6);
        $selectedPath = ViewNodeSupport::path(
            (string) ($data['selected_path'] ?? ''),
            ''
        );

        $optionsHtml = '';
        foreach (ViewNodeSupport::options((string) ($data['options'] ?? '')) as $option) {
            $selectedAttribute = $this->selectedAttribute($selectedPath, $option['value']);
            $optionsHtml .= sprintf(
                '<option value="%s"%s>%s</option>',
                htmlspecialchars($option['value'], ENT_QUOTES, 'UTF-8'),
                $selectedAttribute,
                htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8')
            );
        }

        $controlHtml = '<select' . ViewNodeSupport::attributes([
            'id' => $id,
            'name' => $name,
            'aria-describedby' => ViewNodeSupport::inlineErrorId($data, $node, $id),
            'required' => (bool) ($data['required'] ?? false),
            'class' => ViewNodeSupport::controlClasses($data),
            'style' => ViewNodeSupport::inlineStyle($data, 'control_style'),
        ]) . '>' . $optionsHtml . '</select>';
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

        return ['html' => ViewNodeSupport::themedComponent(
            $context,
            'select',
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

    private function selectedAttribute(string $selectedPath, string $optionValue): string
    {
        if ($selectedPath === '') {
            return '';
        }

        return ' <?= (string)\get_nested_value(get_defined_vars(), '
            . var_export($selectedPath, true)
            . ') === '
            . var_export($optionValue, true)
            . " ? 'selected' : '' ?>";
    }
}
