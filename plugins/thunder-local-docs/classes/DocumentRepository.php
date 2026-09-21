<?php

namespace ThunderLocalDocs;

use RuntimeException;

final class DocumentRepository
{
    private array $catalog = [];
    private array $index = [];

    public function __construct(
        private readonly string $contentPath,
        private readonly string $indexPath
    ) {
    }

    public function topics(): array
    {
        $topics = array_values(array_filter(
            $this->catalog()['topics'] ?? [],
            static fn(array $topic): bool => (bool)($topic['published'] ?? false)
        ));

        usort($topics, static fn(array $a, array $b): int =>
            [$a['order'] ?? 0, $a['id'] ?? 0] <=> [$b['order'] ?? 0, $b['id'] ?? 0]
        );

        return $topics;
    }

    public function topic(string $slug): ?array
    {
        if (!$this->validSlug($slug)) {
            return null;
        }

        foreach ($this->topics() as $topic) {
            if (($topic['slug'] ?? '') === $slug) {
                return $topic;
            }
        }

        return null;
    }

    public function subtopic(string $topicSlug, string $subtopicSlug): ?array
    {
        if (!$this->validSlug($subtopicSlug)) {
            return null;
        }

        $topic = $this->topic($topicSlug);
        if ($topic === null) {
            return null;
        }

        foreach ($topic['subtopics'] ?? [] as $subtopic) {
            if (($subtopic['slug'] ?? '') === $subtopicSlug && ($subtopic['published'] ?? false)) {
                $subtopic['topic'] = $topic;
                return $subtopic;
            }
        }

        return null;
    }

    public function section(string $topicSlug, string $subtopicSlug, string $sectionSlug): ?array
    {
        if (!$this->validSlug($sectionSlug)) {
            return null;
        }

        $subtopic = $this->subtopic($topicSlug, $subtopicSlug);
        if ($subtopic === null) {
            return null;
        }

        foreach ($subtopic['sections'] ?? [] as $section) {
            if (($section['slug'] ?? '') !== $sectionSlug || !($section['published'] ?? false)) {
                continue;
            }

            $absolutePath = $this->safePath($section['path'] ?? '');
            if ($absolutePath === null || !is_file($absolutePath)) {
                return null;
            }

            $parsed = FrontMatter::parse((string)file_get_contents($absolutePath));
            $section = array_merge($section, $parsed['meta']);
            $section['markdown'] = $parsed['body'];
            $section['topic'] = $subtopic['topic'];
            $section['subtopic'] = $subtopic;
            $section['plain_text'] = $this->plainText($section['markdown']);

            return $section;
        }

        return null;
    }

    public function intro(array $subtopic): string
    {
        $path = (string)($subtopic['intro_path'] ?? '');
        if ($path === '') {
            return '';
        }

        $absolutePath = $this->safePath($path);
        return $absolutePath !== null && is_file($absolutePath)
            ? (string)file_get_contents($absolutePath)
            : '';
    }

    public function adjacent(array $section): array
    {
        $sections = array_values(array_filter(
            $section['subtopic']['sections'] ?? [],
            static fn(array $item): bool => (bool)($item['published'] ?? false)
        ));

        $current = null;
        foreach ($sections as $index => $item) {
            if (($item['slug'] ?? '') === ($section['slug'] ?? '')) {
                $current = $index;
                break;
            }
        }

        return [
            'previous' => $current !== null && $current > 0 ? $sections[$current - 1] : null,
            'next' => $current !== null && isset($sections[$current + 1]) ? $sections[$current + 1] : null,
        ];
    }

    public function documents(): array
    {
        return $this->index()['documents'] ?? [];
    }

    public function documentById(string $id): ?array
    {
        foreach ($this->documents() as $document) {
            if (($document['id'] ?? '') === $id) {
                return $document;
            }
        }
        return null;
    }

    public function stats(): array
    {
        $topics = $this->topics();
        $subtopics = 0;
        $sections = 0;

        foreach ($topics as $topic) {
            foreach ($topic['subtopics'] ?? [] as $subtopic) {
                if (!($subtopic['published'] ?? false)) {
                    continue;
                }
                $subtopics++;
                foreach ($subtopic['sections'] ?? [] as $section) {
                    if ($section['published'] ?? false) {
                        $sections++;
                    }
                }
            }
        }

        return [
            'topics' => count($topics),
            'subtopics' => $subtopics,
            'sections' => $sections,
        ];
    }

    private function catalog(): array
    {
        if ($this->catalog === []) {
            $path = $this->contentPath . '/catalog.php';
            if (!is_file($path)) {
                throw new RuntimeException('The local documentation catalog is missing. Run tools/build-index.php.');
            }
            $loaded = require $path;
            $this->catalog = is_array($loaded) ? $loaded : [];
        }
        return $this->catalog;
    }

    private function index(): array
    {
        if ($this->index === []) {
            if (!is_file($this->indexPath)) {
                throw new RuntimeException('The local documentation search index is missing. Run tools/build-index.php.');
            }
            $loaded = require $this->indexPath;
            $this->index = is_array($loaded) ? $loaded : [];
        }
        return $this->index;
    }

    private function safePath(string $relative): ?string
    {
        $relative = str_replace('\\', '/', ltrim($relative, '/'));
        if ($relative === '' || str_contains($relative, '../')) {
            return null;
        }
        return $this->contentPath . '/' . $relative;
    }

    private function validSlug(string $slug): bool
    {
        return (bool)preg_match('/^[a-z0-9][a-z0-9-]*$/', $slug);
    }

    private function plainText(string $markdown): string
    {
        $text = preg_replace('/```[^\n]*\n(.*?)```/su', ' $1 ', $markdown) ?? $markdown;
        $text = preg_replace('/`([^`]+)`/u', '$1', $text) ?? $text;
        $text = preg_replace('/!?\[([^\]]*)\]\([^)]*\)/u', '$1', $text) ?? $text;
        $text = strip_tags($text);
        $text = preg_replace('/[#>*_~|\-]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        return trim($text);
    }
}
