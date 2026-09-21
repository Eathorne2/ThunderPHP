<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class RenameColumnCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'migration') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $table = $context->export((string) ($data['table'] ?? ''));
        $oldName = $context->export((string) ($data['old_name'] ?? ''));
        $newName = $context->export((string) ($data['new_name'] ?? ''));
        $newDefinition = $context->export(
            (string) ($data['new_definition'] ?? '')
        );
        $oldDefinition = $context->export(
            (string) ($data['old_definition'] ?? '')
        );

        $upCode = sprintf(
            '$this->renameColumn(%s, %s, %s, %s);',
            $table,
            $oldName,
            $newName,
            $newDefinition
        );
        $downCode = sprintf(
            '$this->renameColumn(%s, %s, %s, %s);',
            $table,
            $newName,
            $oldName,
            $oldDefinition
        );

        return [
            'up' => $upCode,
            'down' => $downCode,
        ];
    }
}
