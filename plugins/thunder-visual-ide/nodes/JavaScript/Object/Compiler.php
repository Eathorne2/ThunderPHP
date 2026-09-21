<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ObjectCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $count = max(0, min(30, (int) ($data['property_count'] ?? 0)));
        $keys = preg_split('/[\r\n,]+/', (string) ($data['keys'] ?? '')) ?: [];
        $parts = [];
        for ($i = 1; $i <= $count; $i++) {
            $key = trim((string) ($keys[$i - 1] ?? ('property_' . $i)));
            if ($key === '') $key = 'property_' . $i;
            $value = $context->inputJavaScriptExpression((string) $node['id'], 'property_' . $i, 'null');
            $parts[] = $context->javascriptExport($key) . ': ' . $value;
        }
        $expression = '{' . implode(', ', $parts) . '}';
        return ['expression' => $expression, 'outputs' => ['value' => $expression]];
    }
}
