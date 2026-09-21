<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Authorization;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class SetPermissionsCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            $context->addWarning(
                'Set Current Permissions must be placed on the main architecture canvas so its filter is top-level.'
            );
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $graphId = trim((string) ($data['graph_id'] ?? ''));
        $literalPermissions = $this->parseLiteralList(
            (string) ($data['literal_permissions'] ?? '')
        );
        $flowCode = $graphId !== ''
            ? trim($context->compileFlowGraph($graphId))
            : '';

        $body = [
            '$permissions = is_array($permissions) ? $permissions : [];',
        ];

        if ($literalPermissions !== []) {
            $exportedPermissions = var_export($literalPermissions, true);
            $body[] = '$literalPermissions = ' . $exportedPermissions . ';';
            $body[] = '$permissions = array_values(array_unique(array_merge(';
            $body[] = '    $permissions,';
            $body[] = '    $literalPermissions';
            $body[] = ')));';
        }

        if ($flowCode !== '') {
            $body[] = '';
            $body[] = $flowCode;
        }

        $body[] = '';
        $body[] = 'return $permissions;';

        $hook = "add_filter('user_permissions', function (\$permissions) {\n"
            . $context->indent(implode("\n", $body))
            . "\n});";

        return [
            'hooks' => [$hook],
        ];
    }

    /** @return list<string> */
    private function parseLiteralList(string $value): array
    {
        return array_values(array_unique(array_filter(array_map(
            'trim',
            preg_split('/[\r\n,]+/', $value) ?: []
        ), static fn (string $item): bool => $item !== '')));
    }
}
