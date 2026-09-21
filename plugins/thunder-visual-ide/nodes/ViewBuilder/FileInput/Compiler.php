<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class FileInputCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $name = (string) ($data['name'] ?? 'file');
        $id = 'thv-' . preg_replace('/[^a-z0-9_-]/i', '-', $name)
            . '-' . substr((string) $node['id'], -6);
        $controlHtml = '<input' . ViewNodeSupport::attributes([
            'type' => 'file',
            'id' => $id,
            'name' => $name,
            'aria-describedby' => ViewNodeSupport::inlineErrorId($data, $node, $id),
            'required' => (bool) ($data['required'] ?? false),
            'accept' => (string) ($data['accept'] ?? ''),
            'multiple' => (bool) ($data['multiple'] ?? false),
            'class' => ViewNodeSupport::controlClasses($data),
            'style' => ViewNodeSupport::inlineStyle($data, 'control_style'),
        ]) . '>';
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
            'file_input',
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
