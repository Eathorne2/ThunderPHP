<?php

namespace ThunderAdmin;

$searchEnabled = (bool) ($admin_search['enabled'] ?? false);
?>
<header class="ta-topbar">
    <div class="ta-topbar__left">
        <span class="ta-control-label"><i class="fa-solid fa-terminal" aria-hidden="true"></i><span>Control Panel</span></span>
        <button class="ta-topbar__menu-button" type="button" data-ta-sidebar-open aria-label="Open sidebar">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        <?php if ($searchEnabled): ?>
            <form class="ta-search" action="<?= e($admin_search['action'] ?? admin_url()) ?>" method="<?= e($admin_search['method'] ?? 'get') ?>" role="search">
                <i class="ta-search__icon fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input
                    class="ta-search__input"
                    type="search"
                    name="<?= e($admin_search['name'] ?? 'admin_search') ?>"
                    value="<?= e($admin_search['value'] ?? '') ?>"
                    placeholder="<?= e($admin_search['placeholder'] ?? 'Search admin…') ?>"
                    autocomplete="off"
                    data-ta-search-input
                >
                <span class="ta-search__shortcut">Ctrl K</span>
            </form>
        <?php endif; ?>
    </div>

    <div class="ta-topbar__right">
        <div class="ta-topbar__actions">
            <?php do_action('admin_topbar_actions'); ?>
        </div>

        <?php require $ta_view_paths['notifications']; ?>
        <?php require $ta_view_paths['user_info']; ?>
    </div>
</header>
