<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

blog_require_permission('manage-blog-categories');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST)) {
        $error = 'The form token expired. Refresh the page and try again.';
    } else {
        try {
            if (($_POST['category_action'] ?? 'save') === 'delete') {
                blog_delete_category((int) ($_POST['category_id'] ?? 0));
                message('success', 'The category was deleted. Posts in it are now uncategorized.');
            } else {
                blog_save_category($_POST);
                message('success', 'The category was saved.');
            }
            redirect('admin/blog/categories');
            exit;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

set_value('thunder_blog_admin', [
    'categories' => blog_categories(),
    'error' => $error,
]);
