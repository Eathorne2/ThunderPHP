<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

blog_require_any_permission(['create-blog-posts', 'edit-blog-posts', 'manage-blog-pages', 'manage-blog-designs']);
$page = max(1, (int) ($_GET['p'] ?? 1));
$category = trim((string) ($_GET['category'] ?? ''));
$search = trim((string) ($_GET['q'] ?? ''));
$context = strtolower(trim((string) ($_GET['context'] ?? 'post')));
if (!in_array($context, ['post', 'page', 'header', 'footer'], true)) $context = 'post';
$result = blog_blocks_page($page, 12, $category, $search, $context);
blog_json_response(['ok' => true, 'data' => $result]);
