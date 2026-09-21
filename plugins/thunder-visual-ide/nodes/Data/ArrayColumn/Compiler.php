<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ArrayColumnCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $rows = $context->inputExpression((string) ($node['id'] ?? ''), 'rows', '[]');
        $path = array_values(array_filter(array_map('trim', explode('.', (string) ($data['column'] ?? 'value'))), static fn (string $part): bool => $part !== ''));
        $preserve = !array_key_exists('preserve_missing', $data) || !empty($data['preserve_missing']);
        $expression = '(static function ($rows): array {'
            . ' $result = [];'
            . ' if (!is_array($rows) && !$rows instanceof \\Traversable) { return $result; }'
            . ' foreach ($rows as $row) {'
            . ' $value = $row; $found = true;'
            . ' foreach (' . $context->export($path) . ' as $segment) {'
            . ' if (is_array($value) && array_key_exists($segment, $value)) { $value = $value[$segment]; continue; }'
            . ' if (is_object($value) && (isset($value->{$segment}) || property_exists($value, $segment))) { $value = $value->{$segment}; continue; }'
            . ' $found = false; $value = null; break;'
            . ' }'
            . ($preserve ? ' $result[] = $found ? $value : null;' : ' if ($found) { $result[] = $value; }')
            . ' } return array_values($result);'
            . '})(' . $rows . ')';

        return ['expression' => $expression, 'outputs' => ['values' => $expression]];
    }
}
