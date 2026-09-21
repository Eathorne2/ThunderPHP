<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

$id = (int) (get_param('id') ?? 0);
$item = $id > 0 ? blog_get_post_by_id($id) : false;
$type = $item ? (string) ($item->post_type ?? 'post') : 'post';
if ($type === 'page') blog_require_permission('manage-blog-pages');
elseif (in_array($type, ['header', 'footer'], true)) blog_require_permission('manage-blog-designs');
else blog_require_permission('delete-blog-posts');

$redirectUrl = ltrim(str_replace(ROOT, '', blog_admin_list_url($type)), '/');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST)) {
    message('fail', 'The delete request was rejected.');
    redirect($redirectUrl);
    exit;
}

if ($item && blog_delete_post($id)) {
    message('success', 'The ' . strtolower(blog_content_type_label($type)) . ' was deleted.');
} else {
    message('fail', 'The content could not be deleted.');
}
redirect($redirectUrl);
exit;
