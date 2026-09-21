<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FormDataCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $form = $context->inputJavaScriptExpression((string) $node['id'], 'form', 'null');
        $selector = $context->javascriptExport(trim((string) ($node['data']['form_selector'] ?? 'form')) ?: 'form');
        $expression = '(function () { const form = ' . $form . ' || document.querySelector(' . $selector . '); return form ? new FormData(form) : new FormData(); })()';
        return ['expression' => $expression, 'outputs' => ['value' => $expression]];
    }
}
