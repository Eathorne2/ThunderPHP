<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ArrayPushCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $nodeId = (string) ($node['id'] ?? '');
        $array = $context->inputExpression($nodeId, 'array', '[]');
        $value = $context->inputExpression($nodeId, 'value', 'null');
        $expression = '(static function ($items, $value) { $items = (array) $items; $items[] = $value; return $items; })('
            . $array . ', ' . $value . ')';

        return [
            'expression' => $expression,
            'outputs' => [
                'result' => $expression,
            ],
        ];
    }
}
