<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ModifyColumnCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'migration') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $table = $context->export((string) ($data['table'] ?? ''));
        $newDefinition = $context->export(
            (string) ($data['new_definition'] ?? '')
        );
        $oldDefinition = $context->export(
            (string) ($data['old_definition'] ?? '')
        );

        return [
            'up' => "\$this->modifyColumn({$table}, {$newDefinition});",
            'down' => "\$this->modifyColumn({$table}, {$oldDefinition});",
        ];
    }
}
