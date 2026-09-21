<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Flow;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class UnsetCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $path = ltrim(trim((string) ($data['variable_path'] ?? '')), '$');
        $title = trim((string) ($data['title'] ?? 'Unset Variable')) ?: 'Unset Variable';

        if ($path === '') {
            $context->addError("{$title} requires a variable path.");
            return ['code' => '', 'next_port' => 'exec'];
        }

        $segments = explode('.', $path);
        $root = array_shift($segments);
        if (!is_string($root) || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $root) !== 1) {
            $context->addError("{$title} has an invalid root variable in path: {$path}");
            return ['code' => '', 'next_port' => 'exec'];
        }

        $target = '$' . $root;
        foreach ($segments as $segment) {
            if ($segment === '') {
                $context->addError("{$title} contains an empty dot-notation segment: {$path}");
                return ['code' => '', 'next_port' => 'exec'];
            }
            if (preg_match('/^-?[0-9]+$/', $segment) === 1) {
                $target .= '[' . (string) ((int) $segment) . ']';
                continue;
            }
            $target .= '[' . $context->export($segment) . ']';
        }

        return [
            'code' => 'unset(' . $target . ');',
            'next_port' => 'exec',
        ];
    }
}
