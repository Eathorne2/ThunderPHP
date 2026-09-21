<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Looks;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class LookCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $folder = $this->cleanPath((string) ($data['folder'] ?? 'main'), 'main');
        $themePath = $this->cleanPath(
            (string) ($data['theme_css_path'] ?? 'frontend/theme-colors.php'),
            'frontend/theme-colors.php'
        );

        $files = [
            "looks/{$folder}/{$themePath}" => $this->compileThemeFile($node, $context),
        ];
        $hooks = [];

        if ($this->boolValue($data['load_theme_css'] ?? true)) {
            $hookName = trim((string) ($data['theme_hook_name'] ?? 'before_view')) ?: 'before_view';
            $priority = (int) ($data['theme_hook_priority'] ?? 100);
            $hooks[] = $this->compileThemeHook($hookName, $priority, $themePath);
        }

        return [
            'looks' => [$data],
            'files' => $files,
            'hooks' => $hooks,
        ];
    }

    private function compileThemeFile(array $node, CompileContext $context): string
    {
        $css = trim($context->formThemeCssForLook($node));
        $js = trim($context->formThemeJsForLook($node));
        $scope = ltrim($context->formThemeScopeForLook($node), '.');

        $source = "<?php\n\ndeclare(strict_types=1);\n\n?>\n";
        $source .= '<style data-thv-form-theme="' . htmlspecialchars($scope, ENT_QUOTES, 'UTF-8') . '">' . "\n";
        $source .= $css . "\n</style>\n";

        if ($js !== '' && !str_starts_with($js, '//')) {
            $source .= "<script>\n";
            $source .= "document.addEventListener('DOMContentLoaded', function () {\n";
            $source .= $this->indent($js, 1) . "\n";
            $source .= "});\n";
            $source .= "</script>\n";
        }

        return $source;
    }

    private function compileThemeHook(string $hookName, int $priority, string $themePath): string
    {
        return "add_action(" . var_export($hookName, true) . ", function (): void {\n"
            . "    \$themeFile = current_look(" . var_export($themePath, true) . ");\n"
            . "    if (is_file(\$themeFile)) {\n"
            . "        require_once \$themeFile;\n"
            . "    }\n"
            . "}, {$priority});";
    }

    private function cleanPath(string $path, string $fallback): string
    {
        $path = trim(str_replace('\\', '/', $path), '/');
        if ($path === '' || str_contains($path, '..')) {
            return $fallback;
        }
        return preg_replace('#/+#', '/', $path) ?: $fallback;
    }

    private function boolValue(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
    }

    private function indent(string $source, int $level): string
    {
        $prefix = str_repeat('    ', max(0, $level));
        return implode("\n", array_map(
            static fn (string $line): string => $line === '' ? '' : $prefix . $line,
            preg_split('/\\R/', $source) ?: []
        ));
    }
}
