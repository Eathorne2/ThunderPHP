<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;
use ThunderVisualIde\Html\CssScope;

final class HtmlComponentCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $componentName = ViewNodeSupport::componentName(
            (string) ($data['component_name'] ?? 'custom-component')
        ) ?: 'custom-component';
        $scopeClass = 'tvi-html-' . $componentName;
        $themeScopeClass = $context->currentFormThemeScopeClass();
        $classes = trim(implode(' ', array_filter([
            $scopeClass,
            $themeScopeClass,
            ViewNodeSupport::widthClasses($data),
        ])));
        $html = trim((string) ($data['html_code'] ?? ''));
        $css = CssScope::scope(
            (string) ($data['css_code'] ?? ''),
            '.' . $scopeClass
        );
        $javascript = trim((string) ($data['js_code'] ?? ''));

        $output = '<div'
            . ViewNodeSupport::attributes([
                'class' => $classes,
                'data-tvi-html-component' => $componentName,
                'data-thv-form-theme' => $themeScopeClass,
            ])
            . ">\n"
            . $context->indent($html)
            . "\n</div>";

        if ($javascript !== '') {
            $safeJavascript = str_replace('</script', '<\\/script', $javascript);
            $selector = json_encode(
                '[data-tvi-html-component="' . $componentName . '"]',
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            $output .= <<<HTML

<script>
(function () {
    const initialise = function () {
        document.querySelectorAll({$selector}).forEach(function (root) {
            if (root.dataset.tviHtmlReady === '1') {
                return;
            }

            root.dataset.tviHtmlReady = '1';

            try {
                (function (root) {
{$context->indent($safeJavascript, 5)}
                })(root);
            } catch (error) {
                console.error('HTML component {$componentName} failed:', error);
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise, { once: true });
    } else {
        initialise();
    }
})();
</script>
HTML;
        }

        return [
            'html' => $output,
            'css' => $css,
            'component_name' => $componentName,
        ];
    }
}
