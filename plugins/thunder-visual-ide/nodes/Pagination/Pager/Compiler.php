<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Pagination;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PagerCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $variable = $context->variableForNode($node, 'pager');
        $outputs = [
            'pager' => $variable,
            'limit' => $variable . '->limit()',
            'offset' => $variable . '->offset()',
            'current_page' => $variable . '->current_page()',
        ];
        if ($context->mode() === 'expression') {
            $port = $context->outputPort() ?: 'pager';
            return ['expression' => $outputs[$port] ?? $variable, 'outputs' => $outputs];
        }
        $limit = max(1, (int) ($node['data']['items_per_page'] ?? 20));
        $extras = max(0, (int) ($node['data']['extra_links'] ?? 2));
        $total = $context->inputExpression((string) $node['id'], 'total_rows', '0');
        return [
            'code' => $variable . ' = new \\Core\\Pager(' . $limit . ', ' . $extras . ', ' . $total . ');',
            'next_port' => 'exec',
            'outputs' => $outputs,
        ];
    }
}
