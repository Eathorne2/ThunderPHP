<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Looks;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class AssetCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture')
        {
            return [];
        }
        $d = $node['data'] ?? [];
        $looks = $context->connectedNodes((string)$node['id'], 'look', 'out', 'main');
        $folder = (string)($looks[0]['data']['folder'] ?? 'main');
        $encoded = (string)($d['content_base64'] ?? '');
        if (str_contains($encoded, ','))
        {
            $encoded = explode(',', $encoded, 2)[1];
        }
        $content = $encoded !== '' ? base64_decode($encoded, true) : '';
        if ($content === false)
        {
            $content = '';
        }
        $path = 'looks/' . trim($folder, '/') . '/' . trim((string)($d['filename'] ?? 'assets/file.txt'), '/');
        return [
            'files' => [
                $path => $content,
            ],
        ];
    }
}
