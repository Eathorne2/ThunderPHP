<?php

namespace ThunderAdmin;
?>
<header class="ta-horizontal-header">
    <div class="ta-horizontal-header__brandbar">
        <a class="ta-brand" href="<?= e(admin_url()) ?>">
            <?php if (app_logo() !== ''): ?>
                <span class="ta-brand__mark ta-brand__mark--image">
                    <img class="ta-brand__logo" src="<?= e(app_logo()) ?>" alt="<?= e(app_name()) ?>">
                </span>
            <?php else: ?>
                <span class="ta-brand__mark"><i class="fa-solid fa-bolt" aria-hidden="true"></i></span>
            <?php endif; ?>

            <span class="ta-brand__copy">
                <span class="ta-brand__text"><?= e(app_name()) ?></span>
                <?php if (app_description() !== ''): ?>
                    <span class="ta-brand__description"><?= e(app_description()) ?></span>
                <?php endif; ?>
            </span>
        </a>

        <div class="ta-horizontal-header__actions">
            <span class="ta-horizontal-header__version">Thunder Admin 1.5.0</span>
            <button class="ta-horizontal-header__menu-button" type="button" data-ta-sidebar-open aria-label="Open navigation">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</header>

<div class="ta-horizontal-nav-wrap" data-ta-sidebar data-ta-horizontal-nav>
        <div class="ta-horizontal-nav-wrap__header">
            <strong class="ta-horizontal-nav-wrap__title">Navigation</strong>
            <button class="ta-horizontal-nav-wrap__close" type="button" data-ta-sidebar-close aria-label="Close navigation">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>

        <nav class="ta-nav" aria-label="Admin navigation">
            <?php foreach ($admin_menu as $pluginId => $group): ?>
                <div class="ta-nav__group" data-ta-plugin-group="<?= e($pluginId) ?>">
                    <?php render_menu_items($group['roots'], $group['children']); ?>
                </div>
            <?php endforeach; ?>
        </nav>
</div>
