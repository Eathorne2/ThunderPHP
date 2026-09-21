<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

$query = trim((string) ($_GET['q'] ?? ''));
if (blog_text_length($query) > 120) {
    $query = blog_text_substr($query, 0, 120);
}
$page = blog_page_number();
$result = blog_get_posts([
    'page' => $page,
    'search' => $query,
    'post_type' => 'post',
]);

$heroEyebrow = (string) blog_setting('search_page_eyebrow', 'Stories, ideas and updates');
$heroTitle = (string) blog_setting('search_page_title', 'Search the Blog');
$heroSubtitle = (string) blog_setting('search_page_subtitle', 'Find articles, topics and stories from the blog.');
$archiveLabel = $query !== '' ? 'Search results for “' . $query . '”' : '';
$pageTitle = $query !== '' ? $heroTitle . ': ' . $query : $heroTitle;

do_action('thunder_blog_before_search', ['query' => $query, 'result' => $result]);
set_value('thunder_blog_page', [
    'page_title' => $pageTitle,
    'archive_label' => $archiveLabel,
    'hero_eyebrow' => $heroEyebrow,
    'hero_title' => $heroTitle,
    'hero_subtitle' => $heroSubtitle,
    'search_query' => $query,
    'posts' => $result['items'],
    'pagination' => $result,
    'categories' => blog_categories(),
    'palette' => blog_global_palette(),
    'is_search' => true,
]);
