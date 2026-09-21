<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class AddColumnCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'migration')
        {
            return [];
        }
        $d = is_array($node['data'] ?? null) ? $node['data'] : [];
        $table = trim((string)($d['table'] ?? ''));
        $sql = trim((string)($d['column_sql'] ?? ''));
        $column = trim((string)($d['column_name'] ?? ''));
        return [
            'up' => '$this->addColumnToTable(' . $context->export($table) . ', ' . $context->export($sql) . ');',
            'down' => '$this->dropColumn(' . $context->export($table) . ', ' . $context->export($column) . ');',
        ];
    }
}
