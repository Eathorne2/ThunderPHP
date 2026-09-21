<?php

declare(strict_types=1);

namespace ThunderVisualIde\Tutorial;

use ThunderVisualIde\ZipWriter;

final class TutorialRegistry
{
    private string $root;

    /** @var array<string,mixed>|null */
    private ?array $index = null;

    /** @var array<int,array<string,mixed>>|null */
    private ?array $tutorials = null;

    public function __construct(string $root)
    {
        $this->root = rtrim(str_replace('\\', '/', $root), '/');
    }

    /** @return array<string,mixed> */
    public function index(): array
    {
        if ($this->index !== null) {
            return $this->index;
        }

        $path = $this->root . '/index.json';
        $decoded = is_file($path)
            ? json_decode((string) file_get_contents($path), true)
            : [];

        if (!is_array($decoded)) {
            $decoded = [];
        }

        $decoded['title'] = trim((string) ($decoded['title'] ?? 'Thunder Visual IDE Tutorials'));
        $decoded['description'] = trim((string) ($decoded['description'] ?? 'Learn Thunder Visual IDE through short, node-based tutorials.'));
        $decoded['categories'] = is_array($decoded['categories'] ?? null) ? array_values($decoded['categories']) : [];
        $decoded['tutorials'] = is_array($decoded['tutorials'] ?? null) ? array_values($decoded['tutorials']) : [];

        return $this->index = $decoded;
    }

    /** @return array<int,array<string,mixed>> */
    public function categories(): array
    {
        $categories = [];
        foreach ($this->index()['categories'] as $category) {
            if (!is_array($category)) {
                continue;
            }
            $id = $this->slug((string) ($category['id'] ?? ''));
            if ($id === '') {
                continue;
            }
            $categories[] = [
                'id' => $id,
                'name' => trim((string) ($category['name'] ?? $id)) ?: $id,
                'description' => trim((string) ($category['description'] ?? '')),
                'order' => (int) ($category['order'] ?? 100),
            ];
        }

        usort($categories, static fn(array $a, array $b): int =>
            ($a['order'] <=> $b['order']) ?: strcmp((string) $a['name'], (string) $b['name'])
        );

        return $categories;
    }

    /** @return array<int,array<string,mixed>> */
    public function all(): array
    {
        if ($this->tutorials !== null) {
            return $this->tutorials;
        }

        $tutorials = [];
        $entries = $this->index()['tutorials'];

        if ($entries === []) {
            foreach (glob($this->root . '/*/*/tutorial.json') ?: [] as $path) {
                $relative = trim(str_replace($this->root, '', str_replace('\\', '/', dirname($path))), '/');
                $entries[] = ['path' => $relative];
            }
        }

        foreach ($entries as $entry) {
            $entry = is_string($entry) ? ['path' => $entry] : $entry;
            if (!is_array($entry)) {
                continue;
            }

            $relative = $this->safeRelativePath((string) ($entry['path'] ?? ''));
            if ($relative === '') {
                continue;
            }

            $folder = $this->resolveDirectory($relative);
            if ($folder === null) {
                continue;
            }

            $metaPath = $folder . '/tutorial.json';
            $meta = is_file($metaPath)
                ? json_decode((string) file_get_contents($metaPath), true)
                : [];
            if (!is_array($meta)) {
                continue;
            }

            $meta = array_replace($meta, $entry);
            if (($meta['published'] ?? true) === false) {
                continue;
            }

            $slug = $this->slug((string) ($meta['slug'] ?? basename($folder)));
            if ($slug === '') {
                continue;
            }

            $category = $this->slug((string) ($meta['category'] ?? dirname($relative)));
            $thumbnail = $this->fileDataUri($folder, (string) ($meta['thumbnail'] ?? 'thumbnail.svg'));
            $contentFile = $this->safeRelativePath((string) ($meta['content'] ?? 'content.html'));
            $contentPath = $contentFile !== '' ? $this->resolveFileWithin($folder, $contentFile) : null;

            $tutorial = [
                'slug' => $slug,
                'title' => trim((string) ($meta['title'] ?? $slug)) ?: $slug,
                'summary' => trim((string) ($meta['summary'] ?? '')),
                'category' => $category,
                'order' => (int) ($meta['order'] ?? 100),
                'level' => trim((string) ($meta['level'] ?? 'Beginner')) ?: 'Beginner',
                'duration' => trim((string) ($meta['duration'] ?? '')),
                'tags' => array_values(array_filter((array) ($meta['tags'] ?? []), 'is_string')),
                'video' => is_array($meta['video'] ?? null) ? $meta['video'] : [],
                'thumbnail_data_uri' => $thumbnail,
                'content_html' => $contentPath !== null ? (string) file_get_contents($contentPath) : '',
                'folder' => $folder,
                'relative_path' => $relative,
                'files' => $this->companionFilesFromFolder($folder),
            ];
            $tutorial['video_embed_url'] = $this->videoEmbedUrl($tutorial['video']);
            $tutorials[] = $tutorial;
        }

        usort($tutorials, static fn(array $a, array $b): int =>
            strcmp((string) $a['category'], (string) $b['category'])
            ?: ($a['order'] <=> $b['order'])
            ?: strcmp((string) $a['title'], (string) $b['title'])
        );

        return $this->tutorials = $tutorials;
    }

