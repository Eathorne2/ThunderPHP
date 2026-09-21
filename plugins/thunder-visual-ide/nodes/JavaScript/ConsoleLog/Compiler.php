<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ConsoleLogCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $level = (string) ($node['data']['level'] ?? 'log');
        if (!in_array($level, ['log','info','warn','error','debug'], true)) $level = 'log';
        $value = $context->inputJavaScriptExpression((string) $node['id'], 'value', 'null');
        return ['code' => 'console.' . $level . '(' . $value . ');', 'next_port' => 'exec'];
    }
}
