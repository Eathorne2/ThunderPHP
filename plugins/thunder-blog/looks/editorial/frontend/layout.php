<?php

namespace ThunderBlog;

$beforeTitle = do_filter('html_before_title', '');
$afterTitle = do_filter('html_after_title', '');
$description = (string) ($data['meta_description'] ?? APP_DESCRIPTION ?? '');
$paletteStyle = blog_palette_variables((array) $palette);
?>
<!doctype html>
<html class="tb-page-root" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($beforeTitle . $title . $afterTitle) ?></title>
    <?php if ($description !== ''): ?><meta name="description" content="<?= esc($description) ?>"><?php endif; ?>
    <link rel="stylesheet" href="<?= esc(blog_look_http('assets/css/frontend.css')) ?>">
    <?php do_action('html_head', ['plugin' => 'thunder-blog', 'view' => $view]); ?>
</head>
<body class="tb-page-body" style="<?= esc($paletteStyle) ?>">
<div class="thunder-blog">
    <?php $activeHeader = blog_render_active_design('header'); ?>
    <?php if ($activeHeader !== ''): ?>
        <?= $activeHeader ?>
    <?php else: ?>
        <header class="thunder-blog__site-header">
            <div class="thunder-blog__site-header-inner">
                <a class="thunder-blog__brand" href="<?= esc(ROOT . '/') ?>">
                    <?php if (defined('APP_LOGO') && APP_LOGO): ?><img class="thunder-blog__brand-logo" src="<?= esc(get_image(APP_LOGO)) ?>" alt="<?= esc(APP_NAME) ?>"><?php endif; ?>
                    <span class="thunder-blog__brand-name"><?= esc(APP_NAME) ?></span>
                </a>
                <div class="thunder-blog__header-actions"><nav class="thunder-blog__nav" aria-label="Main navigation"><?php do_action('html_main_menu', ['plugin' => 'thunder-blog']); ?><a class="thunder-blog__nav-link" href="<?= esc(ROOT . '/blog') ?>">Blog</a></nav><form class="thunder-blog__header-search" method="get" action="<?= esc(ROOT . '/blog/search') ?>"><label class="thunder-blog__search-label thunder-blog__search-label--header"><span class="thunder-blog__sr-only">Search blog posts</span><input class="thunder-blog__search-input" type="search" name="q" placeholder="Search"><button class="thunder-blog__search-button thunder-blog__search-button--icon" type="submit" aria-label="Search"><span aria-hidden="true">⌕</span></button></label></form></div>
            </div>
        </header>
    <?php endif; ?>

    <main class="thunder-blog__main">
        <?= $content ?>
    </main>

    <?php $activeFooter = blog_render_active_design('footer'); ?>
    <?php if ($activeFooter !== ''): ?>
        <?= $activeFooter ?>
    <?php else: ?>
        <footer class="thunder-blog__footer"><div class="thunder-blog__footer-inner"><span class="thunder-blog__footer-text">&copy; <?= date('Y') ?> <?= esc(APP_NAME) ?></span><div class="thunder-blog__footer-menu"><?php do_action('html_footer_menu', ['plugin' => 'thunder-blog']); ?></div></div></footer>
    <?php endif; ?>
</div>
<?php do_action('html_footer', ['plugin' => 'thunder-blog', 'view' => $view]); ?>
</body>
</html>
