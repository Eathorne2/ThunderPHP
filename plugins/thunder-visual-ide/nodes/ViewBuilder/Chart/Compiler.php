<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ChartCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $suffix = substr(preg_replace('/[^A-Za-z0-9_]/', '_', (string) ($node['id'] ?? 'chart')) ?: 'chart', -12);
        $configVar = '$__tvi_chart_config_' . $suffix;
        $hasDataVar = '$__tvi_chart_has_data_' . $suffix;
        $canvasId = preg_replace('/[^A-Za-z0-9_:\-.]/', '-', trim((string) ($data['canvas_id'] ?? 'chartCanvas'))) ?: 'chartCanvas';
        $key = trim((string) ($data['data_key'] ?? 'chart')) ?: 'chart';
        $height = max(80, (int) ($data['height'] ?? 360));
        $width = trim((string) ($data['width'] ?? '100%')) ?: '100%';
        $chartAttributes = ViewNodeSupport::attributes([
            'class' => 'thv-chart',
            'style' => ViewNodeSupport::mergeStyles(
                'height:' . $height . 'px',
                'width:' . $width
            ),
        ]);
        $wrapperOpen = '';
        $wrapperClose = '';
        if (ViewNodeSupport::hasExplicitWrapper($data)) {
            $wrapperOpen = '<div' . ViewNodeSupport::attributes([
                'class' => ViewNodeSupport::classes($data),
                'style' => ViewNodeSupport::inlineStyle($data),
            ]) . '>';
            $wrapperClose = '</div>';
        }
        $empty = htmlspecialchars((string) ($data['empty_message'] ?? 'No chart data is available.'), ENT_QUOTES, 'UTF-8');
        $source = trim((string) ($data['chart_js_source'] ?? ''));
        $sourceVar = '$__tvi_chart_source_' . $suffix;
        $sourceExpression = $source !== ''
            ? var_export($source, true)
            : "plugin_http_path('assets/js/chart.min.js')";
        $canvasJson = json_encode($canvasId, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '"chartCanvas"';

        $loader = <<<'HTML'
<script>
window.__tviChartJsReady = window.__tviChartJsReady || new Promise(function (resolve, reject) {
    if (typeof window.Chart === 'function') { resolve(window.Chart); return; }
    var source = <?= json_encode(%%SOURCE_VAR%%, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;
    var existing = document.querySelector('script[data-tvi-chartjs]');
    if (existing) {
        existing.addEventListener('load', function () { resolve(window.Chart); }, { once: true });
        existing.addEventListener('error', reject, { once: true });
        return;
    }
    var script = document.createElement('script');
    script.src = source;
    script.dataset.tviChartjs = '1';
    script.addEventListener('load', function () { resolve(window.Chart); }, { once: true });
    script.addEventListener('error', reject, { once: true });
    var anchor = document.currentScript;
    if (anchor && anchor.parentNode) {
        anchor.parentNode.insertBefore(script, anchor.nextSibling);
    } else {
        document.head.appendChild(script);
    }
});
</script>
HTML;
        // Resolve placeholders inside the loader before inserting it into the
        // outer template. strtr() does not recursively process replacement
        // values, so leaving this for the outer pass would emit %%SOURCE_VAR%%
        // into generated PHP.
        $loader = strtr($loader, [
            '%%SOURCE_VAR%%' => $sourceVar,
        ]);

        $html = <<<'HTML'
<?php
%%CONFIG_VAR%% = get_value(%%DATA_KEY%%);
%%CONFIG_VAR%% = is_array(%%CONFIG_VAR%%) ? %%CONFIG_VAR%% : [];
%%HAS_DATA_VAR%% = !empty(%%CONFIG_VAR%%['data']['datasets']) && is_array(%%CONFIG_VAR%%['data']['datasets']);
%%SOURCE_VAR%% = %%SOURCE_EXPRESSION%%;
?>
%%WRAPPER_OPEN%%<div%%CHART_ATTRIBUTES%%>
<?php if (%%HAS_DATA_VAR%%): ?>
    <canvas id="%%CANVAS_ID%%"></canvas>
    %%LOADER%%
    <script>
    (function () {
        var canvasId = %%CANVAS_JSON%%;
        var config = <?= json_encode(%%CONFIG_VAR%%, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;
        var render = function () {
            var canvas = document.getElementById(canvasId);
            if (!canvas) return;
            if (typeof window.Chart !== 'function') {
                canvas.insertAdjacentHTML('afterend', '<div class="thv-chart__empty">Chart.js is not loaded.</div>');
                return;
            }
            if (typeof window.Chart.getChart === 'function') {
                var existing = window.Chart.getChart(canvas);
                if (existing) existing.destroy();
            }
            new window.Chart(canvas, config);
        };
        var ready = window.__tviChartJsReady;
        if (ready && typeof ready.then === 'function') {
            ready.then(render).catch(function () { render(); });
        } else if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', render, { once: true });
        } else {
            render();
        }
    })();
    </script>
<?php else: ?>
    <div class="thv-chart__empty">%%EMPTY_MESSAGE%%</div>
<?php endif; ?>
</div>%%WRAPPER_CLOSE%%
HTML;
        $html = strtr($html, [
            '%%CONFIG_VAR%%' => $configVar,
            '%%HAS_DATA_VAR%%' => $hasDataVar,
            '%%SOURCE_VAR%%' => $sourceVar,
            '%%SOURCE_EXPRESSION%%' => $sourceExpression,
            '%%DATA_KEY%%' => var_export($key, true),
            '%%WRAPPER_OPEN%%' => $wrapperOpen,
            '%%CHART_ATTRIBUTES%%' => $chartAttributes,
            '%%WRAPPER_CLOSE%%' => $wrapperClose,
            '%%CANVAS_ID%%' => htmlspecialchars($canvasId, ENT_QUOTES, 'UTF-8'),
            '%%CANVAS_JSON%%' => $canvasJson,
            '%%LOADER%%' => $loader,
            '%%EMPTY_MESSAGE%%' => $empty,
        ]);

        return [
            'html' => trim($html),
            'component_name' => ViewNodeSupport::componentName((string) ($data['component_name'] ?? '')),
        ];
    }
}