    /** @return array<string,mixed>|null */
    public function find(string $slug): ?array
    {
        $slug = $this->slug($slug);
        foreach ($this->all() as $tutorial) {
            if (($tutorial['slug'] ?? '') === $slug) {
                return $tutorial;
            }
        }

        return null;
    }

    public function companionArchive(string $slug): ?string
    {
        $tutorial = $this->find($slug);
        if ($tutorial === null || ($tutorial['files'] ?? []) === []) {
            return null;
        }

        $files = [];
        foreach ($tutorial['files'] as $file) {
            $path = (string) ($file['absolute_path'] ?? '');
            $relative = (string) ($file['path'] ?? '');
            if ($path === '' || $relative === '' || !is_file($path)) {
                continue;
            }
            $files[$relative] = (string) file_get_contents($path);
        }

        if ($files === []) {
            return null;
        }

        return ZipWriter::create($files, $this->slug((string) $tutorial['slug']) . '-files');
    }

    /** @return array{path:string,mime:string,name:string}|null */
    public function localVideo(string $slug): ?array
    {
        $tutorial = $this->find($slug);
        if ($tutorial === null) {
            return null;
        }

        $video = is_array($tutorial['video'] ?? null) ? $tutorial['video'] : [];
        if (($video['type'] ?? '') !== 'file') {
            return null;
        }

        $file = $this->safeRelativePath((string) ($video['file'] ?? ''));
        $folder = (string) ($tutorial['folder'] ?? '');
        $path = $file !== '' ? $this->resolveFileWithin($folder, $file) : null;
        if ($path === null) {
            return null;
        }

        return [
            'path' => $path,
            'mime' => $this->mimeType($path),
            'name' => basename($path),
        ];
    }

    /** @return array<int,array<string,mixed>> */
    private function companionFilesFromFolder(string $folder): array
    {
        $filesRoot = $this->resolveDirectoryWithin($folder, 'files');
        if ($filesRoot === null) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($filesRoot, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $absolute = str_replace('\\', '/', $file->getPathname());
            $relative = ltrim(str_replace(str_replace('\\', '/', $filesRoot), '', $absolute), '/');
            $files[] = [
                'name' => $file->getFilename(),
                'path' => $relative,
                'size' => $file->getSize(),
                'size_label' => $this->formatBytes($file->getSize()),
                'absolute_path' => $absolute,
            ];
        }

        usort($files, static fn(array $a, array $b): int => strcmp((string) $a['path'], (string) $b['path']));
        return $files;
    }

    /** @param array<string,mixed> $video */
    private function videoEmbedUrl(array $video): string
    {
        $type = strtolower(trim((string) ($video['type'] ?? '')));
        $url = trim((string) ($video['url'] ?? ''));
        $id = trim((string) ($video['id'] ?? ''));

        if ($type === 'youtube') {
            if ($id === '' && $url !== '') {
                if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~', $url, $match)) {
                    $id = $match[1];
                }
            }
            return $id !== '' ? 'https://www.youtube-nocookie.com/embed/' . rawurlencode($id) : '';
        }

        if ($type === 'vimeo') {
            if ($id === '' && preg_match('~vimeo\.com/(?:video/)?([0-9]+)~', $url, $match)) {
                $id = $match[1];
            }
            return $id !== '' ? 'https://player.vimeo.com/video/' . rawurlencode($id) : '';
        }

        return '';
    }

    private function fileDataUri(string $folder, string $relative): string
    {
        $relative = $this->safeRelativePath($relative);
        $path = $relative !== '' ? $this->resolveFileWithin($folder, $relative) : null;
        if ($path === null) {
            return '';
        }

        return 'data:' . $this->mimeType($path) . ';base64,' . base64_encode((string) file_get_contents($path));
    }

    private function mimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return match ($extension) {
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg', 'ogv' => 'video/ogg',
            default => 'application/octet-stream',
        };
    }

    private function resolveDirectory(string $relative): ?string
    {
        return $this->resolveDirectoryWithin($this->root, $relative);
    }

    private function resolveDirectoryWithin(string $base, string $relative): ?string
    {
        $relative = $this->safeRelativePath($relative);
        if ($relative === '') {
            return null;
        }
        $root = realpath($base);
        $path = realpath(rtrim($base, '/\\') . '/' . $relative);
        if ($root === false || $path === false || !is_dir($path)) {
            return null;
        }
        $root = rtrim(str_replace('\\', '/', $root), '/') . '/';
        $path = str_replace('\\', '/', $path);
        return str_starts_with($path . '/', $root) ? $path : null;
    }

    private function resolveFileWithin(string $base, string $relative): ?string
    {
        $relative = $this->safeRelativePath($relative);
        if ($relative === '') {
            return null;
        }
        $root = realpath($base);
        $path = realpath(rtrim($base, '/\\') . '/' . $relative);
        if ($root === false || $path === false || !is_file($path)) {
            return null;
        }
        $root = rtrim(str_replace('\\', '/', $root), '/') . '/';
        $path = str_replace('\\', '/', $path);
        return str_starts_with($path, $root) ? $path : null;
    }

    private function safeRelativePath(string $path): string
    {
        $parts = [];
        foreach (explode('/', str_replace('\\', '/', trim($path))) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..' || str_contains($part, "\0")) {
                return '';
            }
            $parts[] = $part;
        }
        return implode('/', $parts);
    }

    private function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]+/', '-', $value) ?? '';
        return trim($value, '-_');
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return number_format($bytes / 1048576, 1) . ' MB';
    }
}
