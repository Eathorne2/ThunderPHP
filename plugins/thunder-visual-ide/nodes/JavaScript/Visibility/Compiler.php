<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class VisibilityCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $element = JavaScriptNodeSupport::targetExpression($node, $context);
        $variable = $context->javascriptVariable($node, 'element');
        $action = (string) ($node['data']['action'] ?? 'Show');
        $code = match ($action) {
            'Hide' => $variable . '.hidden = true;',
            'Toggle' => $variable . '.hidden = !' . $variable . '.hidden;',
            default => $variable . '.hidden = false;',
        };
        return ['code' => 'const ' . $variable . ' = ' . $element . '; if (' . $variable . ') ' . $code, 'next_port' => 'exec'];
    }
}
