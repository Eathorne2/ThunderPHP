<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Routing;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class RouteScopeCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $list = (string) ($data['route_list'] ?? 'on') === 'off' ? 'off' : 'on';
        $paths = $this->parsePaths((string) ($data['paths'] ?? ''));

        return [
            'route_scope' => [
                'list' => $list,
                'paths' => $paths,
            ],
        ];
    }

    /** @return list<string> */
    private function parsePaths(string $value): array
    {
        $paths = [];
        foreach (preg_split('/[\r\n,]+/', $value) ?: [] as $item) {
            $item = trim((string) $item);
            if ($item === '') {
                continue;
            }

            $item = trim($item, "/ \t\n\r\0\x0B");
            $segment = explode('/', $item, 2)[0] ?? '';
            $segment = trim($segment);
            if ($segment !== '') {
                $paths[] = $segment;
            }
        }

        return array_values(array_unique($paths));
    }
}
