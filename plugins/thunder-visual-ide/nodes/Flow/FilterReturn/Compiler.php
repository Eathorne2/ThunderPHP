<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Flow;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FilterReturnCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $owner = $context->graphOwnerNode();
        $ownerData = is_array($owner['data'] ?? null) ? $owner['data'] : [];
        $variableName = $this->variableName((string) ($ownerData['data_variable'] ?? 'data'));

        if (($owner['type'] ?? '') !== 'lifecycle.controller' || ($ownerData['hook_type'] ?? 'action') !== 'filter') {
            $context->addWarning('Return Filter Data is outside a filter Controller Hook; $data was returned as a fallback.');
            $variableName = 'data';
        }

        return [
            'code' => 'return $' . $variableName . ';',
            'terminal' => true,
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
