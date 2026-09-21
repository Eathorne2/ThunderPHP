<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Authorization;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class AllRolesCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $raw = (string) ($node['data']['roles_json'] ?? '[]');
        $items = json_decode($raw, true);

        if (!is_array($items)) {
            $context->addError('The All Roles node contains invalid role data.');
            return [];
        }

        $roles = [];
        $seen = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $slug = trim((string) ($item['slug'] ?? ''));

            if ($slug === '') {
                $context->addWarning('A role without a slug was ignored.');
                continue;
            }

            $lookupKey = strtolower($slug);

            if (isset($seen[$lookupKey])) {
                $context->addWarning("Duplicate role {$slug} was ignored.");
                continue;
            }

            $seen[$lookupKey] = true;
            $roles[$slug] = [
                'name' => trim((string) ($item['name'] ?? $slug)) ?: $slug,
                'slug' => $slug,
                'description' => trim((string) ($item['description'] ?? '')),
            ];
        }

        if ($roles === []) {
            return [];
        }

        $exportedRoles = var_export($roles, true);
        $hook = sprintf(<<<'PHP'
add_filter('roles', function ($roles) {
    $roles = is_array($roles) ? $roles : [];
    $definedRoles = %s;

    foreach ($definedRoles as $slug => $definition) {
        $roles[$slug] = (object) $definition;
    }

    return $roles;
});
PHP, $exportedRoles);

        return [
            'hooks' => [$hook],
        ];
    }
}
