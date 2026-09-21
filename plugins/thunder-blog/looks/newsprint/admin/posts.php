<?php
namespace ThunderBlog;
$success = function_exists('message') ? message('success', '', true) : null;
$fail = function_exists('message') ? message('fail', '', true) : null;
$urlPrefix = $post_type === 'page' ? '/' : '/blog/';
?>
<link rel="stylesheet" href="<?= esc(blog_look_http('assets/css/admin.css')) ?>">
<div class="tb-admin">
    <div class="tb-admin__header">
        <div class="tb-admin__heading-group"><span class="tb-admin__eyebrow">Thunder Blog</span><h1 class="tb-admin__title"><?= esc($content_label_plural) ?></h1><p class="tb-admin__subtitle"><?= $post_type === 'page' ? 'Create standalone block-designed pages published directly at /slug.' : 'Create, publish and manage block-based articles.' ?></p></div>
        <?php if ($can_create): ?><a class="tb-admin__button tb-admin__button--primary" href="<?= esc($create_url) ?>"><i class="fa-solid fa-plus"></i> New <?= esc(strtolower($content_label)) ?></a><?php endif; ?>
    </div>
    <?php if ($success): ?><div class="tb-admin__notice tb-admin__notice--success"><?= esc($success) ?></div><?php endif; ?>
    <?php if ($fail): ?><div class="tb-admin__notice tb-admin__notice--danger"><?= esc($fail) ?></div><?php endif; ?>
    <form class="tb-admin__filters" method="get" action="<?= esc($list_url) ?>">
        <label class="tb-admin__search-wrap"><span class="tb-admin__sr-only">Search</span><i class="fa-solid fa-magnifying-glass tb-admin__search-icon"></i><input class="tb-admin__input tb-admin__input--search" type="search" name="q" value="<?= esc($search) ?>" placeholder="Search title, excerpt or slug"></label>
        <label class="tb-admin__filter-field"><span class="tb-admin__sr-only">Status</span><select class="tb-admin__select" name="status"><?php foreach (['all'=>'All statuses','draft'=>'Draft','published'=>'Published','scheduled'=>'Scheduled','archived'=>'Archived'] as $value=>$label): ?><option value="<?= esc($value) ?>" <?= $status_filter === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select></label>
        <button class="tb-admin__button tb-admin__button--secondary" type="submit">Filter</button>
    </form>
    <div class="tb-admin__panel">
        <?php if (!$posts): ?><div class="tb-admin__empty"><i class="fa-regular fa-file-lines tb-admin__empty-icon"></i><h2 class="tb-admin__empty-title">No <?= esc(strtolower($content_label_plural)) ?> found</h2><p class="tb-admin__empty-text">Create the first <?= esc(strtolower($content_label)) ?> or change the filters.</p></div>
        <?php else: ?><div class="tb-admin__table-wrap"><table class="tb-admin__table"><thead class="tb-admin__table-head"><tr class="tb-admin__table-row"><th class="tb-admin__table-heading"><?= esc($content_label) ?></th><th class="tb-admin__table-heading">Status</th><?php if ($post_type === 'post'): ?><th class="tb-admin__table-heading">Category</th><?php endif; ?><th class="tb-admin__table-heading">Author</th><th class="tb-admin__table-heading">Published</th><th class="tb-admin__table-heading tb-admin__table-heading--actions">Actions</th></tr></thead><tbody class="tb-admin__table-body">
        <?php foreach ($posts as $item): $author = blog_author($item->author_id); ?><tr class="tb-admin__table-row"><td class="tb-admin__table-cell"><div class="tb-admin__post-cell"><?php if ($item->featured_image): ?><img class="tb-admin__post-thumb" src="<?= esc($item->featured_image) ?>" alt=""><?php else: ?><span class="tb-admin__post-thumb tb-admin__post-thumb--empty"><i class="fa-regular fa-image"></i></span><?php endif; ?><div class="tb-admin__post-info"><strong class="tb-admin__post-title"><?= esc($item->title) ?></strong><span class="tb-admin__post-slug"><?= esc($urlPrefix . $item->slug) ?></span></div></div></td><td class="tb-admin__table-cell"><span class="tb-admin__badge tb-admin__badge--<?= esc($item->status) ?>"><?= esc(ucfirst($item->status)) ?></span></td><?php if ($post_type === 'post'): ?><td class="tb-admin__table-cell"><?= esc($item->category_name ?: 'Uncategorized') ?></td><?php endif; ?><td class="tb-admin__table-cell"><?= esc($author['display_name']) ?></td><td class="tb-admin__table-cell"><?= esc($item->published_at ? get_date($item->published_at) : '—') ?></td><td class="tb-admin__table-cell tb-admin__table-cell--actions"><div class="tb-admin__actions"><a class="tb-admin__icon-button" href="<?= esc(blog_post_url($item)) ?>" target="_blank" title="View"><i class="fa-solid fa-arrow-up-right-from-square"></i></a><?php if ($can_edit): ?><a class="tb-admin__icon-button" href="<?= esc(blog_admin_edit_url($item)) ?>" title="Edit"><i class="fa-solid fa-pen"></i></a><?php endif; ?><?php if ($can_delete): ?><form class="tb-admin__inline-form" method="post" action="<?= esc(ROOT . '/admin/blog/' . (int)$item->id . '/delete') ?>" data-tb-confirm="Delete this <?= esc(strtolower($content_label)) ?> permanently?"><?= csrf() ?><button class="tb-admin__icon-button tb-admin__icon-button--danger" type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button></form><?php endif; ?></div></td></tr><?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
    </div>
    <?php if (($pagination['pages'] ?? 1) > 1): ?><nav class="tb-admin__pagination"><?php for ($number=1;$number<=$pagination['pages'];$number++): $query=$_GET;$query['p']=$number; ?><a class="tb-admin__page-link <?= $number===$pagination['page']?'tb-admin__page-link--active':'' ?>" href="?<?= esc(http_build_query($query)) ?>"><?= $number ?></a><?php endfor; ?></nav><?php endif; ?>
</div>
<script src="<?= esc(blog_look_http('assets/js/admin.js')) ?>"></script>
