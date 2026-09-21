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

    <?php if ($tutorial === null): ?>
        <main class="tvi-tutorial-main">
            <section class="tvi-tutorial-not-found">
                <span>404</span>
                <h1>Tutorial not found</h1>
                <p>The requested tutorial is missing, unpublished or no longer listed in <code>tutorials/index.json</code>.</p>
                <a class="tvi-tutorial-button" href="<?= $escape($tutorials_url) ?>">Browse tutorials</a>
            </section>
        </main>
    <?php else: ?>
        <main class="tvi-tutorial-detail-layout">
            <article class="tvi-tutorial-lesson">
                <nav class="tvi-tutorial-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?= $escape($tutorials_url) ?>">Tutorials</a>
                    <span>›</span>
                    <span><?= $escape($category_names[$tutorial['category']] ?? $tutorial['category']) ?></span>
                </nav>

                <header class="tvi-tutorial-lesson__head">
                    <div class="tvi-tutorial-lesson__meta">
                        <span><?= $escape($tutorial['level']) ?></span>
                        <?php if (($tutorial['duration'] ?? '') !== ''): ?><span>◷ <?= $escape($tutorial['duration']) ?></span><?php endif; ?>
                        <span><?= $escape($category_names[$tutorial['category']] ?? $tutorial['category']) ?></span>
                    </div>
                    <h1><?= $escape($tutorial['title']) ?></h1>
                    <p><?= $escape($tutorial['summary']) ?></p>
                </header>

                <section class="tvi-tutorial-video">
                    <?php if (($tutorial['video_embed_url'] ?? '') !== ''): ?>
                        <iframe
                            src="<?= $escape($tutorial['video_embed_url']) ?>"
                            title="<?= $escape($tutorial['title']) ?> video"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    <?php elseif (($tutorial['video']['type'] ?? '') === 'file'): ?>
                        <video controls preload="metadata" src="<?= $escape($tutorials_url . '/' . $tutorial['slug'] . '/media') ?>"></video>
                    <?php else: ?>
                        <div class="tvi-tutorial-video__placeholder">
                            <span>▶</span>
                            <strong>Video coming soon</strong>
                            <p>The written lesson and companion files are available now.</p>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="tvi-tutorial-content">
                    <?= (string) ($tutorial['content_html'] ?? '') ?>
                </section>

                <?php if (($tutorial['files'] ?? []) !== []): ?>
                    <section class="tvi-tutorial-downloads">
                        <div class="tvi-tutorial-downloads__head">
                            <div><span class="tvi-tutorial-eyebrow">COMPANION FILES</span><h2>Files used in this tutorial</h2></div>
                            <a class="tvi-tutorial-button" href="<?= $escape($tutorials_url . '/' . $tutorial['slug'] . '/download') ?>">Download all ZIP</a>
                        </div>
                        <div class="tvi-tutorial-file-list">
                            <?php foreach ($tutorial['files'] as $file): ?>
                                <div><span>▤</span><div><strong><?= $escape($file['path']) ?></strong><small><?= $escape($file['size_label']) ?></small></div></div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </article>

            <aside class="tvi-tutorial-sidebar">
                <div class="tvi-tutorial-sidebar__card">
                    <strong>In this category</strong>
                    <?php foreach ($tutorials as $item): ?>
                        <?php if (($item['category'] ?? '') !== ($tutorial['category'] ?? '')) continue; ?>
                        <a class="<?= $item['slug'] === $tutorial['slug'] ? 'is-active' : '' ?>" href="<?= $escape($tutorials_url . '/' . $item['slug']) ?>">
                            <span><?= $escape($item['title']) ?></span>
                            <?php if (($item['duration'] ?? '') !== ''): ?><small><?= $escape($item['duration']) ?></small><?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="tvi-tutorial-sidebar__card">
                    <strong>Build while learning</strong>
                    <p>Keep the IDE open in another tab and reproduce each step in your own project.</p>
                    <a class="tvi-tutorial-button tvi-tutorial-button--block" href="<?= $escape($ide_url) ?>">Open Visual IDE</a>
                </div>
            </aside>
        </main>
    <?php endif; ?>
</div>
