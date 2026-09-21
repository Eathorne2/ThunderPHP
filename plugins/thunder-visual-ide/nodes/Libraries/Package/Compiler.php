<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Libraries;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ZipReader;

final class PackageCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $archive = $this->archiveContents($data);
        if ($archive === '') {
            $context->addWarning(
                sprintf('%s does not contain a library ZIP archive.', (string) ($data['title'] ?? 'Library Package'))
            );
            return [];
        }

        $destination = trim(str_replace('\\', '/', (string) ($data['destination'] ?? 'libraries/package')), '/');
        if ($destination === '') {
            $destination = 'libraries/package';
        }

        $files = [];
        foreach (ZipReader::files($archive, !empty($data['strip_root_folder'])) as $path => $content) {
            $files[$destination . '/' . $path] = $content;
        }

        $result = ['files' => $files];
        if (!empty($data['load_autoloader'])) {
            $autoload = trim(str_replace('\\', '/', (string) ($data['autoload_file'] ?? '')), '/');
            if ($autoload !== '') {
                $fullPath = $destination . '/' . $autoload;
                $variable = '$libraryAutoload_' . substr(sha1((string) ($node['id'] ?? $fullPath)), 0, 8);
                $result['bootstrap'] = [
                    "{$variable} = plugin_path(" . var_export($fullPath, true) . ");\n"
                    . "if (is_file({$variable})) {\n"
                    . "    require_once {$variable};\n"
                    . "}",
                ];
            }
        }

        return $result;
    }
    /** @param array<string,mixed> $data */
    private function archiveContents(array $data): string
    {
        $archiveId = preg_replace('/[^a-zA-Z0-9_-]+/', '', (string) ($data['archive_id'] ?? '')) ?: '';
        if ($archiveId !== '') {
            $path = plugin_path('storage/libraries/' . $archiveId . '.zip');
            if (is_file($path)) {
                return (string) file_get_contents($path);
            }
        }

        $encoded = (string) ($data['content_base64'] ?? '');
        if ($encoded === '') {
            return '';
        }
        if (str_contains($encoded, ',')) {
            $encoded = explode(',', $encoded, 2)[1];
        }

        $archive = base64_decode($encoded, true);
        if ($archive === false) {
            throw new \RuntimeException('The library ZIP data could not be decoded.');
        }
        return $archive;
    }

}
