<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class QueryElementCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $selector = JavaScriptNodeSupport::targetSelector($node, $context);
        if ($selector === '') {
            $context->addWarning('Query Element has no selector.');
            $expression = 'null';
        } elseif (!empty($node['data']['all'])) {
            $expression = 'Array.from(document.querySelectorAll(' . $context->javascriptExport($selector) . '))';
        } else {
            $expression = 'document.querySelector(' . $context->javascriptExport($selector) . ')';
        }
        return ['expression' => $expression, 'outputs' => ['element' => $expression]];
    }
}
