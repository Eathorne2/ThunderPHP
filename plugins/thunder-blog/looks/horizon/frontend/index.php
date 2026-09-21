<?php

namespace ThunderBlog;
?>
<section class="thunder-blog__hero">
    <div class="thunder-blog__hero-inner">
        <span class="thunder-blog__eyebrow"><?= esc($hero_eyebrow ?? 'Stories, ideas and updates') ?></span>
        <h1 class="thunder-blog__hero-title"><?= esc($hero_title ?? ($archive_label ?: 'The Blog')) ?></h1>
        <p class="thunder-blog__hero-text"><?= esc($hero_subtitle ?? (defined('APP_DESCRIPTION') ? APP_DESCRIPTION : 'Explore our latest articles and updates.')) ?></p>
        <form class="thunder-blog__search-form" method="get" action="<?= esc(ROOT . '/blog/search') ?>">
            <label class="thunder-blog__search-label"><span class="thunder-blog__sr-only">Search blog posts</span><input class="thunder-blog__search-input" type="search" name="q" value="<?= esc($search_query ?? '') ?>" placeholder="Search articles, topics or titles"><button class="thunder-blog__search-button" type="submit"><span aria-hidden="true">⌕</span><span>Search</span></button></label>
        </form>
    </div>
</section>

<section class="thunder-blog__listing">
    <div class="thunder-blog__listing-main">
        <?php if (!empty($is_search) && !empty($archive_label)): ?><div class="thunder-blog__results-label"><?= esc($archive_label) ?></div><?php endif; ?>
        <?php if (!$posts): ?>
            <div class="thunder-blog__empty">
                <h2 class="thunder-blog__empty-title"><?= !empty($is_search) ? 'No matching posts' : 'No posts found' ?></h2>
                <p class="thunder-blog__empty-text"><?= !empty($is_search) ? 'Try a different search phrase or browse the latest articles.' : 'There are no published posts in this section yet.' ?></p>
            </div>
        <?php else: ?>
            <div class="thunder-blog__grid">
                <?php foreach ($posts as $post): $author = blog_author($post->author_id); ?>
                    <article class="thunder-blog__card">
                        <a class="thunder-blog__card-media" href="<?= esc(blog_post_url($post)) ?>">
                            <?php if (!empty($post->featured_image)): ?>
                                <img class="thunder-blog__card-image" src="<?= esc($post->featured_image) ?>" alt="<?= esc($post->title) ?>">
                            <?php else: ?>
                                <span class="thunder-blog__card-placeholder">Thunder Blog</span>
                            <?php endif; ?>
                        </a>
                        <div class="thunder-blog__card-body">
                            <div class="thunder-blog__card-meta">
                                <?php if (blog_setting('show_category', '1') === '1' && !empty($post->category_name)): ?>
                                    <a class="thunder-blog__category" href="<?= esc(ROOT . '/blog/category/' . rawurlencode($post->category_slug)) ?>"><?= esc($post->category_name) ?></a>
                                <?php endif; ?>
                                <?php if (blog_setting('show_date', '1') === '1'): ?>
                                    <span class="thunder-blog__date"><?= esc(get_date($post->published_at ?: $post->created_at)) ?></span>
                                <?php endif; ?>
                                <?php if (blog_setting('show_views', '1') === '1'): ?>
                                    <span class="thunder-blog__views"><?= (int) $post->view_count ?> views</span>
                                <?php endif; ?>
                            </div>
                            <h2 class="thunder-blog__card-title"><a class="thunder-blog__card-title-link" href="<?= esc(blog_post_url($post)) ?>"><?= esc($post->title) ?></a></h2>
                            <p class="thunder-blog__card-excerpt"><?= esc(blog_excerpt($post)) ?></p>
                            <div class="thunder-blog__card-footer">
                                <?php if (blog_setting('show_author', '1') === '1'): ?>
                                    <span class="thunder-blog__author-name">By <?= esc($author['display_name']) ?></span>
                                <?php endif; ?>
                                <a class="thunder-blog__read-more" href="<?= esc(blog_post_url($post)) ?>">Read article <span class="thunder-blog__read-more-arrow">→</span></a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (($pagination['pages'] ?? 1) > 1): ?>
                <nav class="thunder-blog__pagination" aria-label="Blog pagination">
                    <?php for ($number = 1; $number <= $pagination['pages']; $number++): ?>
                        <?php $query = $_GET; $query['p'] = $number; ?>
                        <a class="thunder-blog__page-link <?= $number === $pagination['page'] ? 'thunder-blog__page-link--active' : '' ?>" href="?<?= esc(http_build_query($query)) ?>"><?= $number ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <aside class="thunder-blog__sidebar">
        <div class="thunder-blog__sidebar-card">
            <h2 class="thunder-blog__sidebar-title">Search</h2>
            <form class="thunder-blog__sidebar-search" method="get" action="<?= esc(ROOT . '/blog/search') ?>"><label class="thunder-blog__search-label thunder-blog__search-label--compact"><span class="thunder-blog__sr-only">Search blog posts</span><input class="thunder-blog__search-input" type="search" name="q" value="<?= esc($search_query ?? '') ?>" placeholder="Search the blog"><button class="thunder-blog__search-button thunder-blog__search-button--icon" type="submit" aria-label="Search"><span aria-hidden="true">⌕</span></button></label></form>
        </div>
        <div class="thunder-blog__sidebar-card">
            <h2 class="thunder-blog__sidebar-title">Categories</h2>
            <div class="thunder-blog__category-list">
                <?php foreach ($categories as $category): ?>
                    <a class="thunder-blog__category-row" href="<?= esc(ROOT . '/blog/category/' . rawurlencode($category->slug)) ?>">
                        <span class="thunder-blog__category-row-name"><?= esc($category->name) ?></span>
                        <span class="thunder-blog__category-count"><?= (int) $category->post_count ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>
</section>
<?php do_action('thunder_blog_after_index', ['posts' => $posts, 'pagination' => $pagination]); ?>
