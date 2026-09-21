<?php

namespace ThunderLocalDocs;

$topicSlug = isset($topicSlug) ? (string)$topicSlug : (string)get_param('topic');
$subtopicSlug = isset($subtopicSlug) ? (string)$subtopicSlug : (string)get_param('subtopic');
$repository = docs_repository();
$subtopic = $repository->subtopic($topicSlug, $subtopicSlug);

if ($subtopic === null) {
    docs_not_found();
    return;
}

$renderer = new MarkdownRenderer((bool)(docs_settings()['enable_copy_code'] ?? true));
$intro = $repository->intro($subtopic);
$renderedIntro = $intro !== '' ? $renderer->render($intro) : ['html' => '', 'toc' => []];

set_value('thunder_local_docs_page', [
    'type' => 'subtopic',
    'title' => $subtopic['title'],
    'description' => $subtopic['description'] ?? '',
    'settings' => docs_settings(),
    'topics' => $repository->topics(),
    'topic' => $subtopic['topic'],
    'subtopic' => $subtopic,
    'intro_html' => $renderedIntro['html'],
]);
