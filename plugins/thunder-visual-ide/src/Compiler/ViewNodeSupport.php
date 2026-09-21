<?php

declare(strict_types=1);

namespace ThunderVisualIde\Compiler;

final class ViewNodeSupport
{
    /** @param array<string,mixed> $data */
    public static function widthClasses(array $data): string
    {
        $widths = is_array($data['widths'] ?? null) ? $data['widths'] : [];
        $mobile = self::column($widths['mobile'] ?? 12);
        $small = self::column($widths['small'] ?? $mobile);
        $medium = self::column($widths['medium'] ?? $small);
        $large = self::column($widths['large'] ?? $medium);
        return "thv-col-mobile-{$mobile} thv-col-small-{$small} thv-col-medium-{$medium} thv-col-large-{$large}";
    }

    /** @param array<string,mixed> $data */
    public static function classes(array $data, string ...$base): string
    {
        $classes = array_values(array_filter(array_map('trim', $base)));
        $classes[] = self::widthClasses($data);
        $custom = trim((string)($data['css_class'] ?? ''));
        if ($custom !== '') {
            $classes[] = $custom;
        }
        return trim(implode(' ', array_values(array_filter($classes))));
    }

    /** @param array<string,mixed> $data */
    public static function hasExplicitWrapper(
        array $data,
        string $classProperty = 'css_class',
        string $styleProperty = 'inline_style'
    ): bool {
        return trim((string) ($data[$classProperty] ?? '')) !== ''
            || trim((string) ($data[$styleProperty] ?? '')) !== '';
    }

    /** @param array<string,mixed> $data */
    public static function controlClasses(array $data): string
    {
        return trim((string) ($data['control_css_class'] ?? ''));
    }

    /** @param array<string,mixed> $data */
    public static function inlineStyle(array $data, string $property = 'inline_style'): string
    {
        return trim((string) ($data[$property] ?? ''));
    }

    public static function mergeStyles(string ...$styles): string
    {
        $compiled = [];
        foreach ($styles as $style) {
            $style = trim($style);
            if ($style === '') {
                continue;
            }
            $compiled[] = rtrim($style, " \t\n\r\0\x0B;") . ';';
        }

        return implode(' ', $compiled);
    }

    /** @param array<string,mixed> $data */
    public static function inlineErrorId(array $data, array $node, string $controlId): string
    {
        if (!self::showsInlineError($data)) {
            return '';
        }

        return $controlId . '-error';
    }

    /** @param array<string,mixed> $data */
    public static function inlineErrorHtml(array $data, array $node, string $controlId): string
    {
        if (!self::showsInlineError($data)) {
            return '';
        }

        $path = trim((string) ($data['error_path'] ?? ''));
        if ($path === '') {
            $path = self::defaultErrorPath((string) ($data['name'] ?? 'field'));
        } else {
            $path = self::path($path, 'errors.field');
        }

        $mode = strtolower(trim((string) ($data['error_display'] ?? 'first')));
        $mode = $mode === 'all' ? 'all' : 'first';
        $errorClass = trim('thv-error ' . trim((string) ($data['error_css_class'] ?? '')));
        $errorAttributes = self::attributes([
            'class' => $errorClass,
            'style' => self::inlineStyle($data, 'error_style'),
        ]);
        $errorId = self::inlineErrorId($data, $node, $controlId);
        $suffix = substr(preg_replace('/[^A-Za-z0-9_]/', '_', (string) ($node['id'] ?? 'field')) ?: 'field', -12);
        $valueVar = '$__tvi_field_error_value_' . $suffix;
        $messagesVar = '$__tvi_field_error_messages_' . $suffix;
        $itemVar = '$__tvi_field_error_item_' . $suffix;
        $messageVar = '$__tvi_field_error_message_' . $suffix;

        $code = "<?php
";
        $code .= $valueVar . ' = \get_nested_value(get_defined_vars(), ' . var_export($path, true) . ");
";
        $code .= $messagesVar . " = [];
";
        $code .= 'if (is_array(' . $valueVar . ') || ' . $valueVar . " instanceof \Traversable) {
";
        $code .= '    foreach (' . $valueVar . ' as ' . $itemVar . ") {
";
        $code .= '        if (is_scalar(' . $itemVar . ') || ' . $itemVar . " instanceof \Stringable) {
";
        $code .= '            ' . $messagesVar . '[] = (string) ' . $itemVar . ";
";
        $code .= "        }
    }
";
        $code .= '} elseif (is_scalar(' . $valueVar . ') || ' . $valueVar . " instanceof \Stringable) {
";
        $code .= '    ' . $messagesVar . '[] = (string) ' . $valueVar . ";
}
";
        $code .= $messagesVar . " = array_values(array_filter(" . $messagesVar . ", static fn (string \$message): bool => trim(\$message) !== ''));
";
        if ($mode === 'first') {
            $code .= $messagesVar . ' = array_slice(' . $messagesVar . ", 0, 1);
";
        }
        $code .= 'if (' . $messagesVar . " !== []): ?>
