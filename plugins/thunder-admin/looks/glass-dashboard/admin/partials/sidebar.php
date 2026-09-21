<?php

namespace ThunderAdmin;
?>
<aside class="ta-sidebar" data-ta-sidebar>
    <div class="ta-sidebar__brand-row">
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

        <button class="ta-sidebar__desktop-toggle" type="button" data-ta-sidebar-collapse aria-label="Collapse sidebar" aria-expanded="true">
            <i class="fa-solid fa-angles-left" aria-hidden="true"></i>
        </button>

        <button class="ta-sidebar__mobile-close" type="button" data-ta-sidebar-close aria-label="Close sidebar">
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

    <div class="ta-sidebar__footer">
        <span class="ta-sidebar__version">Glass Dashboard · Thunder Admin 1.5.0</span>
    </div>
</aside>
