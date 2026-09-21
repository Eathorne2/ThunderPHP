<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class RawCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'migration')
        {
            return [];
        }
        $d = is_array($node['data'] ?? null) ? $node['data'] : [];
        $up = trim((string)($d['up_sql'] ?? ''));
        $down = trim((string)($d['down_sql'] ?? ''));
        return [
            'up' => $up !== '' ? '$this->query(' . $context->export($up) . ');' : '',
            'down' => $down !== '' ? '$this->query(' . $context->export($down) . ');' : '',
        ];
    }
}
