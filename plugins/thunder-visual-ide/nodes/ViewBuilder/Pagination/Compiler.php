<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;
use ThunderVisualIde\Pagination\PaginationTemplateRegistry;

final class PaginationCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $registry = new PaginationTemplateRegistry(
            dirname(__DIR__, 3) . '/pagination-templates'
        );
        $templateId = trim((string) ($data['template_id'] ?? 'minimal'));
        $definition = $registry->get($templateId);
        $defaults = is_array($definition['defaults'] ?? null)
            ? $definition['defaults']
            : [];
        $settings = array_merge($defaults, $data);

        $componentName = ViewNodeSupport::componentName(
            (string) ($settings['component_name'] ?? 'pagination')
        ) ?: 'pagination';
        $scopeClass = 'tvi-pagination-' . $componentName;
        $wrapperClass = trim(implode(' ', array_filter([
            $scopeClass,
            ViewNodeSupport::widthClasses($settings),
            trim((string) ($settings['wrapper_class'] ?? '')),
        ])));

        $baseCss = !empty($settings['base_styles'])
            ? (string) ($definition['css'] ?? '')
            : '';
        $customCss = trim((string) ($settings['custom_css'] ?? ''));
        $cssTokens = [
            '{{scope}}' => $scopeClass,
            '{{wrapper}}' => $this->cssClass((string) ($settings['wrapper_class'] ?? ''), $scopeClass),
            '{{buttons}}' => $this->cssClass((string) ($settings['buttons_class'] ?? ''), 'tvi-pager-buttons'),
            '{{button}}' => $this->cssClass((string) ($settings['button_class'] ?? ''), 'tvi-pager-button'),
            '{{active}}' => $this->cssClass((string) ($settings['active_class'] ?? ''), 'is-active'),
            '{{disabled}}' => $this->cssClass((string) ($settings['disabled_class'] ?? ''), 'is-disabled'),
            '{{ellipsis}}' => $this->cssClass((string) ($settings['ellipsis_class'] ?? ''), 'is-ellipsis'),
            '{{summary}}' => $this->cssClass((string) ($settings['summary_class'] ?? ''), 'tvi-pager-summary'),
            ...$this->colorTokens(
                $settings,
                is_array($definition['palette'] ?? null) ? $definition['palette'] : []
            ),
        ];
        $compiledCss = trim(strtr(trim($baseCss . "\n" . $customCss), $cssTokens));

        $labels = [
            'first' => (string) ($settings['first_label'] ?? 'First'),
            'prev' => (string) ($settings['prev_label'] ?? 'Previous'),
            'next' => (string) ($settings['next_label'] ?? 'Next'),
            'last' => (string) ($settings['last_label'] ?? 'Last'),
        ];

        $replacements = [
            '%%PAGER_VARIABLE%%' => var_export(
                ViewNodeSupport::variableName(
                    (string) ($settings['pager_variable'] ?? 'pager'),
                    'pager'
                ),
                true
            ),
            '%%LABELS%%' => var_export($labels, true),
            '%%WRAPPER_CLASS%%' => var_export($wrapperClass, true),
            '%%BUTTONS_CLASS%%' => var_export(trim((string) ($settings['buttons_class'] ?? '')), true),
            '%%BUTTON_CLASS%%' => var_export(trim((string) ($settings['button_class'] ?? '')), true),
            '%%ACTIVE_CLASS%%' => var_export(trim((string) ($settings['active_class'] ?? '')), true),
            '%%DISABLED_CLASS%%' => var_export(trim((string) ($settings['disabled_class'] ?? '')), true),
            '%%ELLIPSIS_CLASS%%' => var_export(trim((string) ($settings['ellipsis_class'] ?? '')), true),
            '%%SUMMARY_CLASS%%' => var_export(trim((string) ($settings['summary_class'] ?? '')), true),
            '%%WRAPPER_STYLE%%' => var_export(trim((string) ($settings['wrapper_style'] ?? '')), true),
            '%%BUTTONS_STYLE%%' => var_export(trim((string) ($settings['buttons_style'] ?? '')), true),
            '%%BUTTON_STYLE%%' => var_export(trim((string) ($settings['button_style'] ?? '')), true),
            '%%ACTIVE_STYLE%%' => var_export(trim((string) ($settings['active_style'] ?? '')), true),
            '%%DISABLED_STYLE%%' => var_export(trim((string) ($settings['disabled_style'] ?? '')), true),
            '%%ELLIPSIS_STYLE%%' => var_export(trim((string) ($settings['ellipsis_style'] ?? '')), true),
            '%%SUMMARY_STYLE%%' => var_export(trim((string) ($settings['summary_style'] ?? '')), true),
            '%%SHOW_SUMMARY%%' => !empty($settings['show_summary']) ? 'true' : 'false',
            '%%BASE_STYLES%%' => !empty($settings['base_styles']) ? 'true' : 'false',
            '%%CUSTOM_CSS%%' => var_export($customCss, true),
            '%%COMPILED_CSS%%' => $compiledCss,
        ];

        return [
            'html' => strtr($registry->renderer($templateId), $replacements),
            'component_name' => $componentName,
        ];
    }


    /**
     * @param array<string,mixed> $settings
     * @param array<string,mixed> $defaults
     * @return array<string,string>
     */
    private function colorTokens(array $settings, array $defaults): array
    {
        $roles = [
            'button_background' => ['custom_button_background', 'theme_button_background', 'surface', '#ffffff'],
            'button_text' => ['custom_button_text', 'theme_button_text', 'text', '#334155'],
            'button_border' => ['custom_button_border', 'theme_button_border', 'border', '#cbd5e1'],
            'hover_background' => ['custom_hover_background', 'theme_hover_background', 'surface_alt', '#f8fafc'],
            'hover_text' => ['custom_hover_text', 'theme_hover_text', 'text', '#0f172a'],
            'active_background' => ['custom_active_background', 'theme_active_background', 'primary', '#2563eb'],
            'active_text' => ['custom_active_text', 'theme_active_text', 'on_primary', '#ffffff'],
            'disabled_background' => ['custom_disabled_background', 'theme_disabled_background', 'surface_alt', '#f8fafc'],
            'disabled_text' => ['custom_disabled_text', 'theme_disabled_text', 'muted', '#64748b'],
            'summary_text' => ['custom_summary_text', 'theme_summary_text', 'muted', '#64748b'],
        ];
        $mode = strtolower(trim((string) ($settings['color_mode'] ?? 'defaults')));
        if (!in_array($mode, ['defaults', 'custom', 'inherit'], true)) {
            $mode = 'defaults';
        }

        $tokens = [];
        foreach ($roles as $role => [$customKey, $themeKey, $themeDefault, $fallback]) {
            $default = $this->cssColorValue((string) ($defaults[$role] ?? ''), $fallback);
            if ($mode === 'custom') {
                $value = $this->cssColorValue((string) ($settings[$customKey] ?? ''), $default);
            } elseif ($mode === 'inherit') {
                $theme = $this->themeColorName((string) ($settings[$themeKey] ?? $themeDefault), $themeDefault);
                $value = 'var(--thv-theme-' . str_replace('_', '-', $theme) . ', ' . $default . ')';
            } else {
                $value = $default;
            }
            $tokens['{{' . $role . '}}'] = $value;
        }

        return $tokens;
    }

    private function cssColorValue(string $value, string $fallback): string
    {
        $value = trim($value);
        if ($value === '' || preg_match('/[;{}<>]/', $value) === 1) {
            return $fallback;
        }
        return $value;
    }

    private function themeColorName(string $value, string $fallback): string
    {
        $allowed = [
            'primary', 'secondary', 'surface', 'surface_alt', 'text', 'muted',
            'border', 'on_primary', 'danger', 'success', 'warning', 'info',
        ];
        $value = strtolower(trim($value));
        return in_array($value, $allowed, true) ? $value : $fallback;
    }

    private function cssClass(string $classes, string $fallback): string
    {
        foreach (preg_split('/\s+/', trim($classes)) ?: [] as $class) {
            $class = preg_replace('/[^A-Za-z0-9_-]/', '', $class) ?? '';
            if ($class !== '') {
                return $class;
            }
        }
        return $fallback;
    }
}
