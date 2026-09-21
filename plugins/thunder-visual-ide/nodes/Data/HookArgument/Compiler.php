<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class HookArgumentCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $owner = $context->graphOwnerNode();
        $ownerData = is_array($owner['data'] ?? null) ? $owner['data'] : [];
        $variableName = $this->variableName((string) ($ownerData['data_variable'] ?? 'data'));

        if (($owner['type'] ?? '') !== 'lifecycle.controller') {
            $context->addWarning('Hook Data Argument is outside a Controller Hook flow; $data was used as a fallback.');
            $variableName = 'data';
        }

        $expression = '$' . $variableName;
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
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

            $safePart = preg_replace('/[^A-Za-z0-9_]/', '', $part);
            if ($safePart !== '') {
                $expression .= '->' . $safePart;
            }
        }

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }

    private function variableName(string $value): string
    {
        $name = preg_replace('/^\\$+/', '', trim($value));
        $name = preg_replace('/[^A-Za-z0-9_]+/', '_', (string) $name) ?: 'data';
        if (preg_match('/^[0-9]/', $name)) {
            $name = '_' . $name;
        }
        return $name;
    }
}
