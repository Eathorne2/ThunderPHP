<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PhpValueCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $variable = trim((string) ($data['js_variable_name'] ?? 'serverValue'));
        if (!JavaScriptNodeSupport::validIdentifier($variable)) {
            $context->addError('PHP Value to JS requires a valid non-reserved JavaScript variable name.');
            $variable = '__tvi_invalid_php_value';
        }

        $expression = trim((string) ($data['php_expression'] ?? ''));
        $expression = preg_replace('/^\s*<\?(?:php|=)?/i', '', $expression) ?? $expression;
        $expression = preg_replace('/\?>\s*$/', '', $expression) ?? $expression;
        $expression = rtrim(trim($expression), "; \t\n\r\0\x0B");
        if ($expression === '') {
            $context->addError('PHP Value to JS requires a PHP expression.');
            $expression = 'null';
        }

        return [
            'expression' => $variable,
            'outputs' => ['value' => $variable],
            'php_expression' => $expression,
            'js_variable' => $variable,
            'bridge_key' => (string) ($node['id'] ?? 'php_value'),
        ];
    }
}
