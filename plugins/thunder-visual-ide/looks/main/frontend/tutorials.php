<?php

declare(strict_types=1);

namespace ThunderVisualIde;

$base_url = rtrim((string) ROOT, '/');
$ide_url = $base_url . '/thunder-ide';
$tutorials_url = $ide_url . '/tutorials';
$escape = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$category_names = [];
foreach ($tutorial_categories as $category) {
    $category_names[(string) $category['id']] = (string) $category['name'];
}
?>
<link rel="icon" type="image/jpeg" href="<?= current_look_http('assets/images/plugin.jpg') ?>">
<link rel="stylesheet" href="<?= current_look_http('assets/css/tutorials.css') ?>">
<div class="tvi-tutorial-app">
    <header class="tvi-tutorial-header">
        <a class="tvi-tutorial-brand" href="<?= $escape($ide_url) ?>">
            <span><img src="<?= current_look_http('assets/images/plugin.jpg') ?>" alt=""></span>
            <div><strong>Thunder Visual IDE</strong><small>Node-based tutorials</small></div>
        </a>
        <nav>
            <a href="<?= $escape($ide_url) ?>">Open IDE</a>
            <a class="is-active" href="<?= $escape($tutorials_url) ?>">Tutorials</a>
            <a href="https://thunderphp.com" target="_blank" rel="noopener noreferrer">ThunderPHP ↗</a>
        </nav>
    </header>

    <main class="tvi-tutorial-main">
        <section class="tvi-tutorial-hero">
            <div>
                <span class="tvi-tutorial-eyebrow">NODE-BASED LEARNING</span>
                <h1><?= $escape($tutorial_index['title'] ?? 'Thunder Visual IDE Tutorials') ?></h1>
                <p><?= $escape($tutorial_index['description'] ?? '') ?></p>
            </div>
            <div class="tvi-tutorial-hero__stats">
                <div><strong><?= count($tutorials) ?></strong><span>Tutorials</span></div>
                <div><strong><?= count($tutorial_categories) ?></strong><span>Categories</span></div>
            </div>
        </section>

        <section class="tvi-tutorial-controls" aria-label="Tutorial filters">
            <label class="tvi-tutorial-search">
                <span>⌕</span>
                <input id="tvi-tutorial-search" type="search" placeholder="Search titles, topics or tags">
            </label>
            <label class="tvi-tutorial-select">
                <span>Category</span>
                <select id="tvi-tutorial-category">
                    <option value="all">All categories</option>
                    <?php foreach ($tutorial_categories as $category): ?>
                        <option value="<?= $escape($category['id']) ?>"><?= $escape($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </section>

        <?php if ($tutorials === []): ?>
            <section class="tvi-tutorial-empty">
                <strong>No tutorials have been published yet.</strong>
            </section>
        <?php else: ?>
            <section class="tvi-tutorial-grid" id="tvi-tutorial-grid">
                <?php foreach ($tutorials as $tutorial):
                    $search_text = strtolower(implode(' ', [
                        (string) $tutorial['title'],
                        (string) $tutorial['summary'],
                        implode(' ', (array) $tutorial['tags']),
                        (string) ($category_names[$tutorial['category']] ?? $tutorial['category']),
                    ]));
                ?>
                    <article class="tvi-tutorial-card"
                        data-category="<?= $escape($tutorial['category']) ?>"
                        data-search="<?= $escape($search_text) ?>">
                        <a class="tvi-tutorial-card__image" href="<?= $escape($tutorials_url . '/' . $tutorial['slug']) ?>">
                            <?php if (($tutorial['thumbnail_data_uri'] ?? '') !== ''): ?>
                                <img src="<?= $escape($tutorial['thumbnail_data_uri']) ?>" alt="">
                            <?php else: ?>
                                <img src="<?= current_look_http('assets/images/plugin.jpg') ?>" alt="">
                            <?php endif; ?>
                            <span class="tvi-tutorial-card__level"><?= $escape($tutorial['level']) ?></span>
                        </a>
                        <div class="tvi-tutorial-card__body">
                            <div class="tvi-tutorial-card__category"><?= $escape($category_names[$tutorial['category']] ?? $tutorial['category']) ?></div>
                            <h2><a href="<?= $escape($tutorials_url . '/' . $tutorial['slug']) ?>"><?= $escape($tutorial['title']) ?></a></h2>
                            <p><?= $escape($tutorial['summary']) ?></p>
                            <div class="tvi-tutorial-card__meta">
                                <?php if (($tutorial['duration'] ?? '') !== ''): ?><span>◷ <?= $escape($tutorial['duration']) ?></span><?php endif; ?>
                                <?php if (($tutorial['files'] ?? []) !== []): ?><span>↓ <?= count($tutorial['files']) ?> files</span><?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
            <section class="tvi-tutorial-empty" id="tvi-tutorial-no-results" hidden>
                <strong>No tutorials match this search.</strong>
                <p>Try another keyword or category.</p>
            </section>
        <?php endif; ?>
 
    </main>
</div>
<script>
(() => {
    const search = document.getElementById('tvi-tutorial-search');
    const category = document.getElementById('tvi-tutorial-category');
    const cards = [...document.querySelectorAll('.tvi-tutorial-card')];
    const empty = document.getElementById('tvi-tutorial-no-results');
    const apply = () => {
        const query = String(search?.value || '').trim().toLowerCase();
        const selected = String(category?.value || 'all');
        let visible = 0;
        cards.forEach(card => {
            const categoryMatch = selected === 'all' || card.dataset.category === selected;
            const searchMatch = query === '' || String(card.dataset.search || '').includes(query);
            const show = categoryMatch && searchMatch;
            card.hidden = !show;
            if (show) visible++;
        });
        if (empty) empty.hidden = visible !== 0;
    };
    search?.addEventListener('input', apply);
    category?.addEventListener('change', apply);
})();
</script>
