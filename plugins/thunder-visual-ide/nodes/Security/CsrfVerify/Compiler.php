<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Security;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class CsrfVerifyCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $key = $context->export((string)($node['data']['session_key'] ?? 'csrf'));
        $data = $context->inputExpression((string)$node['id'], 'data', '[]');
        return [
            'control' => 'if',
            'condition' => 'csrf_verify(' . $data . ', ' . $key . ')',
            'true_port' => 'valid',
            'false_port' => 'invalid',
        ];
    }
}
