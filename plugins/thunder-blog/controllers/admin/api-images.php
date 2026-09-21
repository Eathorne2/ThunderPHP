<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

blog_require_any_permission(['create-blog-posts', 'edit-blog-posts']);
$page = max(1, (int) ($_GET['p'] ?? 1));
$category = trim((string) ($_GET['category'] ?? ''));
$search = trim((string) ($_GET['q'] ?? ''));

blog_json_response([
    'ok' => true,
    'data' => blog_images_page($page, 24, $category, $search),
]);
