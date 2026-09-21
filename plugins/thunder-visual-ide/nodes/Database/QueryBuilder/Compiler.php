<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Database;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class QueryBuilderCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('database');

        if ($context->mode() === 'expression') {
            return [
                'expression' => '$db',
                'outputs' => ['builder' => '$db'],
            ];
        }

        return [
            'code' => '',
            'next_port' => 'exec',
            'outputs' => ['builder' => '$db'],
        ];
    }
}
