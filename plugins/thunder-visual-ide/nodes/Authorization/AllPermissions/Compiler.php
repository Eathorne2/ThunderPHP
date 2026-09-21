<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Authorization;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class AllPermissionsCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $raw = (string) ($node['data']['permissions_json'] ?? '[]');
        $items = json_decode($raw, true);

        if (!is_array($items)) {
            $context->addError('The All Permissions node contains invalid permission data.');
            return [];
        }

        $permissions = [];
        $seen = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $slug = trim((string) ($item['slug'] ?? ''));

            if ($slug === '') {
                $context->addWarning('A permission without a slug was ignored.');
                continue;
            }

            $lookupKey = strtolower($slug);

            if (isset($seen[$lookupKey])) {
                $context->addWarning("Duplicate permission {$slug} was ignored.");
                continue;
            }

            $seen[$lookupKey] = true;
            $permissions[$slug] = [
                'name' => trim((string) ($item['name'] ?? $slug)) ?: $slug,
                'slug' => $slug,
                'group' => trim((string) ($item['group'] ?? 'General')) ?: 'General',
                'description' => trim((string) ($item['description'] ?? '')),
            ];
        }

        if ($permissions === []) {
            return [];
        }

        return [
            'permissions' => array_values($permissions),
        ];
    }
}
