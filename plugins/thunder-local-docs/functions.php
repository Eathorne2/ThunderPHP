<?php

namespace ThunderLocalDocs;

defined('ROOTPATH') or die('Direct script access denied');


function docs_lower(string $value): string
{
    return \function_exists('mb_strtolower')
        ? mb_strtolower($value, 'UTF-8')
        : strtolower($value);
}

function docs_length(string $value): int
{
    return \function_exists('mb_strlen')
        ? mb_strlen($value, 'UTF-8')
        : strlen($value);
}

function docs_substr(string $value, int $start, ?int $length = null): string
{
    if (\function_exists('mb_substr')) {
        return $length === null
            ? mb_substr($value, $start, null, 'UTF-8')
            : mb_substr($value, $start, $length, 'UTF-8');
    }

    return $length === null ? substr($value, $start) : substr($value, $start, $length);
}

function docs_stripos(string $haystack, string $needle): int|false
{
    return \function_exists('mb_stripos')
        ? mb_stripos($haystack, $needle, 0, 'UTF-8')
        : stripos($haystack, $needle);
}

function docs_settings(): array
{
    static $settings = null;

    if ($settings === null) {
        $loaded = require __DIR__ . '/content/settings.php';
        $settings = is_array($loaded) ? $loaded : [];
    }

    return $settings;
}

function docs_repository(): DocumentRepository
{
    static $repository = null;

    if ($repository === null) {
        $repository = new DocumentRepository(
            __DIR__ . '/content',
            __DIR__ . '/storage/search-index.php'
        );
    }

    return $repository;
}

function docs_url(string ...$segments): string
{
    $parts = ['docs'];

    foreach ($segments as $segment) {
        $segment = trim($segment, '/');
        if ($segment !== '') {
            $parts[] = rawurlencode($segment);
        }
    }

    return base_url(implode('/', $parts));
}

function docs_search_url(string $query = ''): string
{
    $url = docs_url('search');
    return $query === '' ? $url : $url . '?q=' . rawurlencode($query);
}

function docs_escape(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function docs_highlight(string $text, array $terms): string
{
    $terms = array_values(array_unique(array_filter(
        array_map('trim', $terms),
        static fn(string $term): bool => docs_length($term) >= 2
    )));

    if ($terms === []) {
        return docs_escape($text);
    }

    usort($terms, static fn(string $a, string $b): int => docs_length($b) <=> docs_length($a));
    $pattern = '/(' . implode('|', array_map(static fn(string $term): string => preg_quote($term, '/'), $terms)) . ')/iu';
    $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

    if ($parts === false) {
        return docs_escape($text);
    }

    $html = '';
    foreach ($parts as $part) {
        $html .= preg_match($pattern, $part) === 1
            ? '<mark>' . docs_escape($part) . '</mark>'
            : docs_escape($part);
    }

    return $html;
}

function docs_reading_time(string $plainText): int
{
    $words = preg_split('/\s+/u', trim($plainText), -1, PREG_SPLIT_NO_EMPTY);
    return max(1, (int)ceil(count($words ?: []) / 220));
}

function docs_not_found(string $message = 'The requested documentation page could not be found.'): void
{
    if (!headers_sent()) {
        http_response_code(404);
    }
    set_value('thunder_local_docs_page', [
        'type' => 'not-found',
        'title' => 'Documentation page not found',
        'message' => $message,
        'topics' => docs_repository()->topics(),
        'settings' => docs_settings(),
    ]);
}
