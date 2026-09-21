<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Security;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PasswordHashCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $password = $context->inputExpression(
            (string) $node['id'],
            'password',
            "''"
        );
        $e = "password_hash({$password}, PASSWORD_DEFAULT)";
        return [
            'expression' => $e,
            'outputs' => [
                'value' => $e,
            ],
        ];
    }
}
