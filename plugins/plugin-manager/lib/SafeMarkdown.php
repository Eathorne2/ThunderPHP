<?php

namespace PluginManager;

if(!defined('ROOT')) exit('No direct script access allowed');

/**
 * Small, dependency-free Markdown fallback used when Parsedown is unavailable.
 * It intentionally supports the README features plugin authors use most often.
 */
final class SafeMarkdown
{
    private string $pluginId;

    public function __construct(string $pluginId = '')
    {
        $this->pluginId = clean_id($pluginId);
    }

    public function text(string $markdown): string
    {
        $markdown = str_replace(["\r\n", "\r"], "\n", trim($markdown));
        if($markdown === '') return '';

        $lines = explode("\n", $markdown);
        $html = [];
        $count = count($lines);

        for($i = 0; $i < $count; $i++){
            $line = rtrim($lines[$i]);
            if(trim($line) === '') continue;

            if(preg_match('/^\s*```\s*([^\s`]*)\s*$/', $line, $match)){
                $language = preg_replace('/[^a-zA-Z0-9_-]/', '', $match[1] ?? '');
                $code = [];
                while(++$i < $count && !preg_match('/^\s*```\s*$/', $lines[$i])) $code[] = $lines[$i];
                $class = $language !== '' ? ' class="language-' . e($language) . '"' : '';
                $html[] = '<pre><code' . $class . '>' . e(implode("\n", $code)) . '</code></pre>';
                continue;
            }

            if($i + 1 < $count && $this->isTableDivider($lines[$i + 1]) && str_contains($line, '|')){
                $headers = $this->tableCells($line);
                $alignments = $this->tableAlignments($lines[$i + 1]);
                $rows = [];
                $i += 2;
                while($i < $count && trim($lines[$i]) !== '' && str_contains($lines[$i], '|')){
                    $rows[] = $this->tableCells($lines[$i]);
                    $i++;
                }
                $i--;

                $table = '<div class="pm-markdown-table"><table><thead><tr>';
                foreach($headers as $index => $cell){
                    $align = $alignments[$index] ?? '';
                    $style = $align !== '' ? ' style="text-align:' . $align . '"' : '';
                    $table .= '<th' . $style . '>' . $this->inline($cell) . '</th>';
                }
                $table .= '</tr></thead><tbody>';
                foreach($rows as $row){
                    $table .= '<tr>';
                    foreach($headers as $index => $_){
                        $align = $alignments[$index] ?? '';
                        $style = $align !== '' ? ' style="text-align:' . $align . '"' : '';
                        $table .= '<td' . $style . '>' . $this->inline($row[$index] ?? '') . '</td>';
                    }
                    $table .= '</tr>';
                }
                $html[] = $table . '</tbody></table></div>';
                continue;
            }

            if(preg_match('/^(#{1,6})\s+(.+)$/', $line, $match)){
                $level = strlen($match[1]);
                $html[] = '<h' . $level . '>' . $this->inline(trim($match[2])) . '</h' . $level . '>';
                continue;
            }

            if($i + 1 < $count && preg_match('/^\s*(=+|-+)\s*$/', $lines[$i + 1], $match)){
                $level = str_starts_with(trim($match[1]), '=') ? 1 : 2;
                $html[] = '<h' . $level . '>' . $this->inline(trim($line)) . '</h' . $level . '>';
                $i++;
                continue;
            }

            if(preg_match('/^\s*(?:-{3,}|\*{3,}|_{3,})\s*$/', $line)){
                $html[] = '<hr>';
                continue;
            }

            if(preg_match('/^\s*>\s?(.*)$/', $line, $match)){
                $quote = [$match[1]];
                while($i + 1 < $count && preg_match('/^\s*>\s?(.*)$/', $lines[$i + 1], $next)){
                    $quote[] = $next[1];
                    $i++;
                }
                $html[] = '<blockquote><p>' . $this->inline(implode("\n", $quote), true) . '</p></blockquote>';
                continue;
            }

            if(preg_match('/^\s*[-+*]\s+(.+)$/', $line, $match)){
                $items = [$match[1]];
                while($i + 1 < $count && preg_match('/^\s*[-+*]\s+(.+)$/', $lines[$i + 1], $next)){
                    $items[] = $next[1];
                    $i++;
                }
                $list = '<ul>';
                foreach($items as $item) $list .= '<li>' . $this->inline($item) . '</li>';
                $html[] = $list . '</ul>';
                continue;
            }

            if(preg_match('/^\s*\d+[.)]\s+(.+)$/', $line, $match)){
                $items = [$match[1]];
                while($i + 1 < $count && preg_match('/^\s*\d+[.)]\s+(.+)$/', $lines[$i + 1], $next)){
                    $items[] = $next[1];
                    $i++;
                }
                $list = '<ol>';
                foreach($items as $item) $list .= '<li>' . $this->inline($item) . '</li>';
                $html[] = $list . '</ol>';
                continue;
            }

            $paragraph = [trim($line)];
            while($i + 1 < $count){
                $next = rtrim($lines[$i + 1]);
                if(trim($next) === '' || $this->startsBlock($next, $lines[$i + 2] ?? '')) break;
                $paragraph[] = trim($next);
                $i++;
            }
            $html[] = '<p>' . $this->inline(implode("\n", $paragraph), true) . '</p>';
        }

        return implode("\n", $html);
    }

