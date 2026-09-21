<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Security;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ValidateCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $rules = json_decode(
            (string) ($data['rules_json'] ?? '{}'),
            true
        );

        if (!is_array($rules)) {
            $context->addError('Validate node has invalid rules JSON.');
            $rules = [];
        }

        $validatorVariable = $context->variableForNode($node, 'validate', 'validator_variable');
        $errorsVariable = $context->variableForNode($node, 'errors', 'errors_variable');

        if ($context->mode() === 'expression') {
            return [
                'expression' => $errorsVariable,
                'outputs' => [
                    'errors' => $errorsVariable,
                ],
            ];
        }

        $inputData = $context->inputExpression(
            (string) $node['id'],
            'data',
            '[]'
        );
        $beforeCode = implode("\n", [
            "{$validatorVariable} = new \\Core\\Validate({$inputData});",
            $validatorVariable
                . '->setRules('
                . $context->export($rules)
                . ');',
        ]);

        return [
            'control' => 'if',
            'before' => $beforeCode,
            'condition' => "{$validatorVariable}->validate()",
            'true_port' => 'valid',
            'false_port' => 'invalid',
            'false_before' => $errorsVariable
                . ' = '
                . $validatorVariable
                . '->getErrors();',
            'outputs' => [
                'errors' => $errorsVariable,
            ],
        ];
    }
}
