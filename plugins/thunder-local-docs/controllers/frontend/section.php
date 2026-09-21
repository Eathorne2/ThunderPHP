<?php

namespace ThunderLocalDocs;

$topicSlug = isset($topicSlug) ? (string)$topicSlug : (string)get_param('topic');
$subtopicSlug = isset($subtopicSlug) ? (string)$subtopicSlug : (string)get_param('subtopic');
$sectionSlug = isset($sectionSlug) ? (string)$sectionSlug : (string)get_param('section');
$repository = docs_repository();
$section = $repository->section($topicSlug, $subtopicSlug, $sectionSlug);

if ($section === null) {
    docs_not_found();
    return;
}

$settings = docs_settings();
$renderer = new MarkdownRenderer((bool)($settings['enable_copy_code'] ?? true));
$rendered = $renderer->render($section['markdown']);
$indexDocument = $repository->documentById($topicSlug . '/' . $subtopicSlug . '/' . $sectionSlug) ?? [];
$related = [];

if ($settings['enable_related_docs'] ?? true) {
    $related = (new SearchEngine($repository->documents()))->related($indexDocument, 4);
}

set_value('thunder_local_docs_page', [
    'type' => 'section',
    'title' => $section['title'],
    'description' => $section['description'] ?? '',
    'settings' => $settings,
    'topics' => $repository->topics(),
    'topic' => $section['topic'],
    'subtopic' => $section['subtopic'],
    'section' => $section,
    'content_html' => $rendered['html'],
    'toc' => $rendered['toc'],
    'adjacent' => $repository->adjacent($section),
    'related' => $related,
    'reading_time' => docs_reading_time($section['plain_text']),
]);
