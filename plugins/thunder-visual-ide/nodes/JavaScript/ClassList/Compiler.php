<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ClassListCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $element = JavaScriptNodeSupport::targetExpression($node, $context);
        $class = $context->inputJavaScriptExpression((string) $node['id'], 'class_name', $context->javascriptExport((string) ($node['data']['class_name'] ?? 'is-active')));
        $action = strtolower((string) ($node['data']['action'] ?? 'Add'));
        if (!in_array($action, ['add','remove','toggle'], true)) $action = 'add';
        $variable = $context->javascriptVariable($node, 'element');
        return ['code' => 'const ' . $variable . ' = ' . $element . '; if (' . $variable . ') ' . $variable . '.classList.' . $action . '(String(' . $class . '));', 'next_port' => 'exec'];
    }
}
