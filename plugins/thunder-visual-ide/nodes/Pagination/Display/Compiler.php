<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Pagination;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class DisplayPagerCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $pager = $context->inputExpression((string) $node['id'], 'pager', 'null');
        return ['code' => '(' . $pager . ')->display();', 'next_port' => 'exec'];
    }
}
