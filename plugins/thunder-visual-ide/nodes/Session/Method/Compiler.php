<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Session;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class SessionMethodCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $context->requireService('session');

        $method = $this->selectedMethod($node, $context);
        $resultVariable = $context->variableForNode($node, 'result');
        $sessionExpression = $context->inputExpression(
            (string) $node['id'],
            'session',
            '$session'
        );

        if ($context->mode() === 'expression') {
            $outputPort = $context->outputPort() ?: 'result';
            $expression = $outputPort === 'session'
                ? $sessionExpression
                : $resultVariable;

            return [
                'expression' => $expression,
                'outputs' => [
                    'result' => $resultVariable,
                    'session' => $sessionExpression,
                ],
            ];
        }

        if ($method === null) {
            $context->addError('Session Method requires a valid method selection.');

            return [
                'code' => '// Invalid session method selection.',
                'next_port' => 'exec',
                'outputs' => [
                    'result' => 'null',
                    'session' => $sessionExpression,
                ],
            ];
        }

        $arguments = $this->compileArguments($node, $method, $context);
        $methodName = preg_replace(
            '/[^A-Za-z0-9_]/',
            '',
            (string) ($method['name'] ?? '')
        );
        $call = '(' . $sessionExpression . ')->' . $methodName
            . '(' . implode(', ', $arguments) . ')';
        $returnType = strtolower(trim((string) ($method['return_type'] ?? 'mixed')));

        if ($returnType === 'void' || $returnType === 'never') {
            return [
                'code' => $call . ';',
                'next_port' => 'exec',
                'terminal' => $returnType === 'never',
                'outputs' => [
                    'result' => 'null',
                    'session' => $sessionExpression,
                ],
            ];
        }

        return [
            'code' => $resultVariable . ' = ' . $call . ';',
            'next_port' => 'exec',
            'outputs' => [
                'result' => $resultVariable,
                'session' => $sessionExpression,
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
        $selected = (string) ($node['data']['method'] ?? 'set');
        $definition = $context->registry()->definition('session.method');

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
