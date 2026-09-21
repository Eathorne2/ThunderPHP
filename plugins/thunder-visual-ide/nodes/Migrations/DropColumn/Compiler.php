<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class DropColumnCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'migration') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $table = $context->export((string) ($data['table'] ?? ''));
        $column = $context->export((string) ($data['column'] ?? ''));
        $restoreDefinition = $context->export(
            (string) ($data['restore_definition'] ?? '')
        );

        return [
            'up' => "\$this->dropColumn({$table}, {$column});",
            'down' => "\$this->addColumnToTable({$table}, {$restoreDefinition});",
        ];
    }
}
