<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ReusableViewCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $reference = trim((string) ($data['reusable_view_node_id'] ?? ''));
        if ($reference === '') {
            $context->addWarning('Render Reusable View has no selected reusable view.');
            return ['html' => '<!-- Reusable View not selected -->'];
        }

        try {
            $reusable = $context->node($reference, 'main');
        } catch (\Throwable) {
            $context->addWarning("Render Reusable View references missing node {$reference}.");
            return ['html' => '<!-- Reusable View is missing -->'];
        }

        if ((string) ($reusable['type'] ?? '') !== 'lifecycle.reusable_view') {
            $context->addWarning('Render Reusable View target is not a Reusable View node.');
            return ['html' => '<!-- Invalid Reusable View target -->'];
        }

        $owner = $context->graphOwnerNode();
        $reusableLooks = $context->connectedNodesOfType((string) $reusable['id'], 'looks.look', 'main');
        $ownerLooks = is_array($owner)
            ? $context->connectedNodesOfType((string) ($owner['id'] ?? ''), 'looks.look', 'main')
            : [];
        if (
            isset($reusableLooks[0]['id'], $ownerLooks[0]['id'])
            && (string) $reusableLooks[0]['id'] !== (string) $ownerLooks[0]['id']
        ) {
            $context->addWarning('Render Reusable View targets a partial connected to a different Look.');
        }

        $path = trim(str_replace('\\', '/', (string) ($reusable['data']['filename'] ?? '')), '/');
        if ($path === '') {
            $context->addWarning('The selected Reusable View has an empty partial path.');
            return ['html' => '<!-- Reusable View path is empty -->'];
        }

        $include = '<?php require current_look(' . $context->export($path) . '); ?>';
        if (!ViewNodeSupport::hasExplicitWrapper($data)) {
            return ['html' => $include];
        }

        return [
            'html' => '<div' . ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::classes($data),
                'style' => ViewNodeSupport::inlineStyle($data),
            ]) . ">\n" . $context->indent($include) . "\n</div>",
        ];
    }
}
