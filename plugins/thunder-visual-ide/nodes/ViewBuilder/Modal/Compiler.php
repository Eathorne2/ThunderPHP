<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\ModalNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ModalCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $modalId = ViewNodeSupport::componentName((string) ($data['modal_id'] ?? ''));
        if ($modalId === '') {
            $modalId = 'modal-' . substr((string) ($node['id'] ?? 'dialog'), -8);
            $context->addWarning("Modal node generated fallback ID {$modalId} because Modal ID was empty.");
        }

        $matchingModalIds = 0;
        foreach ($context->nodesOfType('view.modal', $context->graphId()) as $candidate) {
            $candidateId = ViewNodeSupport::componentName((string) ($candidate['data']['modal_id'] ?? ''));
            if ($candidateId === $modalId) {
                $matchingModalIds++;
            }
        }
        if ($matchingModalIds > 1) {
            $context->addWarning("Modal ID {$modalId} is used by more than one Modal in the same View graph.");
        }

        $positionType = in_array((string) ($data['position_type'] ?? ''), ['fixed', 'absolute'], true)
            ? (string) $data['position_type']
            : 'fixed';
        [$alignItems, $justifyContent] = $this->alignment((string) ($data['content_position'] ?? 'center'));
        $zIndex = max(1, (int) ($data['z_index'] ?? 1000));
        $backdrop = trim((string) ($data['backdrop_color'] ?? 'rgba(15, 23, 42, 0.65)'));
        $width = trim((string) ($data['content_width'] ?? 'min(92vw, 42rem)'));
        $maxHeight = trim((string) ($data['content_max_height'] ?? 'min(88vh, 52rem)'));
        $fallbackOpen = !empty($data['initial_open']);
        $initialOpenExpression = $context->inputExpression(
            (string) $node['id'],
            'initial_open',
            $fallbackOpen ? 'true' : 'false'
        );
        $stateSuffix = preg_replace('/[^A-Za-z0-9_]/', '_', (string) ($node['id'] ?? 'modal')) ?: 'modal';
        $stateVariable = '$__tvi_modal_initial_open_' . $stateSuffix;
        $children = $context->compileViewChildren((string) $node['id']);

        $overlayStyle = ViewNodeSupport::mergeStyles(
            "position:{$positionType}",
            'inset:0',
            'display:flex',
            "align-items:{$alignItems}",
            "justify-content:{$justifyContent}",
            'padding:1rem',
            "z-index:{$zIndex}",
            $backdrop !== '' ? "background:{$backdrop}" : ''
        );
        $contentStyle = ViewNodeSupport::mergeStyles(
            $width !== '' ? "width:{$width}" : '',
            $maxHeight !== '' ? "max-height:{$maxHeight}" : '',
            'overflow:auto',
            ViewNodeSupport::inlineStyle($data, 'content_style')
        );

        $dynamicStateAttributes = ' aria-hidden="<?= ' . $stateVariable
            . " ? 'false' : 'true' ?>\""
            . '<?php if (!' . $stateVariable . '): ?> hidden<?php endif; ?>';
        $html = '<?php ' . $stateVariable . ' = (bool) (' . $initialOpenExpression . '); ?>' . "\n";
        $html .= '<div' . ViewNodeSupport::attributes([
            'id' => $modalId,
            'class' => trim('thv-modal ' . trim((string) ($data['css_class'] ?? ''))),
            'style' => $overlayStyle,
            'data-tvi-modal' => '1',
            'data-tvi-close-backdrop' => !empty($data['close_on_backdrop']) ? '1' : '0',
            'data-tvi-close-escape' => !empty($data['close_on_escape']) ? '1' : '0',
            'data-tvi-lock-scroll' => !empty($data['lock_body_scroll']) ? '1' : '0',
            'tabindex' => '-1',
        ]) . $dynamicStateAttributes . ">\n";
        $html .= '    <div' . ViewNodeSupport::attributes([
            'class' => trim('thv-modal__dialog ' . trim((string) ($data['content_css_class'] ?? ''))),
            'style' => $contentStyle,
            'role' => 'dialog',
            'aria-modal' => 'true',
            'aria-label' => trim((string) ($data['aria_label'] ?? 'Dialog')) ?: 'Dialog',
        ]) . ">\n";
        $html .= $context->indent($children, 2) . "\n";
        $html .= "    </div>\n</div>\n" . ModalNodeSupport::runtimeScript();

        return ['html' => $html];
    }

    /** @return array{string,string} */
    private function alignment(string $position): array
    {
        return match ($position) {
            'top' => ['flex-start', 'center'],
            'bottom' => ['flex-end', 'center'],
            'left' => ['center', 'flex-start'],
            'right' => ['center', 'flex-end'],
            default => ['center', 'center'],
        };
    }
}
