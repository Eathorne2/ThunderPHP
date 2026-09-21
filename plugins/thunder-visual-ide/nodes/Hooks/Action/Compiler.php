<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Hooks;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ActionCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $hookName = trim((string) ($node['data']['hook_name'] ?? ''));
        if ($hookName === '') {
            $context->addError('Run Action Hook requires a hook name.');
            return [];
        }

        $data = $context->inputExpression(
            (string) $node['id'],
            'data',
            '[]'
        );

        return [
            'code' => sprintf(
                'do_action(%s, %s);',
                $context->export($hookName),
                $data
            ),
            'next_port' => 'exec',
        ];
    }
}
