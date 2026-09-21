<?php namespace ThunderLocalDocs; $search=$page['search'] ?? ['query'=>'','results'=>[],'total'=>0,'terms'=>[]]; ?>
<nav class="docs-breadcrumbs" aria-label="Breadcrumb"><a href="<?= docs_escape(docs_url()) ?>">Docs</a><span>/</span><span>Search</span></nav>
<header class="docs-page-header docs-search-header">
    <span class="docs-type-badge">Search</span>
    <h1><?= $search['query'] !== '' ? 'Results for “' . docs_escape($search['query']) . '”' : 'Search documentation' ?></h1>
    <?php if ($search['query'] !== ''): ?><p><?= (int)$search['total'] ?> matching section<?= (int)$search['total'] === 1 ? '' : 's' ?></p><?php else: ?><p>Search titles, headings, descriptions, examples, and full section content.</p><?php endif; ?>
</header>
<?php if (($page['search_error'] ?? '') !== ''): ?><div class="docs-notice"><?= docs_escape($page['search_error']) ?></div><?php endif; ?>
<?php if ($search['query'] !== '' && empty($search['results']) && ($page['search_error'] ?? '') === ''): ?>
    <div class="docs-empty"><h2>No matching documentation</h2><p>Try fewer words or a framework term such as <code>hooks</code>, <code>request</code>, or <code>query builder</code>.</p></div>
<?php endif; ?>
<div class="docs-search-results">
    <?php foreach (($search['results'] ?? []) as $result): ?>
        <a href="<?= docs_escape(base_url($result['url'])) ?>">
            <span class="docs-search-result__path"><?= docs_escape($result['topic']) ?> / <?= docs_escape($result['subtopic']) ?></span>
            <h2><?= docs_highlight($result['title'], $search['terms']) ?></h2>
            <p><?= docs_highlight($result['excerpt'], $search['terms']) ?></p>
        </a>
    <?php endforeach; ?>
</div>
