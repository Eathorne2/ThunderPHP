<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Files;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FilePutContentsCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $resultVariable = $context->variableForNode($node, 'bytes_written', null);
        $successExpression = $resultVariable . ' !== false';
        $outputs = [
            'bytes' => $resultVariable,
            'success' => $successExpression,
        ];

        if ($context->mode() === 'expression') {
            return [
                'expression' => $outputs[$context->outputPort() ?? 'bytes'] ?? $resultVariable,
                'outputs' => $outputs,
            ];
        }

        $nodeId = (string) ($node['id'] ?? '');
        $path = $context->inputExpression($nodeId, 'path', "''");
        $contents = $context->inputExpression($nodeId, 'data', "''");
        $streamContext = $context->inputExpression($nodeId, 'context', 'null');
        $flags = [];
        if (!empty($data['use_include_path'])) {
            $flags[] = 'FILE_USE_INCLUDE_PATH';
        }
        if (!empty($data['append'])) {
            $flags[] = 'FILE_APPEND';
        }
        if (!empty($data['exclusive_lock'])) {
            $flags[] = 'LOCK_EX';
        }
        $flagExpression = $flags === [] ? '0' : implode(' | ', $flags);
        $suppress = !empty($data['suppress_warnings']) ? '@' : '';

        $call = $suppress . 'file_put_contents('
            . $path . ', '
            . $contents . ', '
            . $flagExpression . ', '
            . $streamContext
            . ')';

        return [
            'code' => $resultVariable . ' = ' . $call . ';',
            'next_port' => 'exec',
            'outputs' => $outputs,
        ];
    }
}
