<?php
$__tviPagerVariable = %%PAGER_VARIABLE%%;
$__tviPager = get_defined_vars()[$__tviPagerVariable] ?? null;

if ($__tviPager instanceof \Core\Pager):
    $__tviPager->set_labels(%%LABELS%%);
    $__tviPager->set_renderer(static function (array $items, \Core\Pager $pager): string {
        $wrapperClass = %%WRAPPER_CLASS%%;
        $buttonsClass = %%BUTTONS_CLASS%%;
        $buttonClass = %%BUTTON_CLASS%%;
        $activeClass = %%ACTIVE_CLASS%%;
        $disabledClass = %%DISABLED_CLASS%%;
        $ellipsisClass = %%ELLIPSIS_CLASS%%;
        $summaryClass = %%SUMMARY_CLASS%%;
        $wrapperStyle = %%WRAPPER_STYLE%%;
        $buttonsStyle = %%BUTTONS_STYLE%%;
        $buttonStyle = %%BUTTON_STYLE%%;
        $activeStyle = %%ACTIVE_STYLE%%;
        $disabledStyle = %%DISABLED_STYLE%%;
        $ellipsisStyle = %%ELLIPSIS_STYLE%%;
        $summaryStyle = %%SUMMARY_STYLE%%;
        $showSummary = %%SHOW_SUMMARY%%;
        $styleAttribute = static function (string ...$styles): string {
            $styles = array_values(array_filter(array_map(
                static fn (string $style): string => trim($style, " \t\n\r\0\x0B;"),
                $styles
            ), static fn (string $style): bool => $style !== ''));
            if ($styles === []) {
                return '';
            }
            return ' style="' . htmlspecialchars(implode('; ', $styles) . ';', ENT_QUOTES, 'UTF-8') . '"';
        };

        $html = '<nav class="' . htmlspecialchars($wrapperClass, ENT_QUOTES, 'UTF-8') . '"' . $styleAttribute($wrapperStyle) . ' aria-label="Pagination">';

        if ($showSummary) {
            $html .= '<div class="' . htmlspecialchars($summaryClass, ENT_QUOTES, 'UTF-8') . '"' . $styleAttribute($summaryStyle) . '>'
                . htmlspecialchars($pager->summary_text(), ENT_QUOTES, 'UTF-8')
                . '</div>';
        }

        $html .= '<div class="' . htmlspecialchars($buttonsClass, ENT_QUOTES, 'UTF-8') . '"' . $styleAttribute($buttonsStyle) . '>';

        foreach ($items as $item) {
            $label = htmlspecialchars((string) ($item['label'] ?? ''), ENT_QUOTES, 'UTF-8');
            $class = trim($buttonClass
                . (!empty($item['active']) ? ' ' . $activeClass : '')
                . (!empty($item['disabled']) ? ' ' . $disabledClass : '')
                . (($item['type'] ?? '') === 'ellipsis' ? ' ' . $ellipsisClass : ''));
            $safeClass = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');
            $itemStyle = $styleAttribute(
                $buttonStyle,
                !empty($item['active']) ? $activeStyle : '',
                !empty($item['disabled']) ? $disabledStyle : '',
                ($item['type'] ?? '') === 'ellipsis' ? $ellipsisStyle : ''
            );

            if (($item['type'] ?? '') === 'ellipsis' || !empty($item['disabled'])) {
                $html .= '<span class="' . $safeClass . '"' . $itemStyle . '>' . $label . '</span>';
                continue;
            }

            $url = htmlspecialchars((string) ($item['url'] ?? '#'), ENT_QUOTES, 'UTF-8');
            $ariaCurrent = !empty($item['active']) ? ' aria-current="page"' : '';
            $html .= '<a class="' . $safeClass . '"' . $itemStyle . ' href="' . $url . '"' . $ariaCurrent . '>' . $label . '</a>';
        }

        return $html . '</div></nav>';
    });
?>
<?php if (%%BASE_STYLES%% || trim(%%CUSTOM_CSS%%) !== ''): ?>
<style>
%%COMPILED_CSS%%
</style>
<?php endif; ?>
<?= $__tviPager->render() ?>
<?php endif; ?>
