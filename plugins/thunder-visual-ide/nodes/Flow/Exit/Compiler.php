<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Flow;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ExitCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $function = (string) ($data['function'] ?? 'exit');
        $function = $function === 'die' ? 'die' : 'exit';

        if (empty($data['use_value'])) {
            return [
                'code' => $function . ';',
                'terminal' => true,
            ];
        }

        $value = $context->inputExpression(
            (string) $node['id'],
            'value',
            $context->export((string) ($data['default_value'] ?? ''))
        );

        return [
            'code' => $function . '(' . $value . ');',
            'terminal' => true,
        ];
    }
}
