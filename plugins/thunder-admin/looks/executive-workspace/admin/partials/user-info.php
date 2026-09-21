<?php

namespace ThunderAdmin;
?>
<div class="ta-dropdown" data-ta-dropdown>
    <button class="ta-user-button" type="button" data-ta-dropdown-toggle aria-label="Open user menu" aria-expanded="false">
        <?php if (($admin_user['image'] ?? '') !== ''): ?>
            <img class="ta-user-button__avatar" src="<?= e($admin_user['image']) ?>" alt="">
        <?php else: ?>
            <span class="ta-user-button__initials"><?= e($admin_user['initials'] ?? 'U') ?></span>
        <?php endif; ?>

        <span class="ta-user-button__details">
            <strong class="ta-user-button__name"><?= e($admin_user['name'] ?? 'Administrator') ?></strong>
            <span class="ta-user-button__role"><?= e($admin_user['role'] ?? 'Admin') ?></span>
        </span>

        <i class="ta-user-button__chevron fa-solid fa-chevron-down" aria-hidden="true"></i>
    </button>

    <div class="ta-dropdown__panel ta-user-menu" data-ta-dropdown-panel hidden>
        <div class="ta-user-menu__header">
            <strong class="ta-user-menu__name"><?= e($admin_user['name'] ?? 'Administrator') ?></strong>
            <?php if (($admin_user['email'] ?? '') !== ''): ?>
                <span class="ta-user-menu__email"><?= e($admin_user['email']) ?></span>
            <?php endif; ?>
        </div>

        <div class="ta-user-menu__links">
            <?php foreach (($admin_user['links'] ?? []) as $link): ?>
                <a class="ta-user-menu__link" href="<?= e($link['link'] ?? '#') ?>">
                    <?php if (($link['icon'] ?? '') !== ''): ?>
                        <i class="ta-user-menu__link-icon <?= e($link['icon']) ?>" aria-hidden="true"></i>
                    <?php endif; ?>
                    <span class="ta-user-menu__link-title"><?= e($link['title'] ?? '') ?></span>
                </a>
            <?php endforeach; ?>
            <?php do_action('admin_user_menu_links'); ?>
            <a class="ta-user-menu__link ta-user-menu__link--logout" href="<?= e(root_url() . '/logout') ?>">
                <i class="ta-user-menu__link-icon fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                <span class="ta-user-menu__link-title">Logout</span>
            </a>
        </div>
    </div>
</div>
