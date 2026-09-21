<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class SetVariableCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $name = trim((string) ($data['variable_name'] ?? 'value'));
        $mode = (string) ($data['assignment_mode'] ?? 'Declare let');
        $declaration = match ($mode) {
            'Declare const' => 'const',
            'Declare var' => 'var',
            'Assign existing' => '',
            default => 'let',
        };

        if ($declaration === '') {
            if (!JavaScriptNodeSupport::validAssignablePath($name)) {
                $context->addError('Set JS Variable requires a valid assignable JavaScript variable or property path.');
                $name = '__tvi_invalid_assignment';
            }
        } elseif (!JavaScriptNodeSupport::validIdentifier($name)) {
            $context->addError('Set JS Variable declarations require a valid non-reserved JavaScript identifier.');
            $name = '__tvi_invalid_variable';
        }

        if ($context->mode() === 'javascript_expression') {
            return [
                'expression' => $name,
                'outputs' => ['value' => $name],
            ];
        }

        if ($context->mode() !== 'javascript_statement') {
            return [];
        }

        $value = $context->inputJavaScriptExpression(
            (string) ($node['id'] ?? ''),
            'value',
            'null'
        );
        $prefix = $declaration !== '' ? $declaration . ' ' : '';

        return [
            'code' => $prefix . $name . ' = ' . $value . ';',
            'next_port' => 'exec',
        ];
    }
}
