<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

blog_require_permission('manage-blog-designs');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST)) {
        $error = 'The form token expired. Refresh the page and try again.';
    } else {
        try {
            $type = strtolower(trim((string) ($_POST['design_type'] ?? '')));
            $id = max(0, (int) ($_POST['design_id'] ?? 0));
            blog_activate_design($type, $id);
            message('success', ucfirst($type) . ' selection updated.');
            redirect('admin/blog/designs');
            exit;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$headers = blog_get_posts(['admin' => true, 'post_type' => 'header', 'per_page' => 100, 'page' => 1, 'status' => 'all']);
$footers = blog_get_posts(['admin' => true, 'post_type' => 'footer', 'per_page' => 100, 'page' => 1, 'status' => 'all']);
set_value('thunder_blog_admin', [
    'headers' => $headers['items'],
    'footers' => $footers['items'],
    'active_header_id' => (int) blog_setting('active_header_id', 0),
    'active_footer_id' => (int) blog_setting('active_footer_id', 0),
    'error' => $error,
]);
