<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Files;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FileGetContentsCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $resultVariable = $context->variableForNode($node, 'file_contents', null);
        $successExpression = $resultVariable . ' !== false';
        $outputs = [
            'contents' => $resultVariable,
            'success' => $successExpression,
        ];

        if ($context->mode() === 'expression') {
            return [
                'expression' => $outputs[$context->outputPort() ?? 'contents'] ?? $resultVariable,
                'outputs' => $outputs,
            ];
        }

        $nodeId = (string) ($node['id'] ?? '');
        $path = $context->inputExpression($nodeId, 'path', "''");
        $streamContext = $context->inputExpression($nodeId, 'context', 'null');
        $offset = $context->inputExpression($nodeId, 'offset', '0');
        $length = $context->inputExpression($nodeId, 'length', 'null');
        $lengthVariable = $resultVariable . '_length';
        $useIncludePath = !empty($data['use_include_path']) ? 'true' : 'false';
        $suppress = !empty($data['suppress_warnings']) ? '@' : '';

        $call = $suppress . 'file_get_contents('
            . $path . ', '
            . $useIncludePath . ', '
            . $streamContext . ', '
            . '(int) (' . $offset . '), '
            . '(' . $lengthVariable . ' === null || (int) ' . $lengthVariable . ' <= 0) ? null : (int) ' . $lengthVariable
            . ')';

        return [
            'code' => $lengthVariable . ' = ' . $length . ';' . "\n"
                . $resultVariable . ' = ' . $call . ';',
            'next_port' => 'exec',
            'outputs' => $outputs,
        ];
    }
}
