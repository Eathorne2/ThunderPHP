<?php

namespace ThunderLocalDocs;

final class FrontMatter
{
    public static function parse(string $content): array
    {
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        if (!str_starts_with($content, "---\n")) {
            return ['meta' => [], 'body' => $content];
        }

        $end = strpos($content, "\n---\n", 4);
        if ($end === false) {
            return ['meta' => [], 'body' => $content];
        }

        $header = substr($content, 4, $end - 4);
        $body = substr($content, $end + 5);
        $meta = [];

        foreach (explode("\n", $header) as $line) {
            if (!str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            $decoded = json_decode($value, true);
            $meta[$key] = json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        }

        return ['meta' => $meta, 'body' => ltrim($body)];
    }
}
