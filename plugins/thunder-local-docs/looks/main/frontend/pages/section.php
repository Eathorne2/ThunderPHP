<?php namespace ThunderLocalDocs; ?>
<nav class="docs-breadcrumbs" aria-label="Breadcrumb"><a href="<?= docs_escape(docs_url()) ?>">Docs</a><span>/</span><a href="<?= docs_escape(docs_url($page['topic']['slug'])) ?>"><?= docs_escape($page['topic']['title']) ?></a><span>/</span><a href="<?= docs_escape(docs_url($page['topic']['slug'], $page['subtopic']['slug'])) ?>"><?= docs_escape($page['subtopic']['title']) ?></a></nav>
<div class="docs-article-layout">
    <article class="docs-article">
        <header class="docs-article-header">
            <span class="docs-type-badge"><?= docs_escape(ucfirst($page['subtopic']['type'] ?? 'reference')) ?></span>
            <h1><?= docs_escape($page['section']['title']) ?></h1>
            <?php if (($page['section']['description'] ?? '') !== ''): ?><p><?= docs_escape($page['section']['description']) ?></p><?php endif; ?>
            <?php if ($page['settings']['enable_reading_time'] ?? true): ?><div class="docs-article-meta"><?= (int)$page['reading_time'] ?> min read</div><?php endif; ?>
        </header>
        <div class="docs-prose"><?= $page['content_html'] ?></div>
        <nav class="docs-adjacent" aria-label="Previous and next documentation">
            <?php if (!empty($page['adjacent']['previous'])): $previous=$page['adjacent']['previous']; ?>
                <a href="<?= docs_escape(docs_url($page['topic']['slug'], $page['subtopic']['slug'], $previous['slug'])) ?>"><small>Previous</small><strong>← <?= docs_escape($previous['title']) ?></strong></a>
            <?php else: ?><span></span><?php endif; ?>
            <?php if (!empty($page['adjacent']['next'])): $next=$page['adjacent']['next']; ?>
                <a class="is-next" href="<?= docs_escape(docs_url($page['topic']['slug'], $page['subtopic']['slug'], $next['slug'])) ?>"><small>Next</small><strong><?= docs_escape($next['title']) ?> →</strong></a>
            <?php endif; ?>
        </nav>
        <?php if (!empty($page['related'])): ?>
            <section class="docs-related"><h2>Related documentation</h2><div>
                <?php foreach ($page['related'] as $related): ?>
                    <a href="<?= docs_escape(base_url($related['url'])) ?>"><span><?= docs_escape($related['subtopic']) ?></span><strong><?= docs_escape($related['title']) ?></strong></a>
                <?php endforeach; ?>
            </div></section>
        <?php endif; ?>
    </article>
    <?php if (!empty($page['toc'])): ?>
        <aside class="docs-toc"><div><strong>On this page</strong><?php foreach ($page['toc'] as $item): ?><a class="level-<?= (int)$item['level'] ?>" href="#<?= docs_escape($item['id']) ?>"><?= docs_escape($item['label']) ?></a><?php endforeach; ?></div></aside>
    <?php endif; ?>
</div>
