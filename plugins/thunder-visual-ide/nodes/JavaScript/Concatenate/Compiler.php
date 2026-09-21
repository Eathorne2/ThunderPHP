<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ConcatenateCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $count = max(2, min(30, (int) ($node['data']['input_count'] ?? 2)));
        $values = [];
        for ($i = 1; $i <= $count; $i++) {
            $values[] = $context->inputJavaScriptExpression((string) $node['id'], 'value_' . $i, "''");
        }
        $separator = $context->javascriptExport((string) ($node['data']['separator'] ?? ''));
        $expression = '[' . implode(', ', $values) . '].map(function (value) { return value == null ? \'\' : (typeof value === \'string\' ? value : (typeof value === \'object\' ? JSON.stringify(value) : String(value))); }).join(' . $separator . ')';
        return ['expression' => $expression, 'outputs' => ['value' => $expression]];
    }
}
