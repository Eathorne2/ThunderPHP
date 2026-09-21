<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\ModalNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ModalTriggerCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $modal = $context->connectedNodes(
            (string) $node['id'],
            'modal',
            'out',
            $context->graphId()
        )[0] ?? null;

        if (!is_array($modal)) {
            $reference = trim((string) ($data['modal_node_id'] ?? ''));
            if ($reference !== '') {
                try {
                    $candidate = $context->node($reference, $context->graphId());
                    if ((string) ($candidate['type'] ?? '') === 'view.modal') {
                        $modal = $candidate;
                    }
                } catch (\Throwable) {
                    $modal = null;
                }
            }
        }

        if (!is_array($modal) || (string) ($modal['type'] ?? '') !== 'view.modal') {
            $context->addWarning('Modal Trigger has no valid Modal target.');
            return ['html' => '<!-- Modal Trigger has no target -->'];
        }

        $modalId = ViewNodeSupport::componentName((string) ($modal['data']['modal_id'] ?? ''));
        if ($modalId === '') {
            $modalId = 'modal-' . substr((string) ($modal['id'] ?? 'dialog'), -8);
        }
        $action = in_array((string) ($data['action'] ?? ''), ['open', 'close', 'toggle'], true)
            ? (string) $data['action']
            : 'open';
        $source = trim((string) ($data['html'] ?? ''));
        if ($source === '') {
            $source = '<button type="button">' . ucfirst($action) . ' modal</button>';
        }

        $trigger = ModalNodeSupport::addAttributesToFirstElement($source, [
            'data-tvi-modal-target' => $modalId,
            'data-tvi-modal-action' => $action,
            'aria-controls' => $modalId,
            'aria-haspopup' => $action === 'close' ? null : 'dialog',
            'aria-expanded' => !empty($modal['data']['initial_open']) ? 'true' : 'false',
        ]);
        if ($trigger === '') {
            $context->addWarning('Modal Trigger HTML did not contain a valid first element; a button was generated.');
            $label = trim(strip_tags($source));
            $label = $label !== '' ? $label : ucfirst($action) . ' modal';
            $trigger = '<button' . ViewNodeSupport::attributes([
                'type' => 'button',
                'data-tvi-modal-target' => $modalId,
                'data-tvi-modal-action' => $action,
                'aria-controls' => $modalId,
                'aria-haspopup' => $action === 'close' ? null : 'dialog',
                'aria-expanded' => !empty($modal['data']['initial_open']) ? 'true' : 'false',
            ]) . '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</button>';
        }

        $html = '<div' . ViewNodeSupport::attributes([
            'class' => trim('thv-modal-trigger ' . ViewNodeSupport::widthClasses($data)),
        ]) . ">\n" . $context->indent($trigger) . "\n</div>\n";
        $html .= ModalNodeSupport::runtimeScript();

        return [
            'html' => $html,
            'component_name' => ViewNodeSupport::componentName((string) ($data['component_name'] ?? '')),
        ];
    }
}
