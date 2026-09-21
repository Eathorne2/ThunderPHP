<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

$slug = (string) get_param('slug');
$post = blog_get_post_by_slug($slug, false, 'post');
if (!$post) {
    redirect('404');
    exit;
}

blog_db()->query('UPDATE ' . blog_tables()['posts'] . ' SET view_count = view_count + 1 WHERE id = :id', ['id' => (int) $post->id]);
$author = blog_author($post->author_id);
$tags = blog_tags_for_post((int) $post->id);
do_action('thunder_blog_before_post', ['post' => $post, 'author' => $author, 'tags' => $tags]);
set_value('thunder_blog_page', [
    'page_title' => trim((string) ($post->seo_title ?: $post->title)),
    'meta_description' => trim((string) ($post->seo_description ?: blog_excerpt($post, 155))),
    'post' => $post,
    'author' => $author,
    'tags' => $tags,
    'palette' => blog_post_palette($post),
]);
