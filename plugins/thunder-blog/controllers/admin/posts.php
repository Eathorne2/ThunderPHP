<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

$isPages = get_route_name() === 'blog.admin.pages';
$postType = $isPages ? 'page' : 'post';
if ($isPages) {
    blog_require_permission('manage-blog-pages');
} else {
    blog_require_permission('view-blog-admin');
}

$status = trim((string) ($_GET['status'] ?? 'all'));
$search = trim((string) ($_GET['q'] ?? ''));
$result = blog_get_posts([
    'page' => blog_page_number(),
    'per_page' => 20,
    'status' => $status,
    'search' => $search,
    'admin' => true,
    'post_type' => $postType,
]);

set_value('thunder_blog_admin', [
    'posts' => $result['items'],
    'pagination' => $result,
    'status_filter' => $status,
    'search' => $search,
    'post_type' => $postType,
    'content_label' => blog_content_type_label($postType),
    'content_label_plural' => blog_content_type_label($postType, true),
    'list_url' => blog_admin_list_url($postType),
    'create_url' => blog_admin_create_url($postType),
    'can_create' => $postType === 'page' ? blog_can('manage-blog-pages') : blog_can('create-blog-posts'),
    'can_edit' => $postType === 'page' ? blog_can('manage-blog-pages') : blog_can('edit-blog-posts'),
    'can_delete' => $postType === 'page' ? blog_can('manage-blog-pages') : blog_can('delete-blog-posts'),
]);
