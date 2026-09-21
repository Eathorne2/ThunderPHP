<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Pagination;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PagerValueCompiler implements NodeCompilerInterface
{
    private const METHODS = [
        'render', 'current_page', 'prev_page', 'next_page', 'has_prev', 'has_next',
        'showing_from', 'showing_to', 'summary_text', 'limit', 'offset',
    ];

    public function compile(array $node, CompileContext $context): array
    {
        $method = (string) ($node['data']['method'] ?? 'render');
        if (!in_array($method, self::METHODS, true)) {
            $method = 'render';
        }
        $pager = $context->inputExpression((string) $node['id'], 'pager', 'null');
        $expression = '(' . $pager . ')->' . $method . '()';
        return ['expression' => $expression, 'outputs' => ['value' => $expression]];
    }
}
