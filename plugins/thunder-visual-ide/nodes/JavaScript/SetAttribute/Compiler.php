<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class SetAttributeCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $element = JavaScriptNodeSupport::targetExpression($node, $context);
        $value = $context->inputJavaScriptExpression((string) $node['id'], 'value', "''");
        $name = $context->javascriptExport(trim((string) ($node['data']['attribute'] ?? 'aria-busy')) ?: 'aria-busy');
        $variable = $context->javascriptVariable($node, 'element');
        $action = !empty($node['data']['remove'])
            ? $variable . '.removeAttribute(' . $name . ');'
            : $variable . '.setAttribute(' . $name . ', String(' . $value . '));';
        return ['code' => 'const ' . $variable . ' = ' . $element . '; if (' . $variable . ') ' . $action, 'next_port' => 'exec'];
    }
}
