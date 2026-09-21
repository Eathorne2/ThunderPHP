<?php namespace ThunderLocalDocs; ?>
<section class="docs-hero">
    <span class="docs-eyebrow">Bundled framework reference</span>
    <h1><?= docs_escape($page['title']) ?></h1>
    <p><?= docs_escape($page['description'] ?? '') ?></p>
    <form class="docs-hero-search" action="<?= docs_escape(docs_search_url()) ?>" method="get">
        <input type="search" name="q" placeholder="Try “hooks”, “validation”, or “migrations”">
        <button type="submit">Search docs</button>
    </form>
    <div class="docs-stats">
        <span><strong><?= (int)($page['stats']['topics'] ?? 0) ?></strong> topics</span>
        <span><strong><?= (int)($page['stats']['subtopics'] ?? 0) ?></strong> guides and references</span>
        <span><strong><?= (int)($page['stats']['sections'] ?? 0) ?></strong> sections</span>
    </div>
</section>

<section class="docs-section-block">
    <div class="docs-section-heading"><div><span class="docs-eyebrow">Explore</span><h2>Documentation topics</h2></div></div>
    <div class="docs-topic-grid">
        <?php foreach (($page['topics'] ?? []) as $topic): ?>
            <a class="docs-topic-card" href="<?= docs_escape(docs_url($topic['slug'])) ?>">
                <span class="docs-topic-card__type"><?= docs_escape(ucfirst($topic['type'] ?? 'reference')) ?></span>
                <h3><?= docs_escape($topic['title']) ?></h3>
                <p><?= docs_escape($topic['description'] ?? '') ?></p>
                <span class="docs-topic-card__meta"><?= count(array_filter($topic['subtopics'] ?? [], static fn(array $s): bool => (bool)($s['published'] ?? false))) ?> sections groups <b>→</b></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
