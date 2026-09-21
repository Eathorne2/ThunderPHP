<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class CsrfCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        return [
            'html' => '<?= csrf(' . var_export((string)($d['session_key'] ?? 'csrf'), true) . ', true, ' . max(1,
                (int)($d['hours'] ?? 1)) . ') ?>',
        ];
    }
}
