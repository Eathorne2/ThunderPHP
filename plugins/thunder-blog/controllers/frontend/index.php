<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

$page = blog_page_number();
$categorySlug = is_route('blog.category') ? (string) get_param('slug') : '';
$tagSlug = is_route('blog.tag') ? (string) get_param('slug') : '';
$result = blog_get_posts([
    'page' => $page,
    'category_slug' => $categorySlug,
    'tag_slug' => $tagSlug,
    'post_type' => 'post',
]);

$heroEyebrow = (string) blog_setting('blog_page_eyebrow', 'Stories, ideas and updates');
$pageTitle = (string) blog_setting('blog_page_title', 'The Blog');
$archiveLabel = '';
$heroTitle = $pageTitle;
$heroSubtitle = (string) blog_setting('blog_page_subtitle', defined('APP_DESCRIPTION') ? APP_DESCRIPTION : 'Explore our latest articles and updates.');
if ($categorySlug !== '') {
    $archiveLabel = 'Category: ' . ucwords(str_replace('-', ' ', $categorySlug));
    $pageTitle = $archiveLabel . ' · Blog';
    $heroTitle = $archiveLabel;
} elseif ($tagSlug !== '') {
    $archiveLabel = 'Tag: ' . ucwords(str_replace('-', ' ', $tagSlug));
    $pageTitle = $archiveLabel . ' · Blog';
    $heroTitle = $archiveLabel;
}

do_action('thunder_blog_before_index', ['result' => $result, 'category_slug' => $categorySlug, 'tag_slug' => $tagSlug]);
set_value('thunder_blog_page', [
    'page_title' => $pageTitle,
    'archive_label' => $archiveLabel,
    'hero_eyebrow' => $heroEyebrow,
    'hero_title' => $heroTitle,
    'hero_subtitle' => $heroSubtitle,
    'search_query' => '',
    'is_search' => false,
    'posts' => $result['items'],
    'pagination' => $result,
    'categories' => blog_categories(),
    'palette' => blog_global_palette(),
]);
