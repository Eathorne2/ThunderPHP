<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class SetContentCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $element = JavaScriptNodeSupport::targetExpression($node, $context);
        $value = $context->inputJavaScriptExpression((string) $node['id'], 'value', "''");
        $mode = (string) ($node['data']['mode'] ?? 'Text');
        $property = $mode === 'HTML' ? 'innerHTML' : ($mode === 'Value' ? 'value' : 'textContent');
        $variable = $context->javascriptVariable($node, 'element');
        return ['code' => 'const ' . $variable . ' = ' . $element . '; if (' . $variable . ') ' . $variable . '.' . $property . ' = ' . $value . ';', 'next_port' => 'exec'];
    }
}
