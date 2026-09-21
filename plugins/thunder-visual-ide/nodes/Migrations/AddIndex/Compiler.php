<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class AddIndexCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'migration') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $table = (string) ($data['table'] ?? '');
        $columns = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) ($data['columns'] ?? ''))
        )));
        $columnArgument = count($columns) === 1 ? $columns[0] : $columns;
        $indexName = (string) ($data['index_name'] ?? '');
        $method = ($data['index_type'] ?? 'normal') === 'unique'
            ? 'addUniqueIndexToTable'
            : 'addIndexToTable';

        $upCode = sprintf(
            '$this->%s(%s, %s, %s);',
            $method,
            $context->export($table),
            $context->export($columnArgument),
            $context->export($indexName)
        );
        $downCode = sprintf(
            '$this->dropIndex(%s, %s);',
            $context->export($table),
            $context->export($indexName)
        );

        return [
            'up' => $upCode,
            'down' => $downCode,
        ];
    }
}
