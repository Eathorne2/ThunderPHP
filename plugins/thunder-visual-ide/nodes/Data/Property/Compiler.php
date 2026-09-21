<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PropertyCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $source = $context->inputExpression(
            (string) $node['id'],
            'source',
            'null'
        );
        $property = (string) ($data['property'] ?? 'id');

        if (($data['mode'] ?? 'object') === 'array') {
            $expression = '(' . $source . ')['
                . $context->export($property)
                . ']';
        } else {
            $safeProperty = preg_replace(
                '/[^A-Za-z0-9_]/',
                '',
                $property
            );
            $expression = "({$source})->{$safeProperty}";
        }

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }
}
