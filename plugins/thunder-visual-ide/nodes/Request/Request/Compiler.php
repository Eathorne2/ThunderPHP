<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Request;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class RequestCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('request');

        if ($context->mode() === 'expression') {
            return [
                'expression' => '$request',
                'outputs' => [
                    'request' => '$request',
                ],
            ];
        }

        return [
            'code' => '',
            'next_port' => 'exec',
            'outputs' => [
                'request' => '$request',
            ],
        ];
    }
}
