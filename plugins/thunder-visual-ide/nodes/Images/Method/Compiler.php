<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Images;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ImageMethodCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $method = $this->selectedMethod($node, $context);
        $resultVariable = $context->variableForNode($node, 'result');
        $imageExpression = $context->inputExpression(
            (string) $node['id'],
            'image',
            '(new \\Core\\Image())'
        );

        $returnType = strtolower(trim((string) ($method['return_type'] ?? 'mixed')));
        $chainable = !empty($method['chainable']) || in_array($returnType, ['self', 'static'], true);
        $continuationExpression = $chainable ? $resultVariable : $imageExpression;

        if ($context->mode() === 'expression') {
            $outputPort = $context->outputPort() ?: 'result';
            $expression = $outputPort === 'image' ? $continuationExpression : $resultVariable;

            return [
                'expression' => $expression,
                'outputs' => [
                    'result' => $resultVariable,
                    'image' => $continuationExpression,
                ],
            ];
        }

        if ($method === null) {
            $context->addError('Image Method requires a valid method selection.');

            return [
                'code' => '// Invalid image method selection.',
                'next_port' => 'exec',
                'outputs' => [
                    'result' => 'null',
                    'image' => $imageExpression,
                ],
            ];
        }

        $arguments = [];
        foreach ((array) ($method['params'] ?? []) as $parameter) {
            if (!is_array($parameter)) {
                continue;
            }

            $name = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($parameter['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $arguments[] = $context->inputExpression(
                (string) $node['id'],
                'param_' . $name,
                (string) ($parameter['default'] ?? 'null')
            );
        }

        $methodName = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($method['name'] ?? ''));
        $call = '(' . $imageExpression . ')->' . $methodName . '(' . implode(', ', $arguments) . ')';
        if ($returnType === 'void' || $returnType === 'never') {
            return [
                'code' => $call . ';',
                'next_port' => 'exec',
                'terminal' => $returnType === 'never',
                'outputs' => [
                    'result' => 'null',
                    'image' => $imageExpression,
                ],
            ];
        }

        return [
            'code' => $resultVariable . ' = ' . $call . ';',
            'next_port' => 'exec',
            'outputs' => [
                'result' => $resultVariable,
                'image' => $continuationExpression,
            ],
        ];
    }

    /** @return array<string,mixed>|null */
    private function selectedMethod(array $node, CompileContext $context): ?array
    {
        $selected = (string) ($node['data']['method'] ?? 'resize');
        $definition = $context->registry()->definition('images.method');

        foreach ((array) ($definition['methods'] ?? []) as $method) {
            if (is_array($method) && (string) ($method['name'] ?? '') === $selected) {
                return $method;
            }
        }

        return null;
    }
}
