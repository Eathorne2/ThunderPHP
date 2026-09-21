<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Request;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class IsAjaxCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('request');
        $expression = '$request->is_ajax()';
        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }
}
