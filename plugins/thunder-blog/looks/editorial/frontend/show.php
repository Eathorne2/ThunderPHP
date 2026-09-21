<?php

namespace ThunderBlog;
?>
<article class="thunder-blog__article">
    <header class="thunder-blog__article-header">
        <div class="thunder-blog__article-header-inner">
            <?php if (!empty($post->category_name)): ?>
                <a class="thunder-blog__article-category" href="<?= esc(ROOT . '/blog/category/' . rawurlencode($post->category_slug)) ?>"><?= esc($post->category_name) ?></a>
            <?php endif; ?>
            <h1 class="thunder-blog__article-title"><?= esc($post->title) ?></h1>
            <?php if (!empty($post->excerpt)): ?><p class="thunder-blog__article-lead"><?= esc($post->excerpt) ?></p><?php endif; ?>
            <div class="thunder-blog__article-meta">
                <?php if (blog_setting('show_author', '1') === '1'): ?>
                    <?php if (!empty($author['avatar'])): ?><img class="thunder-blog__author-avatar" src="<?= esc($author['avatar']) ?>" alt="<?= esc($author['display_name']) ?>"><?php endif; ?>
                    <?php if (!empty($author['profile_url'])): ?>
                        <a class="thunder-blog__author-link" href="<?= esc($author['profile_url']) ?>"><?= esc($author['display_name']) ?></a>
                    <?php else: ?>
                        <span class="thunder-blog__author-link"><?= esc($author['display_name']) ?></span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (blog_setting('show_date', '1') === '1'): ?><span class="thunder-blog__article-date"><?= esc(get_date($post->published_at ?: $post->created_at)) ?></span><?php endif; ?>
                <?php if (blog_setting('show_views', '1') === '1'): ?><span class="thunder-blog__views"><?= (int) $post->view_count + 1 ?> views</span><?php endif; ?>
            </div>
        </div>
    </header>

    <?php if (!empty($post->featured_image)): ?>
        <div class="thunder-blog__featured-wrap"><img class="thunder-blog__featured-image" src="<?= esc($post->featured_image) ?>" alt="<?= esc($post->title) ?>"></div>
    <?php endif; ?>

    <div class="thunder-blog__article-content">
        <?= blog_render_compiled_content($post, ['author' => $author, 'tags' => $tags]) ?>
    </div>

    <?php if ($tags): ?>
        <footer class="thunder-blog__tag-footer">
            <span class="thunder-blog__tag-label">Tags</span>
            <div class="thunder-blog__tag-list">
                <?php foreach ($tags as $tag): ?><a class="thunder-blog__tag" href="<?= esc(ROOT . '/blog/tag/' . rawurlencode($tag->slug)) ?>"><?= esc($tag->name) ?></a><?php endforeach; ?>
            </div>
        </footer>
    <?php endif; ?>
</article>
<?php do_action('thunder_blog_after_post', ['post' => $post, 'author' => $author, 'tags' => $tags]); ?>
