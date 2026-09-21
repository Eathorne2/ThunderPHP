<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Session;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class SessionCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('session');

        if ($context->mode() === 'expression') {
            return [
                'expression' => '$session',
                'outputs' => [
                    'session' => '$session',
                ],
            ];
        }

        return [
            'code' => '',
            'next_port' => 'exec',
            'outputs' => [
                'session' => '$session',
            ],
        ];
    }
}
