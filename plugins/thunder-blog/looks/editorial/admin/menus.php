<?php

namespace ThunderBlog;

$success = function_exists('message') ? message('success', '', true) : null;
$fail = function_exists('message') ? message('fail', '', true) : null;
?>
<link rel="stylesheet" href="<?= esc(blog_look_http('assets/css/admin.css')) ?>">
<section class="tb-admin tb-menu-admin">
    <header class="tb-admin__header">
        <div>
            <span class="tb-admin__eyebrow">Navigation structure</span>
            <h1 class="tb-admin__title"><?= $menu ? 'Edit menu' : 'Menu designer' ?></h1>
            <p class="tb-admin__subtitle">Menus store content and hierarchy only. Header, footer and menu blocks provide the visual design.</p>
        </div>
        <?php if ($menu): ?><a class="tb-admin__button tb-admin__button--secondary" href="<?= esc(ROOT . '/admin/blog/menus') ?>"><i class="fa-solid fa-arrow-left"></i> All menus</a><?php endif; ?>
    </header>

    <?php if ($success): ?><div class="tb-admin__notice tb-admin__notice--success"><?= nl2br(esc($success)) ?></div><?php endif; ?>
    <?php if ($fail): ?><div class="tb-admin__notice tb-admin__notice--danger"><?= nl2br(esc($fail)) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="tb-admin__notice tb-admin__notice--danger"><?= esc($error) ?></div><?php endif; ?>

    <?php if (!$menu): ?>
        <div class="tb-menu-admin__layout">
            <form class="tb-admin__panel tb-menu-admin__create" method="post" action="<?= esc(ROOT . '/admin/blog/menus') ?>">
                <?= csrf() ?>
                <div class="tb-admin__panel-head"><div><h2 class="tb-admin__panel-title">Create a menu</h2><p class="tb-admin__panel-text">Examples: Main navigation, Footer links, Resources.</p></div></div>
                <label class="tb-admin__field"><span class="tb-admin__label">Menu name</span><input class="tb-admin__input" type="text" name="name" required maxlength="150" placeholder="Main navigation"></label>
                <button class="tb-admin__button" type="submit"><i class="fa-solid fa-plus"></i> Create menu</button>
            </form>

            <div class="tb-menu-admin__list">
                <?php if (!$menus): ?>
                    <div class="tb-admin__empty"><i class="fa-solid fa-bars"></i><h2>No menus yet</h2><p>Create a menu, add post, page or custom links, then select it from a compatible block.</p></div>
                <?php endif; ?>
                <?php foreach ($menus as $menuRow): ?>
                    <a class="tb-menu-admin__card" href="<?= esc(ROOT . '/admin/blog/menus/' . (int) $menuRow->id) ?>">
                        <span class="tb-menu-admin__card-icon"><i class="fa-solid fa-list-tree"></i></span>
                        <span class="tb-menu-admin__card-body"><strong><?= esc($menuRow->name) ?></strong><small><?= (int) $menuRow->item_count ?> item<?= (int) $menuRow->item_count === 1 ? '' : 's' ?> · <?= esc($menuRow->slug) ?></small></span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="tb-menu-editor" data-tb-menu-editor>
            <aside class="tb-menu-editor__sidebar">
                <form class="tb-admin__panel" method="post" action="<?= esc(ROOT . '/admin/blog/menus/' . (int) $menu->id) ?>">
                    <?= csrf() ?><input type="hidden" name="menu_action" value="save_name">
                    <h2 class="tb-admin__panel-title">Menu details</h2>
                    <label class="tb-admin__field"><span class="tb-admin__label">Menu name</span><input class="tb-admin__input" type="text" name="name" value="<?= esc($menu->name) ?>" required maxlength="150"></label>
                    <button class="tb-admin__button" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save name</button>
                </form>

                <form class="tb-admin__panel" method="post" action="<?= esc(ROOT . '/admin/blog/menus/' . (int) $menu->id) ?>" data-tb-menu-add-form>
                    <?= csrf() ?><input type="hidden" name="menu_action" value="add_item">
                    <h2 class="tb-admin__panel-title">Add menu item</h2>
                    <label class="tb-admin__field"><span class="tb-admin__label">Link source</span><select class="tb-admin__select" name="item_type" data-tb-menu-source><option value="post">Blog post</option><option value="page">Page</option><option value="custom">Custom link</option></select></label>
                    <label class="tb-admin__field" data-tb-menu-source-panel="post"><span class="tb-admin__label">Blog post</span><select class="tb-admin__select" name="post_id"><option value="">Select a post</option><?php foreach ($posts as $post): ?><option value="<?= (int) $post->id ?>"><?= esc($post->title) ?></option><?php endforeach; ?></select></label>
                    <label class="tb-admin__field" data-tb-menu-source-panel="page" hidden><span class="tb-admin__label">Page</span><select class="tb-admin__select" name="page_id"><option value="">Select a page</option><?php foreach ($pages as $page): ?><option value="<?= (int) $page->id ?>"><?= esc($page->title) ?></option><?php endforeach; ?></select></label>
                    <label class="tb-admin__field" data-tb-menu-source-panel="custom" hidden><span class="tb-admin__label">Custom URL <small>{{ROOT}} is supported</small></span><input class="tb-admin__input" type="text" name="custom_url" placeholder="{{ROOT}}/home, /contact or https://example.com"></label>
                    <label class="tb-admin__field"><span class="tb-admin__label">Link title <small>(optional for posts/pages)</small></span><input class="tb-admin__input" type="text" name="item_title" maxlength="255" placeholder="Use content title"></label>
                    <label class="tb-admin__field"><span class="tb-admin__label">Open link</span><select class="tb-admin__select" name="target"><option value="_self">Same window</option><option value="_blank">New window</option></select></label>
                    <button class="tb-admin__button" type="submit"><i class="fa-solid fa-plus"></i> Add item</button>
                </form>

                <form class="tb-admin__panel tb-menu-editor__danger" method="post" action="<?= esc(ROOT . '/admin/blog/menus/' . (int) $menu->id . '/delete') ?>" onsubmit="return confirm('Delete this menu and all its items?')">
                    <?= csrf() ?><h2 class="tb-admin__panel-title">Delete menu</h2><p class="tb-admin__panel-text">Blocks using this menu will render an empty menu until another menu is selected.</p><button class="tb-admin__button tb-admin__button--danger" type="submit"><i class="fa-solid fa-trash"></i> Delete menu</button>
                </form>
            </aside>

            <main class="tb-admin__panel tb-menu-editor__items">
                <div class="tb-admin__panel-head"><div><h2 class="tb-admin__panel-title">Items and hierarchy</h2><p class="tb-admin__panel-text">Choose a parent to nest an item. Lower order numbers appear first among siblings.</p></div><span class="tb-admin__badge"><?= count($menu_items) ?> items</span></div>
                <?php if (!$menu_items): ?><div class="tb-admin__empty tb-admin__empty--compact"><i class="fa-solid fa-arrow-left"></i><h3>Add the first link</h3><p>Use the form to add a post, page or custom destination.</p></div><?php else: ?>
                <form method="post" action="<?= esc(ROOT . '/admin/blog/menus/' . (int) $menu->id) ?>">
                    <?= csrf() ?>
                    <div class="tb-menu-items">
                        <?php foreach ($menu_items as $item): ?>
                            <article class="tb-menu-item">
                                <div class="tb-menu-item__drag"><i class="fa-solid fa-grip-vertical"></i></div>
                                <div class="tb-menu-item__main">
                                    <div class="tb-menu-item__heading"><strong><?= esc($item->title) ?></strong><span><?= esc(ucfirst($item->item_type)) ?> · <?= esc(blog_menu_item_url($item)) ?></span></div>
                                    <div class="tb-menu-item__grid">
                                        <label class="tb-admin__field"><span class="tb-admin__label">Title</span><input class="tb-admin__input" type="text" name="items[<?= (int) $item->id ?>][title]" value="<?= esc($item->title) ?>" required></label>
                                        <?php if ($item->item_type === 'custom'): ?><label class="tb-admin__field"><span class="tb-admin__label">URL <small>{{ROOT}} is supported</small></span><input class="tb-admin__input" type="text" name="items[<?= (int) $item->id ?>][url]" value="<?= esc($item->url) ?>" required></label><?php else: ?><label class="tb-admin__field"><span class="tb-admin__label">Resolved URL</span><input class="tb-admin__input" type="text" value="<?= esc(blog_menu_item_url($item)) ?>" readonly></label><?php endif; ?>
                                        <label class="tb-admin__field"><span class="tb-admin__label">Parent</span><select class="tb-admin__select" name="items[<?= (int) $item->id ?>][parent_id]"><option value="0">Top level</option><?php foreach ($menu_items as $parent): if ((int) $parent->id === (int) $item->id) continue; ?><option value="<?= (int) $parent->id ?>" <?= (int) $item->parent_id === (int) $parent->id ? 'selected' : '' ?>><?= esc($parent->title) ?></option><?php endforeach; ?></select></label>
                                        <label class="tb-admin__field"><span class="tb-admin__label">Order</span><input class="tb-admin__input" type="number" name="items[<?= (int) $item->id ?>][sort_order]" value="<?= (int) $item->sort_order ?>" min="0" step="1"></label>
                                        <label class="tb-admin__field"><span class="tb-admin__label">Target</span><select class="tb-admin__select" name="items[<?= (int) $item->id ?>][target]"><option value="_self" <?= $item->target !== '_blank' ? 'selected' : '' ?>>Same window</option><option value="_blank" <?= $item->target === '_blank' ? 'selected' : '' ?>>New window</option></select></label>
                                    </div>
                                </div>
                                <button class="tb-menu-item__delete" type="submit" name="delete_item_id" value="<?= (int) $item->id ?>" title="Delete item" onclick="return confirm('Delete this menu item?')"><i class="fa-solid fa-trash"></i></button>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <button class="tb-admin__button" type="submit" name="menu_action" value="save_items"><i class="fa-solid fa-floppy-disk"></i> Save items and hierarchy</button>
                </form>
                <?php endif; ?>
            </main>
        </div>
    <?php endif; ?>
</section>
<script>
(() => {
    const root = document.querySelector('[data-tb-menu-editor]');
    if (!root) return;
    const source = root.querySelector('[data-tb-menu-source]');
    const panels = [...root.querySelectorAll('[data-tb-menu-source-panel]')];
    const update = () => panels.forEach(panel => { panel.hidden = panel.dataset.tbMenuSourcePanel !== source.value; });
    source?.addEventListener('change', update); update();
})();
</script>