    private function startsBlock(string $line, string $after = ''): bool
    {
        if(preg_match('/^\s*(?:```|#{1,6}\s|>|[-+*]\s+|\d+[.)]\s+|(?:-{3,}|\*{3,}|_{3,})\s*$)/', $line)) return true;
        return $after !== '' && $this->isTableDivider($after) && str_contains($line, '|');
    }

    private function inline(string $text, bool $lineBreaks = false): string
    {
        $text = e($text);
        $tokens = [];

        $store = static function(string $html) use (&$tokens): string {
            $key = '%%PMTOKEN' . count($tokens) . '%%';
            $tokens[$key] = $html;
            return $key;
        };

        $text = preg_replace_callback('/`([^`]+)`/', static function(array $match) use ($store): string {
            return $store('<code>' . $match[1] . '</code>');
        }, $text) ?? $text;

        $text = preg_replace_callback('/!\[([^\]]*)\]\(([^\s)]+)(?:\s+[&quot;\']([^&quot;\']*)[&quot;\'])?\)/', function(array $match) use ($store): string {
            $url = $this->safeUrl(html_entity_decode($match[2], ENT_QUOTES, 'UTF-8'));
            if($url === '') return $match[0];
            $title = !empty($match[3]) ? ' title="' . e(html_entity_decode($match[3], ENT_QUOTES, 'UTF-8')) . '"' : '';
            return $store('<img src="' . e($url) . '" alt="' . $match[1] . '"' . $title . ' loading="lazy">');
        }, $text) ?? $text;

        $text = preg_replace_callback('/\[([^\]]+)\]\(([^\s)]+)(?:\s+[&quot;\']([^&quot;\']*)[&quot;\'])?\)/', function(array $match) use ($store): string {
            $url = $this->safeUrl(html_entity_decode($match[2], ENT_QUOTES, 'UTF-8'));
            if($url === '') return $match[1];
            $title = !empty($match[3]) ? ' title="' . e(html_entity_decode($match[3], ENT_QUOTES, 'UTF-8')) . '"' : '';
            $external = preg_match('#^https?://#i', $url) ? ' target="_blank" rel="noopener noreferrer"' : '';
            return $store('<a href="' . e($url) . '"' . $title . $external . '>' . $match[1] . '</a>');
        }, $text) ?? $text;

        $text = preg_replace('/\*\*(.+?)\*\*|__(.+?)__/', '<strong>$1$2</strong>', $text) ?? $text;
        $text = preg_replace('/~~(.+?)~~/', '<del>$1</del>', $text) ?? $text;
        $text = preg_replace('/(?<!\*)\*([^*\n]+)\*(?!\*)|(?<!_)_([^_\n]+)_(?!_)/', '<em>$1$2</em>', $text) ?? $text;

        if($lineBreaks) $text = nl2br($text, false);
        return strtr($text, $tokens);
    }

    private function safeUrl(string $url): string
    {
        $url = trim($url);
        if($url === '' || preg_match('/^(?:javascript|vbscript|data):/i', $url)) return '';
        if(preg_match('#^(?:https?://|mailto:|tel:|\#|/)#i', $url)) return $url;
        if($this->pluginId === '') return $url;

        $relative = preg_replace('#^\./#', '', $url);
        $relative = str_replace('..', '', $relative);
        return ROOT . '/plugins/' . rawurlencode($this->pluginId) . '/' . ltrim($relative, '/');
    }

    private function isTableDivider(string $line): bool
    {
        $line = trim($line, " \t|");
        if($line === '') return false;
        foreach(explode('|', $line) as $cell){
            if(!preg_match('/^\s*:?-{3,}:?\s*$/', $cell)) return false;
        }
        return true;
    }

    private function tableCells(string $line): array
    {
        $line = trim(trim($line), '|');
        return array_map('trim', explode('|', $line));
    }

    private function tableAlignments(string $line): array
    {
        $alignments = [];
        foreach($this->tableCells($line) as $cell){
            $left = str_starts_with(trim($cell), ':');
            $right = str_ends_with(trim($cell), ':');
            $alignments[] = $left && $right ? 'center' : ($right ? 'right' : ($left ? 'left' : ''));
        }
        return $alignments;
    }
}
