<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Hooks;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FilterCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $hookName = trim((string) ($node['data']['hook_name'] ?? ''));
        if ($hookName === '') {
            $context->addError('Run Filter Hook requires a hook name.');
            return [];
        }

        $variable = $context->variableForNode($node, 'filtered_value');
        $outputs = [
            'value' => $variable,
        ];

        if ($context->mode() === 'expression') {
            return [
                'expression' => $variable,
                'outputs' => $outputs,
            ];
        }

        $data = $context->inputExpression(
            (string) $node['id'],
            'data',
            '[]'
        );

        return [
            'code' => sprintf(
                '%s = do_filter(%s, %s);',
                $variable,
                $context->export($hookName),
                $data
            ),
            'outputs' => $outputs,
            'next_port' => 'exec',
        ];
    }
}
