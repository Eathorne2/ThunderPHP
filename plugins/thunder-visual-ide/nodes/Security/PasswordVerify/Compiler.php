<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Security;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PasswordVerifyCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $password = $context->inputExpression(
            (string) $node['id'],
            'password',
            "''"
        );
        $hash = $context->inputExpression(
            (string) $node['id'],
            'hash',
            "''"
        );
        $expression = "password_verify({$password}, {$hash})";

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }
}
