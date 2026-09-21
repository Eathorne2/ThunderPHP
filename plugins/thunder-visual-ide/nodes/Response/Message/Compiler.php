<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Response;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class MessageCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $d = $node['data'] ?? [];
        $defaultMessage = $context->export(
            (string) ($d['message'] ?? '')
        );
        $msg = $context->inputExpression(
            (string) $node['id'],
            'dynamic_message',
            $defaultMessage
        );
        return [
            'code' => 'message(' . $context->export((string)($d['message_type'] ?? 'success')) . ', ' . $msg . ');',
            'next_port' => 'exec',
        ];
    }
}
