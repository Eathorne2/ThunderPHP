<?php namespace ThunderLocalDocs; ?>
<nav class="docs-breadcrumbs" aria-label="Breadcrumb"><a href="<?= docs_escape(docs_url()) ?>">Docs</a><span>/</span><a href="<?= docs_escape(docs_url($page['topic']['slug'])) ?>"><?= docs_escape($page['topic']['title']) ?></a><span>/</span><span><?= docs_escape($page['subtopic']['title']) ?></span></nav>
<header class="docs-page-header">
    <span class="docs-type-badge"><?= docs_escape(ucfirst($page['subtopic']['type'] ?? 'reference')) ?></span>
    <h1><?= docs_escape($page['subtopic']['title']) ?></h1>
    <p><?= docs_escape($page['subtopic']['description'] ?? '') ?></p>
</header>
<?php if (($page['intro_html'] ?? '') !== ''): ?><div class="docs-intro docs-prose"><?= $page['intro_html'] ?></div><?php endif; ?>
<div class="docs-section-list">
    <?php $number = 0; foreach (($page['subtopic']['sections'] ?? []) as $section): ?>
        <?php if (!($section['published'] ?? false)) continue; $number++; ?>
        <a href="<?= docs_escape(docs_url($page['topic']['slug'], $page['subtopic']['slug'], $section['slug'])) ?>">
            <span><?= str_pad((string)$number, 2, '0', STR_PAD_LEFT) ?></span>
            <div><h2><?= docs_escape($section['title']) ?></h2><p><?= docs_escape($section['description'] ?? '') ?></p></div>
            <b>→</b>
        </a>
    <?php endforeach; ?>
</div>
