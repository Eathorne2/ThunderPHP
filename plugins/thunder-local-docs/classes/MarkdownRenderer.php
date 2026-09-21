<?php

namespace ThunderLocalDocs;

final class MarkdownRenderer
{
    private array $toc = [];
    private array $usedIds = [];

    public function __construct(private readonly bool $copyCode = true)
    {
    }

    public function render(string $markdown): array
    {
        $this->toc = [];
        $this->usedIds = [];
        $markdown = str_replace(["\r\n", "\r"], "\n", trim($markdown));
        $lines = explode("\n", $markdown);
        $html = [];
        $count = count($lines);

        for ($i = 0; $i < $count;) {
            $line = $lines[$i];
            if (trim($line) === '') { $i++; continue; }

            if (preg_match('/^\s*```\s*([\w+-]*)\s*$/', $line, $match)) {
                $language = strtolower($match[1] ?? '');
                $code = [];
                $i++;
                while ($i < $count && !preg_match('/^\s*```\s*$/', $lines[$i])) {
                    $code[] = $lines[$i++];
                }
                if ($i < $count) $i++;
                $html[] = $this->codeBlock(implode("\n", $code), $language);
                continue;
            }

            if (preg_match('/^(#{1,6})\s+(.+?)\s*#*$/', trim($line), $match)) {
                $level = strlen($match[1]);
                $label = trim($match[2]);
                $id = $this->headingId($this->plainInline($label));
                if ($level <= 3) $this->toc[] = ['level' => $level, 'id' => $id, 'label' => $this->plainInline($label)];
                $html[] = "<h{$level} id=\"" . docs_escape($id) . "\">" . $this->inline($label) . "</h{$level}>";
                $i++;
                continue;
            }

            if (preg_match('/^\s*(?:---+|___+|\*\*\*+)\s*$/', $line)) {
                $html[] = '<hr>'; $i++; continue;
            }

            if ($i + 1 < $count && str_contains($line, '|') && preg_match('/^\s*\|?\s*:?-{3,}:?\s*(?:\|\s*:?-{3,}:?\s*)+\|?\s*$/', $lines[$i + 1])) {
                $headers = $this->tableCells($line);
                $i += 2; $rows=[];
                while ($i < $count && trim($lines[$i]) !== '' && str_contains($lines[$i], '|')) {
                    $rows[] = $this->tableCells($lines[$i]); $i++;
                }
                $table = '<div class="docs-table-wrap"><table><thead><tr>';
                foreach ($headers as $cell) $table .= '<th>' . $this->inline($cell) . '</th>';
                $table .= '</tr></thead><tbody>';
                foreach ($rows as $row) {
                    $table .= '<tr>';
                    foreach ($headers as $index => $_) $table .= '<td>' . $this->inline($row[$index] ?? '') . '</td>';
                    $table .= '</tr>';
                }
                $html[] = $table . '</tbody></table></div>';
                continue;
            }

            if (preg_match('/^\s*>\s?(.*)$/', $line)) {
                $quote=[];
                while ($i < $count && preg_match('/^\s*>\s?(.*)$/', $lines[$i], $match)) {
                    $quote[] = $match[1]; $i++;
                }
                $html[] = '<blockquote><p>' . $this->inline(implode(' ', $quote)) . '</p></blockquote>';
                continue;
            }

            if (preg_match('/^\s*[-+*]\s+(.+)$/', $line)) {
                $items=[];
                while ($i < $count && preg_match('/^\s*[-+*]\s+(.+)$/', $lines[$i], $match)) {
                    $items[] = $match[1]; $i++;
                }
                $list='<ul>';
                foreach ($items as $item) $list .= '<li>' . $this->inline($item) . '</li>';
                $html[]=$list.'</ul>';
                continue;
            }

            if (preg_match('/^\s*\d+[.)]\s+(.+)$/', $line)) {
                $items=[];
                while ($i < $count && preg_match('/^\s*\d+[.)]\s+(.+)$/', $lines[$i], $match)) {
                    $items[]=$match[1]; $i++;
                }
                $list='<ol>';
                foreach ($items as $item) $list .= '<li>' . $this->inline($item) . '</li>';
                $html[]=$list.'</ol>';
                continue;
            }

            $paragraph=[];
            while ($i < $count && trim($lines[$i]) !== '' && !$this->startsBlock($lines, $i)) {
                $paragraph[] = trim($lines[$i]); $i++;
            }
            if ($paragraph === []) {
                $paragraph[] = trim($lines[$i]); $i++;
            }
            $html[] = '<p>' . $this->inline(implode(' ', $paragraph)) . '</p>';
        }

        return ['html' => implode("\n", $html), 'toc' => $this->toc];
    }

