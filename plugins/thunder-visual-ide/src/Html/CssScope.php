<?php

declare(strict_types=1);

namespace ThunderVisualIde\Html;

final class CssScope
{
    public static function scope(string $css, string $scopeSelector): string
    {
        $css = trim($css);
        $scopeSelector = trim($scopeSelector);

        if ($css === '' || $scopeSelector === '') {
            return $css;
        }

        $keyframes = [];
        $scopeToken = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', $scopeSelector) ?: '', '-');

        $css = preg_replace_callback(
            '/@(\-webkit\-)?keyframes\s+([A-Za-z_][A-Za-z0-9_-]*)/i',
            static function (array $matches) use (&$keyframes, $scopeToken): string {
                $original = (string) $matches[2];
                $renamed = ($scopeToken !== '' ? $scopeToken . '-' : 'component-') . $original;
                $keyframes[$original] = $renamed;

                return '@' . (string) ($matches[1] ?? '') . 'keyframes ' . $renamed;
            },
            $css
        ) ?? $css;

        if ($keyframes !== []) {
            $css = preg_replace_callback(
                '/(animation(?:-name)?\s*:\s*)([^;{}]+)/i',
                static function (array $matches) use ($keyframes): string {
                    $value = (string) $matches[2];
                    foreach ($keyframes as $original => $renamed) {
                        $value = preg_replace(
                            '/\b' . preg_quote($original, '/') . '\b/',
                            $renamed,
                            $value
                        ) ?? $value;
                    }

                    return (string) $matches[1] . $value;
                },
                $css
            ) ?? $css;
        }

        return trim(self::scopeRules($css, $scopeSelector));
    }

    private static function scopeRules(string $css, string $scopeSelector): string
    {
        $output = '';
        $length = strlen($css);
        $cursor = 0;

        while ($cursor < $length) {
            $brace = self::findNextBrace($css, $cursor);
            $semicolon = self::findNextSemicolon($css, $cursor);

            if ($semicolon !== null && ($brace === null || $semicolon < $brace)) {
                $output .= substr($css, $cursor, $semicolon - $cursor + 1);
                $cursor = $semicolon + 1;
                continue;
            }

            if ($brace === null) {
                $output .= substr($css, $cursor);
                break;
            }

            $header = substr($css, $cursor, $brace - $cursor);
            $close = self::matchingBrace($css, $brace);
            if ($close === null) {
                $output .= substr($css, $cursor);
                break;
            }

            $body = substr($css, $brace + 1, $close - $brace - 1);
            $trimmedHeader = trim($header);
            $leadingLength = strlen($header) - strlen(ltrim($header));
            $leading = substr($header, 0, $leadingLength);
            $commentPrefix = '';

            while (preg_match('/^\/\*.*?\*\/\s*/s', $trimmedHeader, $commentMatch)) {
                $commentPrefix .= rtrim((string) $commentMatch[0]) . "\n";
                $trimmedHeader = ltrim(substr($trimmedHeader, strlen((string) $commentMatch[0])));
            }

            if ($trimmedHeader === '') {
                $output .= $header . '{' . $body . '}';
                $cursor = $close + 1;
                continue;
            }

            if (str_starts_with($trimmedHeader, '@')) {
                if (preg_match('/^@(media|supports|container|layer|document)\b/i', $trimmedHeader)) {
                    $output .= $leading . $commentPrefix . $trimmedHeader . '{'
                        . self::scopeRules($body, $scopeSelector)
                        . '}';
                } else {
                    $output .= $leading . $commentPrefix . $trimmedHeader . '{' . $body . '}';
                }
            } else {
                $output .= $leading
                    . $commentPrefix
                    . self::scopeSelectorList($trimmedHeader, $scopeSelector)
                    . '{'
                    . $body
                    . '}';
            }

            $cursor = $close + 1;
        }

        return $output;
    }

