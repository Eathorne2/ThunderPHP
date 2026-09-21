<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class VariableCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        if (($data['source_type'] ?? 'variable') === 'constant') {
            $expression = $this->constantExpression((string) ($data['constant_name'] ?? 'ROOT'));
        } else {
            $variableName = preg_replace(
                '/[^A-Za-z0-9_]/',
                '',
                (string) ($data['variable_name'] ?? 'value')
            ) ?: 'value';
            if (preg_match('/^[0-9]/', $variableName)) {
                $variableName = '_' . $variableName;
            }
            $expression = '$' . $variableName;
        }

        $path = trim((string) ($data['path'] ?? ''));
        foreach (explode('.', $path) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            if (($data['path_mode'] ?? 'object') === 'array') {
                $expression .= '[' . $context->export($part) . ']';
                continue;
            }

            $safePart = preg_replace('/[^A-Za-z0-9_]/', '', $part) ?: 'value';
            $expression .= '->' . $safePart;
        }

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }

    private function constantExpression(string $value): string
    {
        $value = preg_replace('/\s+/', '', trim($value)) ?: 'ROOT';
        if (str_contains($value, '::')) {
            [$class, $constant] = array_pad(explode('::', $value, 2), 2, '');
            $class = $this->qualifiedName($class, 'self');
            $constant = preg_replace('/[^A-Za-z0-9_]/', '', $constant) ?: 'VALUE';
            if (preg_match('/^[0-9]/', $constant)) {
                $constant = '_' . $constant;
            }
            return $class . '::' . $constant;
        }

        return $this->qualifiedName($value, 'ROOT');
    }

    private function qualifiedName(string $value, string $fallback): string
    {
        $leadingSlash = str_starts_with($value, '\\');
        $parts = array_values(array_filter(explode('\\', ltrim($value, '\\')), static fn (string $part): bool => $part !== ''));
        $safe = [];
        foreach ($parts as $part) {
            $part = preg_replace('/[^A-Za-z0-9_]/', '', $part) ?: '';
            if ($part === '') {
                continue;
            }
            if (preg_match('/^[0-9]/', $part)) {
                $part = '_' . $part;
            }
            $safe[] = $part;
        }
        if ($safe === []) {
            return $fallback;
        }
        return ($leadingSlash ? '\\' : '') . implode('\\', $safe);
    }
}
