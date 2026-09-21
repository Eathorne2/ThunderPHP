<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ValidationErrorCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $errorsVariable = ViewNodeSupport::variableName(
            (string) ($data['errors_variable'] ?? 'errors'),
            'errors'
        );
        $field = var_export((string) ($data['field'] ?? 'field'), true);
        $errorAttributes = ViewNodeSupport::attributes([
            'class' => trim('thv-error ' . (string) ($data['css_class'] ?? '')),
            'style' => ViewNodeSupport::inlineStyle($data),
        ]);
        $errorExpression = '$' . $errorsVariable . '[' . $field . ']';

        $errorHtml = '<?php if (!empty('
            . $errorExpression
            . ')): ?>'
            . '<div'
            . $errorAttributes
            . '><?= esc(is_array('
            . $errorExpression
            . ') ? reset('
            . $errorExpression
            . ') : '
            . $errorExpression
            . ') ?></div>'
            . '<?php endif; ?>';

        $wrapperAttributes = ViewNodeSupport::attributes([
            'class' => ViewNodeSupport::widthClasses($data),
        ]);

        return [
            'html' => '<div'
                . $wrapperAttributes
                . '>'
                . $errorHtml
                . '</div>',
        ];
    }
}
