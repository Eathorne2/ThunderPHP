<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class CodeCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'javascript_statement') return [];
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $count = max(0, min(20, (int) ($data['input_count'] ?? 0)));
        $names = preg_split('/[\r\n,]+/', (string) ($data['input_names'] ?? '')) ?: [];
        $lines = [];
        for ($i = 1; $i <= $count; $i++) {
            $name = JavaScriptNodeSupport::cleanIdentifier((string) ($names[$i - 1] ?? ''), 'value_' . $i);
            $value = $context->inputJavaScriptExpression((string) $node['id'], 'value_' . $i, 'null');
            $lines[] = 'const ' . $name . ' = ' . $value . ';';
        }
        $code = JavaScriptNodeSupport::stripScriptTags((string) ($data['code'] ?? ''));
        if ($code !== '') $lines[] = $code;
        return ['code' => implode("\n", $lines), 'next_port' => 'exec'];
    }
}
