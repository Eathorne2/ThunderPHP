<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class HtmlBoilerplateCompiler implements NodeCompilerInterface
{
    private const VIEW_STYLES_MARKER = '<tvi-view-styles />';
    private const VIEW_CONTENT_MARKER = '<tvi-view-content />';

    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $document = trim((string) ($data['html_code'] ?? ''));
        if ($document === '') {
            $context->addWarning('HTML Boilerplate has no document code.');
            $document = "<!doctype html>\n<html>\n<head></head>\n<body>\n    <main style=\"margin-left:auto;margin-right:auto;max-width:1200px\">\n        <tvi-content />\n    </main>\n</body>\n</html>";
        }

        $children = $context->compileViewChildrenDetailed(
            (string) ($node['id'] ?? ''),
            'children'
        );
        $childHtml = trim((string) ($children['html'] ?? ''));
        $requiresBaseCss = !empty($children['uses_base_css']);

        $insertion = self::VIEW_STYLES_MARKER
            . "\n"
            . self::VIEW_CONTENT_MARKER;
        if ($childHtml !== '') {
            $insertion .= "\n" . $childHtml;
        }

        $pattern = '/(?P<indent>^[ \t]*)?<tvi-(?:content|children)\s*\/?>/im';
        if (preg_match($pattern, $document) === 1) {
            $document = preg_replace_callback(
                $pattern,
                fn (array $matches): string => $this->indentPlacement(
                    $insertion,
                    (string) ($matches['indent'] ?? '')
                ),
                $document,
                1
            ) ?? $document;
        } elseif ($this->usesHtmlContentHook($document)) {
            if ($childHtml !== '') {
                $context->addWarning(
                    'HTML Boilerplate uses the html_content hook, so directly connected child components were not inserted. Render them from a View attached to html_content, or add <tvi-content /> to the boilerplate.'
                );
            }
            $requiresBaseCss = false;
        } else {
            $context->addWarning(
                'HTML Boilerplate has no <tvi-content /> marker; content was inserted before </body>.'
            );
            if (preg_match('/<\/body\s*>/i', $document) === 1) {
                $document = preg_replace_callback(
                    '/(?P<indent>^[ \t]*)<\/body\s*>/im',
                    fn (array $matches): string => $this->indentPlacement(
                        $insertion,
                        (string) ($matches['indent'] ?? '')
                    ) . "\n" . (string) ($matches['indent'] ?? '') . '</body>',
                    $document,
                    1
                ) ?? $document;
            } else {
                $document = rtrim($document) . "\n" . $insertion . "\n";
            }
        }

        return [
            'html' => $document,
            'is_document' => true,
            'uses_base_css' => false,
            'requires_base_css_asset' => $requiresBaseCss,
        ];
    }

    private function usesHtmlContentHook(string $document): bool
    {
        return preg_match(
            "/do_action\s*\(\s*['\"]html_content['\"]\s*\)/i",
            $document
        ) === 1;
    }

    private function indentPlacement(string $placement, string $indent): string
    {
        if ($indent === '' || $placement === '') {
            return $placement;
        }

        return $indent . str_replace("\n", "\n" . $indent, $placement);
    }

}
