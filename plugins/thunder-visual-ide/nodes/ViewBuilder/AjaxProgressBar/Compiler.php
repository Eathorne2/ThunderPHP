<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class AjaxProgressBarCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') return [];
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $fillToken = preg_replace('/[^a-z0-9-]+/', '', strtolower((string) ($data['theme_color'] ?? 'primary'))) ?: 'primary';
        $trackToken = preg_replace('/[^a-z0-9-]+/', '', strtolower((string) ($data['track_color'] ?? 'border'))) ?: 'border';
        $height = trim((string) ($data['height'] ?? '10px')) ?: '10px';
        $style = '--thv-progress-fill:var(--thv-theme-' . $fillToken . ',#2563eb);'
            . '--thv-progress-track:var(--thv-theme-' . $trackToken . ',#e2e8f0);'
            . '--thv-progress-height:' . htmlspecialchars($height, ENT_QUOTES, 'UTF-8') . ';'
            . ViewNodeSupport::inlineStyle($data);
        $label = !empty($data['show_percentage'])
            ? '<span class="thv-ajax-progress__label" data-tvi-progress-label>0%</span>'
            : '';
        $html = '<div' . ViewNodeSupport::attributes([
            'class' => trim(ViewNodeSupport::widthClasses($data) . ' thv-ajax-progress ' . (string) ($data['css_class'] ?? '')),
            'style' => $style,
            'data-tvi-progress-node' => (string) ($node['id'] ?? ''),
            'role' => 'progressbar',
            'aria-valuemin' => '0',
            'aria-valuemax' => '100',
            'hidden' => !empty($data['initially_hidden']) ? 'hidden' : null,
        ]) . '><div class="thv-ajax-progress__track"><div class="thv-ajax-progress__fill" data-tvi-progress-fill style="width:0%"></div></div>' . $label . '</div>';
        return ['html' => $html];
    }
}
