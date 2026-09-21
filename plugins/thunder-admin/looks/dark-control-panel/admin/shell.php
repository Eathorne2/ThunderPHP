<?php

namespace ThunderAdmin;

$vars = get_value();
extract(is_array($vars) ? $vars : [], EXTR_SKIP);

$admin_page_title = $admin_page_title ?? 'Administration';
$admin_page_description = $admin_page_description ?? '';
$admin_menu = $admin_menu ?? [];
$admin_breadcrumbs = $admin_breadcrumbs ?? [];
$admin_user = $admin_user ?? current_user_info();
$admin_notifications = $admin_notifications ?? [];
$admin_search = $admin_search ?? [];
$admin_body_classes = $admin_body_classes ?? ['ta-page'];
$ta_view_paths = $ta_view_paths ?? [];
$ta_assets = $ta_assets ?? [];
$admin_palette = $admin_palette ?? resolved_admin_palette(active_admin_look());
$admin_palette_style = $admin_palette_style ?? palette_css_variables($admin_palette['colors'] ?? []);
$admin_page_background = (string) (($admin_palette['colors']['page_bg'] ?? '#f4f7fb'));
$admin_text_color = (string) (($admin_palette['colors']['text'] ?? '#172033'));

ob_start();
do_action('admin_main_content');
$admin_content = trim((string) ob_get_clean());
?>
<!doctype html>
<html class="ta-html" lang="en" style="background:<?= e($admin_page_background) ?>">
<head class="ta-head">
    <meta class="ta-meta" charset="utf-8">
    <meta class="ta-meta" name="viewport" content="width=device-width, initial-scale=1">
    <title class="ta-title"><?= e($admin_page_title) ?> · <?= e(app_name()) ?></title>
    <link class="ta-stylesheet ta-stylesheet--icons" rel="stylesheet" href="<?= e(root_url() . '/assets/css/all.min.css') ?>">
    <link class="ta-stylesheet" rel="stylesheet" href="<?= e($ta_assets['css'] ?? '') ?>">
    <?php do_action('html_admin_head'); ?>
</head>
<body class="<?= e(implode(' ', $admin_body_classes)) ?>" style="background:<?= e($admin_page_background) ?>;color:<?= e($admin_text_color) ?>">
<div class="ta-shell ta-shell--control-panel" data-ta-shell style="<?= e($admin_palette_style) ?>">
    <?php require $ta_view_paths['sidebar']; ?>

    <div class="ta-mobile-overlay" data-ta-mobile-overlay hidden></div>

    <div class="ta-main">
        <?php require $ta_view_paths['topbar']; ?>

        <main class="ta-content">
            <?php require $ta_view_paths['breadcrumbs']; ?>
            <?php require $ta_view_paths['flash']; ?>

            <div class="ta-control-strip"><span class="ta-control-strip__status"><i class="fa-solid fa-circle" aria-hidden="true"></i> SYSTEM ONLINE</span><span class="ta-control-strip__path">CONTROL / <?= e(strtoupper($admin_page_title)) ?></span></div>

            <header class="ta-page-header">
                <div class="ta-page-header__text">
                    <h1 class="ta-page-header__title"><?= e($admin_page_title) ?></h1>
                    <?php if ($admin_page_description !== ''): ?>
                        <p class="ta-page-header__description"><?= e($admin_page_description) ?></p>
                    <?php endif; ?>
                </div>

                <div class="ta-page-header__actions">
                    <?php do_action('admin_page_actions'); ?>
                </div>
            </header>

            <div class="ta-page-content">
                <?php if ($admin_content !== ''): ?>
                    <?= $admin_content ?>
                <?php else: ?>
                    <section class="ta-empty-state">
                        <div class="ta-empty-state__icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
                        <h2 class="ta-empty-state__title">No content was registered for this admin page.</h2>
                        <p class="ta-empty-state__text">Add an <code class="ta-code">admin_main_content</code> action for the current route.</p>
                    </section>
                <?php endif; ?>
            </div>
        </main>

        <?php require $ta_view_paths['footer']; ?>
    </div>
</div>

<script class="ta-script" src="<?= e($ta_assets['js'] ?? '') ?>"></script>
<?php do_action('html_admin_footer'); ?>
</body>
</html>
