<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class CodeAssetCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $reference = trim((string) ($data['code_asset_node_id'] ?? ''));
        $componentName = ViewNodeSupport::componentName(
            (string) ($data['component_name'] ?? '')
        );

        if ($reference === '') {
            $context->addWarning('Load Code Asset has no selected Code Asset node.');
            return [
                'html' => '<!-- Code Asset not selected -->',
                'component_name' => $componentName,
                'uses_base_css' => false,
            ];
        }

        try {
            $assetNode = $context->node($reference, 'main');
        } catch (\Throwable) {
            $context->addWarning("Load Code Asset references missing node {$reference}.");
            return [
                'html' => '<!-- Selected Code Asset is missing -->',
                'component_name' => $componentName,
                'uses_base_css' => false,
            ];
        }

        if ((string) ($assetNode['type'] ?? '') !== 'looks.text_asset') {
            $context->addWarning('Load Code Asset target is not a Code Asset node.');
            return [
                'html' => '<!-- Invalid Code Asset target -->',
                'component_name' => $componentName,
                'uses_base_css' => false,
            ];
        }

        if ($context->isNodeMuted($assetNode, 'main')) {
            $context->addWarning('Load Code Asset targets a muted Code Asset node.');
            return [
                'html' => '<!-- Selected Code Asset is muted -->',
                'component_name' => $componentName,
                'uses_base_css' => false,
            ];
        }

        $assetData = is_array($assetNode['data'] ?? null)
            ? $assetNode['data']
            : [];
        $filename = trim(
            str_replace('\\', '/', (string) ($assetData['filename'] ?? '')),
            '/'
        );
        if ($filename === '') {
            $context->addWarning('The selected Code Asset has an empty destination path.');
            return [
                'html' => '<!-- Selected Code Asset path is empty -->',
                'component_name' => $componentName,
                'uses_base_css' => false,
            ];
        }

        $lookNodes = $context->connectedNodesOfType(
            (string) ($assetNode['id'] ?? ''),
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

        $assetType = str_contains(
            strtolower((string) ($assetData['asset_type'] ?? 'CSS')),
            'java'
        ) ? 'js' : 'css';
        $pluginPath = "looks/{$lookFolder}/{$filename}";
        $html = $this->assetMarkup($assetType, $pluginPath);

        return [
            'html' => $html,
            'component_name' => $componentName,
            'uses_base_css' => false,
        ];
    }

    private function assetMarkup(string $assetType, string $pluginPath): string
    {
        $type = var_export($assetType, true);
        $path = var_export($pluginPath, true);
        $tag = $assetType === 'js'
            ? '<script data-tvi-preserve-position="1" src="<?= esc($__tvi_code_asset_url) ?>"></script>'
            : '<link rel="stylesheet" href="<?= esc($__tvi_code_asset_url) ?>">';

        return <<<PHP
<?php
\$__tvi_code_asset_type = {$type};
\$__tvi_code_asset_url = plugin_http_path({$path});
\$__tvi_code_asset_key = \$__tvi_code_asset_type . ':' . \$__tvi_code_asset_url;
if (empty(\$GLOBALS['__tvi_loaded_code_assets'][\$__tvi_code_asset_key])):
    \$GLOBALS['__tvi_loaded_code_assets'][\$__tvi_code_asset_key] = true;
?>
{$tag}
<?php
endif;
unset(
    \$__tvi_code_asset_type,
    \$__tvi_code_asset_url,
    \$__tvi_code_asset_key
);
?>
PHP;
    }
}
