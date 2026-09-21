<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Authorization;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class SetRolesCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            $context->addWarning(
                'Set Current Roles must be placed on the main architecture canvas so its filter is top-level.'
            );
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $graphId = trim((string) ($data['graph_id'] ?? ''));
        $literalRoles = $this->parseLiteralList(
            (string) ($data['literal_roles'] ?? '')
        );
        $flowCode = $graphId !== ''
            ? trim($context->compileFlowGraph($graphId))
            : '';

        $body = [
            '$roles = is_array($roles) ? $roles : [];',
        ];

        if ($literalRoles !== []) {
            $exportedRoles = var_export($literalRoles, true);
            $body[] = '$literalRoles = ' . $exportedRoles . ';';
            $body[] = '$roles = array_values(array_unique(array_merge(';
            $body[] = '    $roles,';
            $body[] = '    $literalRoles';
            $body[] = ')));';
        }

        if ($flowCode !== '') {
            $body[] = '';
            $body[] = $flowCode;
        }

        $body[] = '';
        $body[] = 'return $roles;';

        $hook = "add_filter('user_roles', function (\$roles) {\n"
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
