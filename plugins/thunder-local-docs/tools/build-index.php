<?php

declare(strict_types=1);

$pluginRoot = dirname(__DIR__);

function lowerText(string $value): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}
$contentRoot = $pluginRoot . '/content';
$catalogPath = $contentRoot . '/catalog.php';
$indexPath = $pluginRoot . '/storage/search-index.php';

function readJson(string $path): array
{
    $data = json_decode((string)file_get_contents($path), true);
    if (!is_array($data)) throw new RuntimeException('Invalid JSON: ' . $path);
    return $data;
}

function parseFrontMatter(string $content): array
{
    $content = str_replace(["\r\n", "\r"], "\n", $content);
    if (!str_starts_with($content, "---\n")) return ['meta'=>[], 'body'=>$content];
    $end = strpos($content, "\n---\n", 4);
    if ($end === false) return ['meta'=>[], 'body'=>$content];
    $meta=[];
    foreach (explode("\n", substr($content, 4, $end-4)) as $line) {
        if (!str_contains($line, ':')) continue;
        [$key,$value]=explode(':',$line,2);
        $decoded=json_decode(trim($value),true);
        $meta[trim($key)]=json_last_error()===JSON_ERROR_NONE?$decoded:trim($value);
    }
    return ['meta'=>$meta,'body'=>ltrim(substr($content,$end+5))];
}

function plainText(string $markdown): string
{
    $text=preg_replace('/```[^\n]*\n(.*?)```/su',' $1 ',$markdown)??$markdown;
    $text=preg_replace('/`([^`]+)`/u','$1',$text)??$text;
    $text=preg_replace('/!?\[([^\]]*)\]\([^)]*\)/u','$1',$text)??$text;
    $text=strip_tags($text);
    $text=preg_replace('/[#>*_~|\-]+/u',' ',$text)??$text;
    return trim(preg_replace('/\s+/u',' ',$text)??$text);
}

function headings(string $markdown): array
{
    $result=[]; $inCode=false;
    foreach (explode("\n",$markdown) as $line) {
        if (str_starts_with(ltrim($line),'```')) {$inCode=!$inCode;continue;}
        if ($inCode) continue;
        if (preg_match('/^#{1,6}\s+(.+?)\s*#*$/',trim($line),$m)) $result[]=trim(preg_replace('/[`*_~]/','',$m[1]));
    }
    return array_slice($result,0,40);
}

function exportPhp(mixed $value, int $indent=0): string
{
    $pad=str_repeat('    ',$indent);
    if ($value===null) return 'null';
    if ($value===true) return 'true';
    if ($value===false) return 'false';
    if (is_int($value)||is_float($value)) return (string)$value;
    if (is_string($value)) return "'".str_replace(['\\',"'"],['\\\\',"\\'"],$value)."'";
    $lines=['['];
    foreach ($value as $key=>$item) {
        $prefix=is_array($value)&&array_is_list($value)?'':exportPhp((string)$key).' => ';
        $lines[]=str_repeat('    ',$indent+1).$prefix.exportPhp($item,$indent+1).',';
    }
    $lines[]=$pad.']';
    return implode("\n",$lines);
}

$topics=[]; $documents=[];
foreach (glob($contentRoot.'/*/topic.json') ?: [] as $topicFile) {
    $topic=readJson($topicFile); $topicDir=dirname($topicFile); $topic['path']=basename($topicDir); $topic['subtopics']=[];
    foreach (glob($topicDir.'/*/subtopic.json') ?: [] as $subFile) {
        $sub=readJson($subFile); $subDir=dirname($subFile); $sub['path']=$topic['slug'].'/'.basename($subDir);
        $sub['intro_path']=is_file($subDir.'/_intro.md')?$sub['path'].'/_intro.md':''; $sub['sections']=[];
        foreach (glob($subDir.'/*.md') ?: [] as $mdFile) {
            if (basename($mdFile)==='_intro.md') continue;
            $parsed=parseFrontMatter((string)file_get_contents($mdFile)); $meta=$parsed['meta'];
            if (!($meta['published']??true)) continue;
            $relative=$topic['slug'].'/'.$sub['slug'].'/'.basename($mdFile); $meta['path']=$relative; $sub['sections'][]=$meta;
            $plain=plainText($parsed['body']); $heads=headings($parsed['body']);
            $documents[]=[
                'id'=>$topic['slug'].'/'.$sub['slug'].'/'.$meta['slug'], 'title'=>$meta['title'], 'description'=>$meta['description']??'',
                'topic'=>$topic['title'], 'topic_slug'=>$topic['slug'], 'subtopic'=>$sub['title'], 'subtopic_slug'=>$sub['slug'],
                'section_slug'=>$meta['slug'], 'url'=>'docs/'.$topic['slug'].'/'.$sub['slug'].'/'.$meta['slug'], 'path'=>$relative,
                'headings'=>$heads, 'keywords'=>$meta['keywords']??[],
                'search_text'=>lowerText(implode(' ',[$meta['title'],$meta['description']??'',$topic['title'],$sub['title'],implode(' ',$heads),$plain])),
                'plain_text'=>$plain, 'order'=>(int)($meta['order']??0),
            ];
        }
        usort($sub['sections'],fn($a,$b)=>[$a['order']??0,$a['source_id']??0]<=>[$b['order']??0,$b['source_id']??0]);
        $topic['subtopics'][]=$sub;
    }
    usort($topic['subtopics'],fn($a,$b)=>[$a['order']??0,$a['id']??0]<=>[$b['order']??0,$b['id']??0]);
    $topics[]=$topic;
}
usort($topics,fn($a,$b)=>[$a['order']??0,$a['id']??0]<=>[$b['order']??0,$b['id']??0]);
$now=date(DATE_ATOM);
$catalog=['format_version'=>1,'generated_at'=>$now,'topics'=>$topics];
$index=['format_version'=>1,'generated_at'=>$now,'documents'=>$documents];
file_put_contents($catalogPath,"<?php\n\ndeclare(strict_types=1);\n\nreturn ".exportPhp($catalog).";\n");
file_put_contents($indexPath,"<?php\n\ndeclare(strict_types=1);\n\nreturn ".exportPhp($index).";\n");
echo 'Built '.count($topics).' topics and '.count($documents)." searchable sections.\n";
