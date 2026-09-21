<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class AddForeignKeyCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'migration') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $table = (string) ($data['table'] ?? '');
        $constraintName = (string) ($data['constraint_name'] ?? '');

        $arguments = [
            $context->export($table),
            $context->export((string) ($data['column'] ?? '')),
            $context->export((string) ($data['reference_table'] ?? '')),
            $context->export((string) ($data['reference_column'] ?? '')),
            $context->export((string) ($data['on_delete'] ?? 'CASCADE')),
            $context->export((string) ($data['on_update'] ?? 'CASCADE')),
            $context->export($constraintName),
        ];

        $upCode = '$this->addForeignKeyToTable('
            . implode(', ', $arguments)
            . ');';
        $downCode = sprintf(
            '$this->dropForeignKey(%s, %s);',
            $context->export($table),
            $context->export($constraintName)
        );

        return [
            'up' => $upCode,
            'down' => $downCode,
        ];
    }
}
