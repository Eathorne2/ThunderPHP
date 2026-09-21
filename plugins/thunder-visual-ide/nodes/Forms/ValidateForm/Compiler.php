<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Forms;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ValidateFormCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $d = is_array($node['data'] ?? null) ? $node['data'] : [];
        $schema = $context->formSchema((string)($d['view_node_id'] ?? ''), (string)($d['form_name'] ?? ''));
        if ($schema === null)
        {
            $context->addError('Validate Form Schema could not find its selected visual form.');
            return [];
        }
        $rules = [];
        foreach ((array)($schema['fields'] ?? []) as $field)
        {
            $name = (string)($field['name'] ?? '');
            if ($name !== '')
            {
                $rules[$name] = array_values((array)($field['rules'] ?? []));
            }
        }
        $extra = json_decode((string)($d['extra_rules_json'] ?? '{}'), true);
        if (!is_array($extra))
        {
            $context->addError('Validate Form Schema has invalid extra rules JSON.');
            $extra = [];
        }
        foreach ($extra as $field => $value)
        {
            $rules[(string)$field] = $value;
        }
        $validator = $context->variableForNode($node, 'validate', 'validator_variable');
        $errors = $context->variableForNode($node, 'errors', 'errors_variable');
        if ($context->mode() === 'expression')
        {
            return [
                'expression' => $errors,
                'outputs' => [
                    'errors' => $errors,
                ],
            ];
        }
        $before = $validator . ' = new \\Core\\Validate(' . $context->inputExpression((string)$node['id'], 'data',
            '[]') . ');' . "\n" . $validator . '->setRules(' . $context->export($rules) . ');';
        return [
            'control' => 'if',
            'before' => $before,
            'condition' => $validator . '->validate()',
            'true_port' => 'valid',
            'false_port' => 'invalid',
            'false_before' => $errors . ' = ' . $validator . '->getErrors();',
            'outputs' => [
                'errors' => $errors,
            ],
        ];
    }
}
