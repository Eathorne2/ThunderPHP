<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Response;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class RedirectCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $defaultUrl = $context->export(
            (string) ($node['data']['url'] ?? '/')
        );
        $url = $context->inputExpression(
            (string) $node['id'],
            'dynamic_url',
            $defaultUrl
        );
        return [
            'code' => 'redirect(' . $url . ');',
            'terminal' => true,
        ];
    }
}
