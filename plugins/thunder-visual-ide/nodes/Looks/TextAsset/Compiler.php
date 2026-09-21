<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Looks;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class TextAssetCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $lookNodes = $context->connectedNodesOfType(
            (string) ($node['id'] ?? ''),
            'looks.look',
            'main'
        );

        $lookFolder = trim(
            (string) ($lookNodes[0]['data']['folder'] ?? 'main'),
            '/'
        );
        if ($lookFolder === '') {
            $lookFolder = 'main';
        }

        $filename = trim(
            str_replace('\\', '/', (string) ($data['filename'] ?? '')),
            '/'
        );
        $assetType = strtolower((string) ($data['asset_type'] ?? 'CSS'));

        if ($filename === '') {
            $filename = str_contains($assetType, 'java')
                ? 'assets/js/main.js'
                : 'assets/css/main.css';
        }

        return [
            'files' => [
                "looks/{$lookFolder}/{$filename}" => (string) ($data['content'] ?? ''),
            ],
        ];
    }
}
