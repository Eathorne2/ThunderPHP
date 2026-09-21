<?php

namespace ThunderLocalDocs;

$repository = docs_repository();
$settings = docs_settings();
$topics = $repository->topics();

set_value('thunder_local_docs_page', [
    'type' => 'home',
    'title' => $settings['site_title'] ?? 'ThunderPHP Documentation',
    'description' => $settings['site_intro'] ?? '',
    'settings' => $settings,
    'topics' => $topics,
    'stats' => $repository->stats(),
]);
