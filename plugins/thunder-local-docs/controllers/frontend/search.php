<?php

namespace ThunderLocalDocs;

$settings = docs_settings();
$query = trim((string)($_GET['q'] ?? ''));
$minimum = max(1, (int)($settings['search_min_length'] ?? 2));
$result = ['query' => $query, 'terms' => [], 'results' => [], 'total' => 0];
$error = '';

if ($query !== '' && docs_length($query) < $minimum) {
    $error = 'Enter at least ' . $minimum . ' characters to search.';
} elseif ($query !== '') {
    $result = (new SearchEngine(docs_repository()->documents()))->search(
        $query,
        (int)($settings['search_results_per_page'] ?? 40)
    );
}

set_value('thunder_local_docs_page', [
    'type' => 'search',
    'title' => $query === '' ? 'Search documentation' : 'Search: ' . $query,
    'description' => 'Search the bundled ThunderPHP documentation.',
    'settings' => $settings,
    'topics' => docs_repository()->topics(),
    'search' => $result,
    'search_error' => $error,
]);
