<?php namespace ThunderLocalDocs; ?>
<nav class="docs-breadcrumbs" aria-label="Breadcrumb"><a href="<?= docs_escape(docs_url()) ?>">Docs</a><span>/</span><span><?= docs_escape($page['topic']['title']) ?></span></nav>
<header class="docs-page-header">
    <span class="docs-type-badge"><?= docs_escape(ucfirst($page['topic']['type'] ?? 'reference')) ?></span>
    <h1><?= docs_escape($page['topic']['title']) ?></h1>
    <p><?= docs_escape($page['topic']['description'] ?? '') ?></p>
</header>
<div class="docs-subtopic-list">
    <?php foreach (($page['topic']['subtopics'] ?? []) as $subtopic): ?>
        <?php if (!($subtopic['published'] ?? false)) continue; ?>
        <a class="docs-subtopic-card" href="<?= docs_escape(docs_url($page['topic']['slug'], $subtopic['slug'])) ?>">
            <div><span><?= docs_escape(ucfirst($subtopic['type'] ?? 'reference')) ?></span><h2><?= docs_escape($subtopic['title']) ?></h2><p><?= docs_escape($subtopic['description'] ?? '') ?></p></div>
            <strong><?= count(array_filter($subtopic['sections'] ?? [], static fn(array $s): bool => (bool)($s['published'] ?? false))) ?> sections</strong>
        </a>
    <?php endforeach; ?>
</div>
