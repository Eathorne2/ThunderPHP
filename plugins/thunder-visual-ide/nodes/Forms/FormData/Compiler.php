<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Forms;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FormDataCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $d = is_array($node['data'] ?? null) ? $node['data'] : [];
        $schema = $context->formSchema((string)($d['view_node_id'] ?? ''), (string)($d['form_name'] ?? ''));
        if ($schema === null)
        {
            $context->addError('Read Form Data could not find its selected visual form schema.');
            return [];
        }
        $context->requireService('request');
        $source = (string)($d['source'] ?? 'auto');
        if (!in_array($source, [
            'post',
            'get',
        ], true))
        {
            $source = strtolower((string)($schema['method'] ?? 'POST')) === 'get' ? 'get' : 'post';
        }
        $variable = $context->variableForNode($node, 'form_data');
        $items = [];
        $outputs = [
            'data' => $variable,
        ];
        foreach ((array)($schema['fields'] ?? []) as $field)
        {
            $name = (string)($field['name'] ?? '');
            if ($name === '')
            {
                continue;
            }
            $expr = '$request->' . $source . '(' . $context->export($name) . ')';
            $items[] = $context->export($name) . ' => ' . $expr;
            $port = preg_replace('/[^A-Za-z0-9_]/', '', $name) ? : 'field';
            $outputs['field_' . $port] = $variable . '[' . $context->export($name) . '] ?? null';
        }
        $code = $variable . ' = [' . ($items ? "\n    " . implode(",\n    ", $items) . "\n" : '') . '];';
        if ($context->mode() === 'expression')
        {
            return [
                'expression' => $variable,
                'outputs' => $outputs,
            ];
        }
        return [
            'code' => $code,
            'outputs' => $outputs,
        ];
    }
}
