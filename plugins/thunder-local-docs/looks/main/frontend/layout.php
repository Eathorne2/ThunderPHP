<?php

namespace ThunderLocalDocs;

$pageType = (string)($page['type'] ?? 'not-found');
$pageTitle = (string)($page['title'] ?? 'ThunderPHP Documentation');
$siteTitle = (string)($page['settings']['site_title'] ?? 'ThunderPHP Documentation');
$currentTopic = (string)($page['topic']['slug'] ?? '');
$currentSubtopic = (string)($page['subtopic']['slug'] ?? '');
$currentSection = (string)($page['section']['slug'] ?? '');
$query = (string)($page['search']['query'] ?? ($_GET['q'] ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= docs_escape($page['description'] ?? '') ?>">
    <title><?= docs_escape($pageTitle) ?> · <?= docs_escape($siteTitle) ?></title>
    <link rel="stylesheet" href="<?= docs_escape(current_look_http('assets/css/docs.css')) ?>">
</head>
<body class="docs-page docs-page--<?= docs_escape($pageType) ?>">
<header class="docs-header">
    <div class="docs-header__inner">
        <a class="docs-brand" href="<?= docs_escape(docs_url()) ?>" aria-label="Documentation home">
            <span class="docs-brand__mark">T</span>
            <span><strong>ThunderPHP</strong><small>Local Documentation</small></span>
        </a>
        <form class="docs-search" action="<?= docs_escape(docs_search_url()) ?>" method="get" role="search">
            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
            <input type="search" name="q" value="<?= docs_escape($query) ?>" placeholder="Search documentation…" autocomplete="off">
            <kbd>/</kbd>
        </form>
    </div>
</header>

<div class="docs-shell">
    <aside class="docs-sidebar" id="docs-sidebar">
        <div class="docs-sidebar__heading">Documentation</div>
        <nav aria-label="Documentation topics">
            <?php foreach (($page['topics'] ?? []) as $navTopic): ?>
                <?php $topicOpen = ($navTopic['slug'] ?? '') === $currentTopic; ?>
                <div class="docs-nav-group<?= $topicOpen ? ' is-open' : '' ?>">
                    <a class="docs-nav-topic<?= $topicOpen ? ' is-current' : '' ?>" href="<?= docs_escape(docs_url($navTopic['slug'])) ?>">
                        <span><?= docs_escape($navTopic['title']) ?></span>
                        <small><?= count(array_filter($navTopic['subtopics'] ?? [], static fn(array $s): bool => (bool)($s['published'] ?? false))) ?></small>
                    </a>
                    <?php if ($topicOpen): ?>
                        <div class="docs-nav-subtopics">
                            <?php foreach (($navTopic['subtopics'] ?? []) as $navSubtopic): ?>
                                <?php if (!($navSubtopic['published'] ?? false)) continue; ?>
                                <?php $subOpen = ($navSubtopic['slug'] ?? '') === $currentSubtopic; ?>
                                <a class="docs-nav-subtopic<?= $subOpen ? ' is-current' : '' ?>" href="<?= docs_escape(docs_url($navTopic['slug'], $navSubtopic['slug'])) ?>">
                                    <?= docs_escape($navSubtopic['title']) ?>
                                </a>
                                <?php if ($subOpen && $currentSection !== ''): ?>
                                    <div class="docs-nav-sections">
                                        <?php foreach (($navSubtopic['sections'] ?? []) as $navSection): ?>
                                            <?php if (!($navSection['published'] ?? false)) continue; ?>
                                            <a class="<?= ($navSection['slug'] ?? '') === $currentSection ? 'is-current' : '' ?>" href="<?= docs_escape(docs_url($navTopic['slug'], $navSubtopic['slug'], $navSection['slug'])) ?>">
                                                <?= docs_escape($navSection['title']) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </nav>
    </aside>

    <main class="docs-main">
        <button class="docs-mobile-nav" type="button" aria-controls="docs-sidebar" aria-expanded="false">Browse documentation</button>
        <?php
        $view = current_look('frontend/pages/' . $pageType . '.php');
        if (!is_file($view)) $view = current_look('frontend/pages/not-found.php');
        require $view;
        ?>
        <footer class="docs-footer">
            <span>Bundled with ThunderPHP · Database-free local documentation</span>
            <a href="<?= docs_escape(docs_url()) ?>">Documentation home</a>
        </footer>
    </main>
</div>
<script src="<?= docs_escape(current_look_http('assets/js/docs.js')) ?>" defer></script>
</body>
</html>
