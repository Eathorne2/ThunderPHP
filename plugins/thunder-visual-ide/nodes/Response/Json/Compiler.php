<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Response;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class JsonCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $statusCode = max(100, min(
            599,
            (int) ($data['status_code'] ?? 200)
        ));

        $flags = ['JSON_UNESCAPED_UNICODE'];
        if (!empty($data['pretty_print'])) {
            $flags[] = 'JSON_PRETTY_PRINT';
        }
        if (!empty($data['unescaped_slashes'])) {
            $flags[] = 'JSON_UNESCAPED_SLASHES';
        }

        $responseData = $context->inputExpression(
            (string) $node['id'],
            'data',
            '[]'
        );
        $flagExpression = implode(' | ', $flags);
        $code = implode("\n", [
            "header('Content-Type: application/json; charset=utf-8');",
            "http_response_code({$statusCode});",
            "echo json_encode({$responseData}, {$flagExpression});",
            'exit;',
        ]);

        return [
            'code' => $code,
            'terminal' => true,
        ];
    }
}