    private static function scopeSelectorList(string $selectors, string $scopeSelector): string
    {
        $parts = self::splitSelectors($selectors);
        $scoped = [];

        foreach ($parts as $selector) {
            $selector = trim($selector);
            if ($selector === '') {
                continue;
            }

            $selector = str_replace(['{{scope}}', ':scope'], $scopeSelector, $selector);
            if (str_contains($selector, $scopeSelector)) {
                $scoped[] = $selector;
                continue;
            }

            $selector = preg_replace('/^(?:html|body|:root)\b\s*/i', '', $selector) ?? $selector;
            $selector = trim($selector);

            if ($selector === '') {
                $scoped[] = $scopeSelector;
            } elseif (str_starts_with($selector, ':')) {
                $scoped[] = $scopeSelector . $selector;
            } else {
                $scoped[] = $scopeSelector . ' ' . $selector;
            }
        }

        return implode(', ', $scoped);
    }

    /** @return list<string> */
    private static function splitSelectors(string $selectors): array
    {
        $parts = [];
        $buffer = '';
        $roundDepth = 0;
        $squareDepth = 0;
        $quote = null;
        $length = strlen($selectors);

        for ($index = 0; $index < $length; $index++) {
            $char = $selectors[$index];

            if ($quote !== null) {
                $buffer .= $char;
                if ($char === '\\' && $index + 1 < $length) {
                    $buffer .= $selectors[++$index];
                } elseif ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if ($char === '"' || $char === "'") {
                $quote = $char;
                $buffer .= $char;
                continue;
            }

            if ($char === '(') {
                $roundDepth++;
            } elseif ($char === ')') {
                $roundDepth = max(0, $roundDepth - 1);
            } elseif ($char === '[') {
                $squareDepth++;
            } elseif ($char === ']') {
                $squareDepth = max(0, $squareDepth - 1);
            }

            if ($char === ',' && $roundDepth === 0 && $squareDepth === 0) {
                $parts[] = $buffer;
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        $parts[] = $buffer;
        return $parts;
    }

    private static function findNextSemicolon(string $css, int $start): ?int
    {
        $length = strlen($css);
        $quote = null;
        $comment = false;
        $roundDepth = 0;

        for ($index = $start; $index < $length; $index++) {
            $char = $css[$index];
            $next = $index + 1 < $length ? $css[$index + 1] : '';

            if ($comment) {
                if ($char === '*' && $next === '/') {
                    $comment = false;
                    $index++;
                }
                continue;
            }

            if ($quote !== null) {
                if ($char === '\\') {
                    $index++;
                } elseif ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if ($char === '/' && $next === '*') {
                $comment = true;
                $index++;
                continue;
            }

            if ($char === '"' || $char === "'") {
                $quote = $char;
                continue;
            }

            if ($char === '(') {
                $roundDepth++;
            } elseif ($char === ')') {
                $roundDepth = max(0, $roundDepth - 1);
            } elseif ($char === ';' && $roundDepth === 0) {
                return $index;
            } elseif ($char === '{' && $roundDepth === 0) {
                return null;
            }
        }

        return null;
    }

    private static function findNextBrace(string $css, int $start): ?int
    {
        $length = strlen($css);
        $quote = null;
        $comment = false;

        for ($index = $start; $index < $length; $index++) {
            $char = $css[$index];
            $next = $index + 1 < $length ? $css[$index + 1] : '';

            if ($comment) {
                if ($char === '*' && $next === '/') {
                    $comment = false;
                    $index++;
                }
                continue;
            }

            if ($quote !== null) {
                if ($char === '\\') {
                    $index++;
                } elseif ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if ($char === '/' && $next === '*') {
                $comment = true;
                $index++;
                continue;
            }

            if ($char === '"' || $char === "'") {
                $quote = $char;
                continue;
            }

            if ($char === '{') {
                return $index;
            }
        }

        return null;
    }

    private static function matchingBrace(string $css, int $open): ?int
    {
        $length = strlen($css);
        $depth = 0;
        $quote = null;
        $comment = false;

        for ($index = $open; $index < $length; $index++) {
            $char = $css[$index];
            $next = $index + 1 < $length ? $css[$index + 1] : '';

            if ($comment) {
                if ($char === '*' && $next === '/') {
                    $comment = false;
                    $index++;
                }
                continue;
            }

            if ($quote !== null) {
                if ($char === '\\') {
                    $index++;
                } elseif ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if ($char === '/' && $next === '*') {
                $comment = true;
                $index++;
                continue;
            }

            if ($char === '"' || $char === "'") {
                $quote = $char;
                continue;
            }

            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
                if ($depth === 0) {
                    return $index;
                }
            }
        }

        return null;
    }
}
