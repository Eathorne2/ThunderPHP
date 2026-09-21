<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Routing;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class RouteCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $name = trim((string)($d['route_name'] ?? ''));
        if ($name === '')
        {
            $context->addError('Every Route node needs a route name.');
        }
        return [
            'routes' => [
                [
                    'method' => strtoupper((string)($d['method'] ?? 'GET')),
                    'pattern' => (string)($d['path'] ?? '/'),
                    'name' => $name,
                ],
            ],
        ];
    }
}