";
        $code .= '<div class="thv-errors" id="' . htmlspecialchars($errorId, ENT_QUOTES, 'UTF-8') . '" role="alert">' . "
";
        $code .= '<?php foreach (' . $messagesVar . ' as ' . $messageVar . '): ?>' . "
";
        $code .= '<div' . $errorAttributes . '><?= esc(' . $messageVar . ') ?></div>' . "
";
        $code .= "<?php endforeach; ?>
</div>
<?php endif; ?>";

        return $code;
    }

    /** @param array<string,mixed> $data */
    private static function showsInlineError(array $data): bool
    {
        return !array_key_exists('show_inline_error', $data)
            || filter_var($data['show_inline_error'], FILTER_VALIDATE_BOOL);
    }

    private static function defaultErrorPath(string $name): string
    {
        $name = preg_replace('/\[([^\]]+)\]/', '.$1', trim($name)) ?? trim($name);
        $name = trim(str_replace('[]', '', $name), '.');
        $path = self::path($name, 'field');
        return 'errors.' . $path;
    }

    /** @param array<string,mixed> $data */
    public static function themedWrapperAttributes(
        array $data,
        CompileContext $context,
        string ...$base
    ): string {
        return self::attributes([
            'class' => self::classes(
                $data,
                ...array_merge($base, [$context->currentFormThemeScopeClass()])
            ),
            'data-thv-form-theme' => $context->currentFormThemeScopeClass(),
            'style' => self::inlineStyle($data, 'wrapper_style'),
        ]);
    }

    /** @param array<string,string> $tokens */
    public static function themedComponent(
        CompileContext $context,
        string $component,
        array $tokens,
        string $fallback
    ): string {
        return $context->renderFormThemeComponent(
            $component,
            $tokens,
            $fallback
        );
    }

    /**
     * Remove CSS and executable JavaScript blocks from repeated loop markup so
     * they can be emitted once around the loop.
     *
     * @return array{html:string,styles:list<string>,scripts:list<string>}
     */
    public static function extractLoopAssets(string $html): array
    {
        $styles = [];
        $scripts = [];

        $html = preg_replace_callback(
            '~<style\b[^>]*>.*?</style>~is',
            static function (array $match) use (&$styles): string {
                $block = trim((string) ($match[0] ?? ''));
                if ($block !== '') {
                    $styles[$block] = $block;
                }
                return '';
            },
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '~<script\b([^>]*)>.*?</script>~is',
            static function (array $match) use (&$scripts): string {
                $attributes = (string) ($match[1] ?? '');
                if (preg_match('~\bdata-tvi-preserve-position(?:\s|=|$)~i', $attributes) === 1) {
                    return (string) ($match[0] ?? '');
                }
                if (preg_match(
                    '~\btype\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))~is',
                    $attributes,
                    $typeMatch
                ) === 1) {
                    $type = strtolower(trim((string) (
                        $typeMatch[1]
                        ?? $typeMatch[2]
                        ?? $typeMatch[3]
                        ?? ''
                    )));
                    if (!in_array($type, [
                        '',
                        'module',
                        'text/javascript',
                        'application/javascript',
                        'text/ecmascript',
                        'application/ecmascript',
                    ], true)) {
                        return (string) ($match[0] ?? '');
                    }
                }

                $block = trim((string) ($match[0] ?? ''));
                if ($block !== '') {
                    $scripts[$block] = $block;
                }
                return '';
            },
            $html
        ) ?? $html;

        return [
            'html' => trim($html),
            'styles' => array_values($styles),
            'scripts' => array_values($scripts),
        ];
    }

    /** @param array<string,string|int|bool|null> $attributes */
    public static function attributes(array $attributes): string
    {
        $compiled = [];
        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false || $value === '') {
                continue;
            }
            if ($value === true) {
                $compiled[] = self::attributeName((string)$name);
                continue;
            }
            $compiled[] = self::attributeName((string)$name) . '="' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '"';
        }
        return $compiled === [] ? '' : ' ' . implode(' ', $compiled);
    }

    public static function tag(string $tag, string $fallback = 'div'): string
    {
        $tag = strtolower(trim($tag));
        return preg_match('/^[a-z][a-z0-9-]*$/', $tag) === 1 ? $tag : $fallback;
    }

    public static function componentName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9]+/', '-', $name) ?? $name;
        return trim($name, '-');
    }

    public static function variableName(string $name, string $fallback = 'item'): string
    {
        $name = preg_replace('/[^A-Za-z0-9_]/', '', trim($name)) ?? '';
        if ($name === '' || preg_match('/^[A-Za-z_]/', $name) !== 1) {
            return $fallback;
        }
        return $name;
    }

    public static function path(string $path, string $fallback = 'value'): string
    {
        $segments = array_values(array_filter(array_map(
            static fn (string $segment): string => preg_replace('/[^A-Za-z0-9_]/', '', trim($segment)) ?: '',
            explode('.', trim($path))
        ), static fn (string $segment): bool => $segment !== ''));
        return $segments !== [] ? implode('.', $segments) : $fallback;
    }

    /** @return list<array{value:string,label:string}> */
    public static function options(string $lines): array
    {
        $options = [];
        foreach (preg_split('/\R/', $lines) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            [$value, $label] = array_pad(explode('|', $line, 2), 2, null);
            $value = trim((string)$value);
            $label = trim((string)($label ?? $value));
            $options[] = ['value' => $value, 'label' => $label];
        }
        return $options;
    }

    public static function baseCss(): string
    {
        $rules = [
            '.thv-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:var(--thv-gap,1rem);align-items:start}',
            '.thv-form{margin:0}',
            '.thv-field{display:grid;gap:.4rem;min-width:0}',
            '.thv-field > :where(label,.thv-label){font-weight:600}',
            '.thv-field :where(input:not([type=checkbox]):not([type=radio]),textarea,select){width:100%;max-width:100%;padding:var(--thv-control-padding,.7rem .8rem);border:var(--thv-control-border,1px solid #cbd5e1);border-radius:var(--thv-control-radius,.45rem);background:var(--thv-control-background,inherit);color:var(--thv-control-color,inherit);font:inherit}',
            '.thv-field :where(textarea){min-height:var(--thv-textarea-min-height,7rem);resize:var(--thv-textarea-resize,vertical)}',
            '.thv-checkbox{display:flex;align-items:flex-start;gap:.55rem}',
            '.thv-checkbox :where(input){margin-top:.2rem}',
            '.thv-button{display:inline-flex;align-items:center;justify-content:center;padding:.7rem 1rem;border:1px solid transparent;border-radius:.45rem;cursor:pointer;font:inherit}',
            '.thv-button--primary{background:#2563eb;color:#fff}',
            '.thv-button--secondary{background:#e2e8f0;color:#0f172a}',
            '.thv-errors{display:grid;gap:.25rem;margin-top:.1rem}',
            '.thv-error{color:var(--thv-theme-danger,#b91c1c);font-size:.875rem}',
            '.thv-flash{padding:.8rem 1rem;border-radius:.45rem;margin-bottom:1rem}',
            '.thv-flash--success{background:#dcfce7;color:#166534}',
            '.thv-flash--fail{background:#fee2e2;color:#991b1b}',
            '.thv-chart{position:relative;min-width:0;max-width:100%}',
            '.thv-chart canvas{display:block;width:100%!important;height:100%!important}',
            '.thv-chart__empty{display:flex;align-items:center;justify-content:center;min-height:8rem;padding:1rem;border:1px dashed #cbd5e1;border-radius:.55rem;color:#64748b;text-align:center}',
            '.thv-modal[hidden]{display:none!important}',
            '.thv-modal{box-sizing:border-box}',
            '.thv-modal__dialog{box-sizing:border-box;position:relative;max-width:100%;background:var(--thv-modal-background,#fff);color:var(--thv-modal-color,inherit);border-radius:var(--thv-modal-radius,.75rem);box-shadow:var(--thv-modal-shadow,0 24px 80px rgba(15,23,42,.35))}',
            '.thv-modal-open{overflow:hidden!important}',
            '.thv-modal-trigger{min-width:0}',
            '.thv-ajax-progress{display:flex;align-items:center;gap:.65rem;min-width:0}',
            '.thv-ajax-progress__track{position:relative;flex:1;min-width:4rem;height:var(--thv-progress-height,10px);overflow:hidden;border-radius:999px;background:var(--thv-progress-track,#e2e8f0)}',
            '.thv-ajax-progress__fill{height:100%;border-radius:inherit;background:var(--thv-progress-fill,#2563eb);transition:width .16s ease}',
            '.thv-ajax-progress__label{min-width:3ch;color:var(--thv-theme-muted,#64748b);font-size:.8rem;font-variant-numeric:tabular-nums;text-align:right}',
            '.thv-ajax-progress.is-indeterminate .thv-ajax-progress__fill{width:35%!important;animation:thv-progress-indeterminate 1.1s ease-in-out infinite}',
            '@keyframes thv-progress-indeterminate{0%{transform:translateX(-120%)}100%{transform:translateX(360%)}}',
        ];
        for ($i = 1; $i <= 12; $i++) {
            $rules[] = ".thv-col-mobile-{$i}{grid-column:span {$i}}";
        }
        $rules[] = '@media(min-width:576px){' . self::breakpointRules('small') . '}';
        $rules[] = '@media(min-width:768px){' . self::breakpointRules('medium') . '}';
        $rules[] = '@media(min-width:1200px){' . self::breakpointRules('large') . '}';
        return implode("\n", $rules) . "\n";
    }

    private static function breakpointRules(string $name): string
    {
        $rules = [];
        for ($i = 1; $i <= 12; $i++) {
            $rules[] = ".thv-col-{$name}-{$i}{grid-column:span {$i}}";
        }
        return implode('', $rules);
    }

    private static function column(mixed $value): int
    {
        return max(1, min(12, (int)$value));
    }

    private static function attributeName(string $name): string
    {
        $name = strtolower(trim($name));
        return preg_replace('/[^a-z0-9_:\-]/', '', $name) ?: 'data-value';
    }
}
