<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class UpdateProgressCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $target = trim((string) ($node['data']['target_node_id'] ?? ''));
        if ($target === '') {
            $context->addWarning('Update AJAX Progress has no Progress Bar target.');
        }
        $progress = $context->inputJavaScriptExpression((string) $node['id'], 'progress', 'null');
        $value = $context->javascriptVariable($node, 'progress');
        $bars = $context->javascriptVariable($node, 'bars');
        $selector = '[data-tvi-progress-node="' . addcslashes($target, "\\\"") . '"]';
        $hide = !empty($node['data']['hide_when_complete']) ? 'true' : 'false';
        $code = 'const ' . $value . ' = ' . $progress . ';' . "\n"
            . 'const ' . $bars . ' = document.querySelectorAll(' . $context->javascriptExport($selector) . ');' . "\n"
            . $bars . '.forEach(function (root) {' . "\n"
            . '    const fill = root.querySelector("[data-tvi-progress-fill]");' . "\n"
            . '    const label = root.querySelector("[data-tvi-progress-label]");' . "\n"
            . '    const percent = ' . $value . ' && Number.isFinite(Number(' . $value . '.percent)) ? Math.max(0, Math.min(100, Number(' . $value . '.percent))) : null;' . "\n"
            . '    root.hidden = false;' . "\n"
            . '    root.classList.toggle("is-indeterminate", percent === null);' . "\n"
            . '    if (fill && percent !== null) fill.style.width = percent + "%";' . "\n"
            . '    if (label) label.textContent = percent === null ? "Working…" : Math.round(percent) + "%";' . "\n"
            . '    if (' . $hide . ' && percent !== null && percent >= 100) root.hidden = true;' . "\n"
            . '});';
        return ['code' => $code, 'next_port' => 'exec'];
    }
}
