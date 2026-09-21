<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Data;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class IssetCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $target = $this->target($node, $context, 'Isset Check');
        $expression = $target !== null ? 'isset(' . $target . ')' : 'false';

        return [
            'expression' => $expression,
            'outputs' => ['value' => $expression],
        ];
    }

    private function target(array $node, CompileContext $context, string $fallbackTitle): ?string
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $title = trim((string) ($data['title'] ?? $fallbackTitle)) ?: $fallbackTitle;
        $path = ltrim(trim((string) ($data['variable_path'] ?? '')), '$');
        $mode = (string) ($data['access_mode'] ?? 'array');

        if ($path === '') {
            $context->addError("{$title} requires a variable path.");
            return null;
        }

        $segments = explode('.', $path);
        $root = array_shift($segments);
        if (!is_string($root) || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $root) !== 1) {
            $context->addError("{$title} has an invalid root variable in path: {$path}");
            return null;
        }

        $target = '$' . $root;
        foreach ($segments as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                $context->addError("{$title} contains an empty dot-notation segment: {$path}");
                return null;
            }

            if ($mode === 'object') {
                if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $segment) !== 1) {
                    $context->addError("{$title} has an invalid object property in path: {$path}");
                    return null;
                }
                $target .= '->' . $segment;
                continue;
            }

            if (preg_match('/^-?[0-9]+$/', $segment) === 1) {
                $target .= '[' . (string) ((int) $segment) . ']';
                continue;
            }
            $target .= '[' . $context->export($segment) . ']';
        }

        return $target;
    }
}