    private function startsBlock(array $lines, int $index): bool
    {
        $line = $lines[$index] ?? '';
        if (preg_match('/^\s*```|^#{1,6}\s+|^\s*>|^\s*[-+*]\s+|^\s*\d+[.)]\s+|^\s*(?:---+|___+)\s*$/', $line)) return true;
        return isset($lines[$index + 1]) && str_contains($line, '|')
            && preg_match('/^\s*\|?\s*:?-{3,}:?\s*(?:\|\s*:?-{3,}:?\s*)+\|?\s*$/', $lines[$index + 1]);
    }

    private function inline(string $text): string
    {
        $codes=[];
        $text = preg_replace_callback('/`([^`]+)`/u', function(array $match) use (&$codes): string {
            $token='@@DOCSCODE' . count($codes) . '@@';
            $codes[$token] = '<code>' . docs_escape($match[1]) . '</code>';
            return $token;
        }, $text) ?? $text;

        $text = docs_escape($text);
        $text = preg_replace_callback('/!\[([^\]]*)\]\(([^)\s]+)(?:\s+&quot;([^&]*)&quot;)?\)/u', function(array $m): string {
            $src=$this->safeUrl(htmlspecialchars_decode($m[2], ENT_QUOTES));
            if ($src === '') return docs_escape($m[1]);
            return '<img src="' . docs_escape($src) . '" alt="' . docs_escape(htmlspecialchars_decode($m[1], ENT_QUOTES)) . '" loading="lazy">';
        }, $text) ?? $text;
        $text = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)(?:\s+&quot;([^&]*)&quot;)?\)/u', function(array $m): string {
            $href=$this->safeUrl(htmlspecialchars_decode($m[2], ENT_QUOTES));
            if ($href === '') return $m[1];
            $external=preg_match('/^https?:\/\//i',$href);
            return '<a href="' . docs_escape($href) . '"' . ($external ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' . $m[1] . '</a>';
        }, $text) ?? $text;
        $text = preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/__(.+?)__/u', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/~~(.+?)~~/u', '<del>$1</del>', $text) ?? $text;
        $text = preg_replace('/(?<!\*)\*([^*\n]+)\*(?!\*)/u', '<em>$1</em>', $text) ?? $text;

        foreach ($codes as $token => $code) {
            $text = str_replace($token, $code, $text);
        }
        return $text;
    }

    private function codeBlock(string $code, string $language): string
    {
        $language = preg_replace('/[^a-z0-9+-]/', '', strtolower($language)) ?: 'text';
        $label = strtoupper($language === 'text' ? 'Code' : $language);
        $copy = $this->copyCode ? '<button class="docs-copy-code" type="button" aria-label="Copy code">Copy</button>' : '';
        return '<div class="docs-code-block"><div class="docs-code-toolbar"><span>' . docs_escape($label) . '</span>' . $copy . '</div><pre><code class="language-' . docs_escape($language) . '">' . docs_escape($code) . '</code></pre></div>';
    }

    private function tableCells(string $line): array
    {
        $line=trim($line); $line=trim($line,'|');
        return array_map('trim', preg_split('/(?<!\\\\)\|/', $line) ?: []);
    }

    private function plainInline(string $text): string
    {
        $text=preg_replace('/[`*_~]/u','',$text) ?? $text;
        $text=preg_replace('/\[([^\]]+)\]\([^)]*\)/u','$1',$text) ?? $text;
        return trim(strip_tags($text));
    }

    private function headingId(string $heading): string
    {
        $id=docs_lower($heading);
        $id=preg_replace('/[^\p{L}\p{N}]+/u','-',$id) ?? $id;
        $id=trim($id,'-') ?: 'section';
        $base=$id; $suffix=2;
        while (isset($this->usedIds[$id])) $id=$base.'-'.$suffix++;
        $this->usedIds[$id]=true;
        return $id;
    }

    private function safeUrl(string $url): string
    {
        $url=trim($url);
        if ($url === '' || preg_match('/^(?:javascript|data|vbscript):/i',$url)) return '';
        if (preg_match('/^(?:https?:\/\/|mailto:|\/|#|\.\.?\/)/i',$url)) return $url;
        return $url;
    }
}
