<?php

declare(strict_types=1);

namespace ThunderVisualIde\Compiler;

final class JavaScriptNodeSupport
{
    /** @param array<string,mixed> $node */
    public static function targetSelector(array $node, CompileContext $context): string
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $targetNodeId = trim((string) ($data['target_node_id'] ?? ''));
        $manual = trim((string) ($data['target_selector'] ?? ''));
        $inside = trim((string) ($data['selector_inside'] ?? ''));

        if ($targetNodeId !== '') {
            $selector = $context->viewNodeSelector($targetNodeId);
            if ($inside !== '') {
                $selector .= ' ' . $inside;
            }
            return $selector;
        }

        return $manual;
    }

    /** @param array<string,mixed> $node */
    public static function targetExpression(
        array $node,
        CompileContext $context,
        string $port = 'element'
    ): string {
        $nodeId = (string) ($node['id'] ?? '');
        if ($context->incomingEdges($nodeId, $port) !== []) {
            return $context->inputJavaScriptExpression($nodeId, $port, 'null');
        }
        $selector = self::targetSelector($node, $context);
        return $selector !== ''
            ? 'document.querySelector(' . $context->javascriptExport($selector) . ')'
            : 'null';
    }

    public static function stripScriptTags(string $code): string
    {
        $code = preg_replace('/^\s*<script(?:\s[^>]*)?>/i', '', $code) ?? $code;
        $code = preg_replace('/<\/script>\s*$/i', '', $code) ?? $code;
        return trim($code);
    }

    public static function validIdentifier(string $value): bool
    {
        if (preg_match('/^[A-Za-z_$][A-Za-z0-9_$]*$/', $value) !== 1) {
            return false;
        }

        static $reserved = [
            'await', 'break', 'case', 'catch', 'class', 'const', 'continue',
            'debugger', 'default', 'delete', 'do', 'else', 'enum', 'export',
            'extends', 'false', 'finally', 'for', 'function', 'if', 'implements',
            'import', 'in', 'instanceof', 'interface', 'let', 'new', 'null',
            'package', 'private', 'protected', 'public', 'return', 'static',
            'super', 'switch', 'this', 'throw', 'true', 'try', 'typeof', 'var',
            'void', 'while', 'with', 'yield',
        ];

        return !in_array($value, $reserved, true);
    }

    public static function validAssignablePath(string $value): bool
    {
        return preg_match(
            '/^[A-Za-z_$][A-Za-z0-9_$]*(?:(?:\.[A-Za-z_$][A-Za-z0-9_$]*)|(?:\[(?:[^\]\r\n]+)\]))*$/',
            $value
        ) === 1;
    }

    public static function cleanIdentifier(string $value, string $fallback): string
    {
        $value = preg_replace('/[^A-Za-z0-9_$]+/', '_', trim($value)) ?: '';
        if ($value === '' || preg_match('/^[0-9]/', $value)) {
            $value = '_' . ($value !== '' ? $value : $fallback);
        }
        return $value;
    }

    /** @return array<string,mixed> */
    public static function decodeObject(string $json): array
    {
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }
}
