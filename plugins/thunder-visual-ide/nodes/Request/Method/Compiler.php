<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Request;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class RequestMethodCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('request');

        $method = $this->selectedMethod($node, $context);
        $resultVariable = $context->variableForNode($node, 'result');
        $requestExpression = $context->inputExpression(
            (string) $node['id'],
            'request',
            '$request'
        );

        $returnType = strtolower(trim((string) ($method['return_type'] ?? 'mixed')));
        $chainable = !empty($method['chainable']) || in_array($returnType, ['self', 'static'], true);
        $continuationExpression = $chainable ? $resultVariable : $requestExpression;

        if ($context->mode() === 'expression') {
            $outputPort = $context->outputPort() ?: 'result';
            $expression = $outputPort === 'request'
                ? $continuationExpression
                : $resultVariable;

            return [
                'expression' => $expression,
                'outputs' => [
                    'result' => $resultVariable,
                    'request' => $continuationExpression,
                ],
            ];
        }

        if ($method === null) {
            $context->addError('Request Method requires a valid method selection.');

            return [
                'code' => '// Invalid request method selection.',
                'next_port' => 'exec',
                'outputs' => [
                    'result' => 'null',
                    'request' => $requestExpression,
                ],
            ];
        }

        $arguments = $this->compileArguments($node, $method, $context);
        $methodName = preg_replace(
            '/[^A-Za-z0-9_]/',
            '',
            (string) ($method['name'] ?? '')
        );
        $call = '(' . $requestExpression . ')->' . $methodName
            . '(' . implode(', ', $arguments) . ')';
        if ($returnType === 'void' || $returnType === 'never') {
            return [
                'code' => $call . ';',
                'next_port' => 'exec',
                'terminal' => $returnType === 'never',
                'outputs' => [
                    'result' => 'null',
                    'request' => $requestExpression,
                ],
            ];
        }

        return [
            'code' => $resultVariable . ' = ' . $call . ';',
            'next_port' => 'exec',
            'outputs' => [
                'result' => $resultVariable,
                'request' => $continuationExpression,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $node
     * @param array<string,mixed> $method
     * @return list<string>
     */
    private function compileArguments(
        array $node,
        array $method,
        CompileContext $context
    ): array {
        $arguments = [];

        foreach ((array) ($method['params'] ?? []) as $parameter) {
            if (!is_array($parameter)) {
                continue;
            }

            $name = preg_replace(
                '/[^A-Za-z0-9_]/',
                '',
                (string) ($parameter['name'] ?? '')
            );
            if ($name === '') {
                continue;
            }

            $arguments[] = $context->inputExpression(
                (string) $node['id'],
                'param_' . $name,
                (string) ($parameter['default'] ?? 'null')
            );
        }

        return $arguments;
    }

    /** @return array<string,mixed>|null */
    private function selectedMethod(
        array $node,
        CompileContext $context
    ): ?array {
        $selected = (string) ($node['data']['method'] ?? 'post');
        $definition = $context->registry()->definition('request.method');

        foreach ((array) ($definition['methods'] ?? []) as $method) {
            if (
                is_array($method)
                && (string) ($method['name'] ?? '') === $selected
            ) {
                return $method;
            }
        }

        return null;
    }
}
