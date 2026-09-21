<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class VariableCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $name = trim((string) ($node['data']['name'] ?? ''));
        if ($name === '' || preg_match('/^[A-Za-z_$][A-Za-z0-9_$]*(?:(?:\.[A-Za-z_$][A-Za-z0-9_$]*)|(?:\[[^\]\r\n]+\]))*$/', $name) !== 1) {
            $context->addError('JS Variable requires a valid JavaScript variable or property path.');
            $name = 'null';
        }
        return ['expression' => $name, 'outputs' => ['value' => $name]];
    }
}
