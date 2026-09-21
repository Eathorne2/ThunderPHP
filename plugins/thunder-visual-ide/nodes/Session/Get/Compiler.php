<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Session;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class SessionGetCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('session');
        $e = '$session->get(' . $context->export((string)($node['data']['key'] ?? '')) . ')';
        return [
            'expression' => $e,
            'outputs' => [
                'value' => $e,
            ],
        ];
    }
}
