<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class RequestInputCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('request');
        $d = $node['data'] ?? [];
        $source = in_array(($d['source'] ?? ''), [
            'post',
            'get',
            'input',
        ], true) ? $d['source'] : 'post';
        $name = (string)($d['name'] ?? '');
        $e = '$request->' . $source . '(' . ($name !== '' ? $context->export($name) : '') . ')';
        return [
            'expression' => $e,
            'outputs' => [
                'value' => $e,
            ],
        ];
    }
}
