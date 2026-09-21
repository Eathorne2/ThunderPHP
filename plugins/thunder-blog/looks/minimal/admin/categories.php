<?php

namespace ThunderBlog;

$success = function_exists('message') ? message('success', '', true) : null;
$fail = function_exists('message') ? message('fail', '', true) : null;
?>
<link rel="stylesheet" href="<?= esc(blog_look_http('assets/css/admin.css')) ?>">
<div class="tb-admin">
    <div class="tb-admin__header">
        <div class="tb-admin__heading-group">
            <span class="tb-admin__eyebrow">Content organization</span>
            <h1 class="tb-admin__title">Categories</h1>
            <p class="tb-admin__subtitle">Group related posts and create browsable category archives.</p>
        </div>
        <a class="tb-admin__button tb-admin__button--secondary" href="<?= esc(ROOT . '/admin/blog') ?>">Back to posts</a>
    </div>
    <?php if ($success): ?><div class="tb-admin__notice tb-admin__notice--success"><?= esc($success) ?></div><?php endif; ?>
    <?php if ($fail): ?><div class="tb-admin__notice tb-admin__notice--danger"><?= esc($fail) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="tb-admin__notice tb-admin__notice--danger"><?= esc($error) ?></div><?php endif; ?>

    <div class="tb-admin__split">
        <section class="tb-admin__panel tb-admin__panel--padded">
            <div class="tb-admin__section-heading">
                <h2 class="tb-admin__section-title" id="tb-category-form-title">Add category</h2>
                <button class="tb-admin__text-button" type="button" id="tb-category-reset">Reset</button>
            </div>
            <form class="tb-admin__form" method="post" action="<?= esc(ROOT . '/admin/blog/categories') ?>" id="tb-category-form">
                <?= csrf() ?>
                <input type="hidden" name="category_action" value="save">
                <input type="hidden" name="category_id" id="tb-category-id" value="0">
                <label class="tb-admin__field"><span class="tb-admin__label">Name</span><input class="tb-admin__input" type="text" name="name" id="tb-category-name" required></label>
                <label class="tb-admin__field"><span class="tb-admin__label">Slug</span><input class="tb-admin__input" type="text" name="slug" id="tb-category-slug" placeholder="generated-from-name"></label>
                <label class="tb-admin__field"><span class="tb-admin__label">Description</span><textarea class="tb-admin__textarea" name="description" id="tb-category-description" rows="5"></textarea></label>
                <button class="tb-admin__button tb-admin__button--primary" type="submit">Save category</button>
            </form>
        </section>

        <section class="tb-admin__panel">
            <div class="tb-admin__table-wrap">
                <table class="tb-admin__table">
                    <thead class="tb-admin__table-head"><tr class="tb-admin__table-row"><th class="tb-admin__table-heading">Category</th><th class="tb-admin__table-heading">Posts</th><th class="tb-admin__table-heading tb-admin__table-heading--actions">Actions</th></tr></thead>
                    <tbody class="tb-admin__table-body">
                    <?php foreach ($categories as $category): ?>
                        <tr class="tb-admin__table-row">
                            <td class="tb-admin__table-cell"><strong class="tb-admin__post-title"><?= esc($category->name) ?></strong><span class="tb-admin__post-slug"><?= esc($category->slug) ?></span></td>
                            <td class="tb-admin__table-cell"><?= (int) $category->post_count ?></td>
                            <td class="tb-admin__table-cell tb-admin__table-cell--actions">
                                <div class="tb-admin__actions">
                                    <button class="tb-admin__icon-button tb-category-edit" type="button" title="Edit" data-id="<?= (int) $category->id ?>" data-name="<?= esc($category->name) ?>" data-slug="<?= esc($category->slug) ?>" data-description="<?= esc($category->description) ?>"><i class="fa-solid fa-pen"></i></button>
                                    <form class="tb-admin__inline-form" method="post" action="<?= esc(ROOT . '/admin/blog/categories') ?>" data-tb-confirm="Delete this category? Posts will become uncategorized.">
                                        <?= csrf() ?><input type="hidden" name="category_action" value="delete"><input type="hidden" name="category_id" value="<?= (int) $category->id ?>">
                                        <button class="tb-admin__icon-button tb-admin__icon-button--danger" type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<script src="<?= esc(blog_look_http('assets/js/admin.js')) ?>"></script>
