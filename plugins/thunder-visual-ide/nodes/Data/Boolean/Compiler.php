<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class BooleanCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $expression = !empty($node['data']['value']) ? 'true' : 'false';

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }
}
