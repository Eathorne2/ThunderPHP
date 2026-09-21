<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Request;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PhpInputCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $rawVariable = $context->variableForNode($node, 'php_input', null);
        $jsonVariable = $rawVariable . '_json';
        $validVariable = $rawVariable . '_json_valid';

        $outputs = [
            'raw' => $rawVariable,
            'json' => $jsonVariable,
            'json_valid' => $validVariable,
        ];

        if ($context->mode() === 'expression') {
            return [
                'expression' => $outputs[$context->outputPort() ?? 'raw'] ?? $rawVariable,
                'outputs' => $outputs,
            ];
        }

        $maximumBytes = max(0, (int) ($data['maximum_bytes'] ?? 0));
        $readExpression = $maximumBytes > 0
            ? "file_get_contents('php://input', false, null, 0, {$maximumBytes})"
            : "file_get_contents('php://input')";
        $associative = !array_key_exists('json_associative', $data) || !empty($data['json_associative']);

        $code = [];
        $code[] = $rawVariable . ' = ' . $readExpression . ';';
        $code[] = $rawVariable . " = {$rawVariable} === false ? '' : {$rawVariable};";
        $code[] = $jsonVariable . ' = json_decode(' . $rawVariable . ', ' . ($associative ? 'true' : 'false') . ');';
        $code[] = $validVariable . ' = json_last_error() === JSON_ERROR_NONE;';
        $code[] = 'if (!' . $validVariable . ') {';
        $code[] = '    ' . $jsonVariable . ' = null;';
        $code[] = '}';

        return [
            'code' => implode("\n", $code),
            'next_port' => 'exec',
            'outputs' => $outputs,
        ];
    }
}
