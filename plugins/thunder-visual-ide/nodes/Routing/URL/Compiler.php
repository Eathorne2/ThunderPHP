<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Routing;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class UrlCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $d = $node['data'] ?? [];
        $source = (string)($d['source'] ?? 'route_param');
        if ($source === 'get')
        {
            $context->requireService('request');
            $expr = '$request->get(' . $context->export((string)($d['param_name'] ?? '')) . ')';
        } elseif ($source === 'segment')
        {
            $expr = 'URL(' . max(0, (int)($d['segment_index'] ?? 0)) . ')';
        } else
        {
            $expr = 'get_param(' . $context->export((string)($d['param_name'] ?? 'id')) . ')';
        }
        $default = (string)($d['default_value'] ?? '');
        if ($default !== '')
        {
            $expr = '(' . $expr . ' ?? ' . $context->export($default) . ')';
        }
        return [
            'expression' => $expr,
            'outputs' => [
                'value' => $expr,
            ],
        ];
    }
}
