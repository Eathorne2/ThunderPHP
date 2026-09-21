<?php

namespace ThunderLocalDocs;

defined('ROOTPATH') or die('Direct script access denied');

require_once __DIR__ . '/classes/FrontMatter.php';
require_once __DIR__ . '/classes/MarkdownRenderer.php';
require_once __DIR__ . '/classes/DocumentRepository.php';
require_once __DIR__ . '/classes/SearchEngine.php';
require_once __DIR__ . '/functions.php';

set_value([
    'thunder_local_docs' => [
        'route' => 'docs',
        'content_path' => __DIR__ . '/content',
        'index_path' => __DIR__ . '/storage/search-index.php',
    ],
]);

/*
 * Use one dispatcher and one renderer for the whole /docs branch.
 *
 * ThunderPHP can consider both a static route such as /docs/search and a
 * dynamic route such as /docs/{topic} active for the same request. Registering
 * one callback per named route therefore caused the search and topic callbacks
 * to run together. The topic callback interpreted "search" as a topic slug,
 * replaced the search result with a documentation 404, and rendered the full
 * layout a second time.
 */
add_action('controller', function ($data = []): void {
    if (page() !== 'docs') {
        return;
    }

    require plugin_path('controllers/frontend/router.php');
}, 10);

add_action('view', function ($data = []): void {
    if (page() !== 'docs') {
        return;
    }

    $page = get_value('thunder_local_docs_page');
    $page = is_array($page) ? $page : [];
    require current_look('frontend/layout.php');
}, 10);
