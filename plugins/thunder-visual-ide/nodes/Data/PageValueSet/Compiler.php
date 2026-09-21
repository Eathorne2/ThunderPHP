<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PageValueSetCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $key = $context->export(
            (string) ($node['data']['key'] ?? '')
        );
        $value = $context->inputExpression(
            (string) $node['id'],
            'value',
            'null'
        );

        return [
            'code' => "set_value({$key}, {$value});",
            'next_port' => 'exec',
        ];
    }
}
