<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Session;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class SessionSetCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('session');

        $key = $context->export(
            (string) ($node['data']['key'] ?? '')
        );
        $value = $context->inputExpression(
            (string) $node['id'],
            'value',
            'null'
        );

        return [
            'code' => "\$session->set({$key}, {$value});",
            'next_port' => 'exec',
        ];
    }
}
