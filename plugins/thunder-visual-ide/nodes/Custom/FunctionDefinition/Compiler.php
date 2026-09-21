<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Custom;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FunctionDefinitionCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $name = preg_replace('/[^A-Za-z0-9_]/', '', (string)($d['function_name'] ?? ''));
        if ($name === '')
        {
            $context->addError('Function Definition requires a function name.');
            return [];
        }
        $params = [];
        foreach (explode(',', (string)($d['parameters'] ?? '')) as $p)
        {
            $p = preg_replace('/[^A-Za-z0-9_]/', '', trim($p));
            if ($p !== '')
            {
                $params[] = '$' . $p;
            }
        }
        $ret = trim((string)($d['return_type'] ?? ''));
        $ret = $ret !== '' ? ': ' . $ret : '';
        $body = $context->compileFlowGraph((string)($d['graph_id'] ?? ''));
        return [
            'functions' => [
                "function {$name}(" . implode(', ', $params) . "){$ret}\n{\n" . $context->indent($body) . "\n}",
            ],
        ];
    }
}
