<?php

namespace ThunderLocalDocs;

final class SearchEngine
{
    public function __construct(private readonly array $documents)
    {
    }

    public function search(string $query, int $limit = 40): array
    {
        $query = trim(preg_replace('/\s+/u', ' ', $query) ?? $query);
        $terms = $this->terms($query);

        if ($query === '' || $terms === []) {
            return ['query' => $query, 'terms' => [], 'results' => [], 'total' => 0];
        }

        $normalizedQuery = $this->normalize($query);
        $results = [];

        foreach ($this->documents as $document) {
            $title = $this->normalize((string)($document['title'] ?? ''));
            $description = $this->normalize((string)($document['description'] ?? ''));
            $headings = $this->normalize(implode(' ', $document['headings'] ?? []));
            $keywords = $this->normalize(implode(' ', $document['keywords'] ?? []));
            $topic = $this->normalize((string)($document['topic'] ?? ''));
            $subtopic = $this->normalize((string)($document['subtopic'] ?? ''));
            $content = $this->normalize((string)($document['search_text'] ?? ''));

            $score = 0;
            $matchedTerms = 0;

            if ($title === $normalizedQuery) $score += 300;
            if ($normalizedQuery !== '' && str_contains($title, $normalizedQuery)) $score += 150;
            if ($normalizedQuery !== '' && str_contains($headings, $normalizedQuery)) $score += 100;
            if ($normalizedQuery !== '' && str_contains($description, $normalizedQuery)) $score += 70;
            if ($normalizedQuery !== '' && str_contains($content, $normalizedQuery)) $score += 45;

            foreach ($terms as $term) {
                $matched = false;
                if (str_contains($title, $term)) { $score += 90 + $this->occurrences($title, $term) * 8; $matched = true; }
                if (str_contains($keywords, $term)) { $score += 65; $matched = true; }
                if (str_contains($headings, $term)) { $score += 50; $matched = true; }
                if (str_contains($description, $term)) { $score += 32; $matched = true; }
                if (str_contains($subtopic, $term)) { $score += 25; $matched = true; }
                if (str_contains($topic, $term)) { $score += 18; $matched = true; }
                if (str_contains($content, $term)) { $score += min(30, 6 + $this->occurrences($content, $term) * 2); $matched = true; }
                if ($matched) $matchedTerms++;
            }

            if ($matchedTerms === 0) {
                continue;
            }

            if ($matchedTerms === count($terms)) {
                $score += 60;
            } else {
                $score -= (count($terms) - $matchedTerms) * 15;
            }

            $result = $document;
            $result['score'] = $score;
            $result['excerpt'] = $this->excerpt((string)($document['plain_text'] ?? ''), $terms);
            $results[] = $result;
        }

        usort($results, static fn(array $a, array $b): int =>
            [$b['score'] ?? 0, -($b['order'] ?? 0)] <=> [$a['score'] ?? 0, -($a['order'] ?? 0)]
        );

        $total = count($results);
        return [
            'query' => $query,
            'terms' => $terms,
            'results' => array_slice($results, 0, max(1, $limit)),
            'total' => $total,
        ];
    }

    public function related(array $document, int $limit = 4): array
    {
        $currentId = (string)($document['id'] ?? '');
        $currentTerms = array_unique(array_merge(
            $document['keywords'] ?? [],
            $this->terms((string)($document['title'] ?? ''))
        ));
        $results = [];

        foreach ($this->documents as $candidate) {
            if (($candidate['id'] ?? '') === $currentId) continue;
            $score = 0;
            if (($candidate['subtopic_slug'] ?? '') === ($document['subtopic_slug'] ?? '')) $score += 100;
            elseif (($candidate['topic_slug'] ?? '') === ($document['topic_slug'] ?? '')) $score += 35;

            $haystack = $this->normalize(implode(' ', array_merge(
                $candidate['keywords'] ?? [],
                [(string)($candidate['title'] ?? ''), (string)($candidate['description'] ?? '')]
            )));
            foreach ($currentTerms as $term) {
                $term = $this->normalize((string)$term);
                if (docs_length($term) >= 3 && str_contains($haystack, $term)) $score += 8;
            }
            if ($score > 0) {
                $candidate['score'] = $score;
                $results[] = $candidate;
            }
        }

        usort($results, static fn(array $a, array $b): int => ($b['score'] ?? 0) <=> ($a['score'] ?? 0));
        return array_slice($results, 0, $limit);
    }

    private function terms(string $query): array
    {
        $parts = preg_split('/[^\p{L}\p{N}_\\-]+/u', $this->normalize($query), -1, PREG_SPLIT_NO_EMPTY);
        $parts = array_filter($parts ?: [], static fn(string $term): bool => docs_length($term) >= 2);
        return array_values(array_unique($parts));
    }

    private function normalize(string $value): string
    {
        return docs_lower(trim(preg_replace('/\s+/u', ' ', $value) ?? $value));
    }

    private function occurrences(string $haystack, string $needle): int
    {
        return $needle === '' ? 0 : substr_count($haystack, $needle);
    }

    private function excerpt(string $text, array $terms, int $radius = 105): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
        if ($text === '') return '';

        $position = null;
        foreach ($terms as $term) {
            $found = docs_stripos($text, $term);
            if ($found !== false && ($position === null || $found < $position)) $position = $found;
        }

        if ($position === null) {
            return docs_substr($text, 0, $radius * 2) . (docs_length($text) > $radius * 2 ? '…' : '');
        }

        $start = max(0, $position - $radius);
        $excerpt = docs_substr($text, $start, $radius * 2);
        return ($start > 0 ? '…' : '') . trim($excerpt) . (($start + $radius * 2) < docs_length($text) ? '…' : '');
    }
}
