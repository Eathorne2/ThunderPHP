<?php

namespace ThunderLocalDocs;

$slug = isset($topicSlug) ? (string)$topicSlug : (string)get_param('topic');
$repository = docs_repository();
$topic = $repository->topic($slug);

if ($topic === null) {
    docs_not_found();
    return;
}

set_value('thunder_local_docs_page', [
    'type' => 'topic',
    'title' => $topic['title'],
    'description' => $topic['description'] ?? '',
    'settings' => docs_settings(),
    'topics' => $repository->topics(),
    'topic' => $topic,
]);
