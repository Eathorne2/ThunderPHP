<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

$route = get_route_name();
$id = (int) (get_param('id') ?? 0);
$isEdit = $id > 0;
$post = $isEdit ? blog_get_post_by_id($id) : false;

if ($isEdit && !$post) {
    message('fail', 'The requested content was not found.');
    redirect('admin/blog');
    exit;
}

$postType = 'post';
if ($post) {
    $postType = (string) ($post->post_type ?? 'post');
} elseif (str_contains($route, '.page.')) {
    $postType = 'page';
} elseif (str_contains($route, '.design.')) {
    $postType = strtolower((string) get_param('type'));
}
if (!in_array($postType, ['post', 'page', 'header', 'footer'], true)) {
    $postType = 'post';
}

if ($postType === 'page') {
    blog_require_permission('manage-blog-pages');
} elseif (in_array($postType, ['header', 'footer'], true)) {
    blog_require_permission('manage-blog-designs');
} else {
    blog_require_permission($isEdit ? 'edit-blog-posts' : 'create-blog-posts');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST)) {
        $error = 'The form token expired. Refresh the page and try again.';
    } else {
        try {
            $_POST['post_type'] = $postType;
            $savedId = blog_save_post($_POST, $isEdit ? $id : null);
            $saved = blog_get_post_by_id($savedId);
            $label = strtolower(blog_content_type_label($postType));
            message('success', $isEdit ? "The {$label} was updated." : "The {$label} was created.");
            $editUrl = blog_admin_edit_url($saved ?: $savedId);
            redirect(ltrim(str_replace(ROOT, '', $editUrl), '/'));
            exit;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

if (!$post) {
    $post = (object) [
        'id' => 0,
        'post_type' => $postType,
        'title' => (string) ($_POST['title'] ?? ''),
        'slug' => (string) ($_POST['slug'] ?? ''),
        'excerpt' => (string) ($_POST['excerpt'] ?? ''),
        'featured_image' => (string) ($_POST['featured_image'] ?? ''),
        'status' => (string) ($_POST['status'] ?? (in_array($postType, ['header', 'footer'], true) ? 'published' : 'draft')),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'blocks_json' => (string) ($_POST['blocks_json'] ?? '[]'),
        'palette_json' => (string) ($_POST['palette_json'] ?? json_encode(blog_global_palette())),
        'category_id' => (int) ($_POST['category_id'] ?? 0),
        'seo_title' => (string) ($_POST['seo_title'] ?? ''),
        'seo_description' => (string) ($_POST['seo_description'] ?? ''),
        'published_at' => (string) ($_POST['published_at'] ?? ''),
    ];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $error !== '') {
    foreach (['title', 'slug', 'excerpt', 'featured_image', 'status', 'blocks_json', 'palette_json', 'category_id', 'seo_title', 'seo_description', 'published_at'] as $key) {
        if (array_key_exists($key, $_POST)) {
            $post->{$key} = $_POST[$key];
        }
    }
    $post->is_featured = isset($_POST['is_featured']) ? 1 : 0;
}

$isDesign = in_array($postType, ['header', 'footer'], true);
$slugPrefix = $postType === 'page' ? ROOT . '/' : ($postType === 'post' ? ROOT . '/blog/' : 'Internal design: ');

set_value('thunder_blog_admin', [
    'post' => $post,
    'post_type' => $postType,
    'content_label' => blog_content_type_label($postType),
    'content_label_lower' => strtolower(blog_content_type_label($postType)),
    'is_design' => $isDesign,
    'is_edit' => $isEdit,
    'error' => $error,
    'categories' => $isDesign ? [] : blog_categories(),
    'tags_text' => $isEdit && in_array($postType, ['post', 'page'], true) ? blog_tags_text($id) : (string) ($_POST['tags'] ?? ''),
    'palette_presets' => blog_palette_presets(),
    'global_palette' => blog_global_palette(),
    'can_publish' => $postType === 'post' ? blog_can('publish-blog-posts') : true,
    'back_url' => blog_admin_list_url($postType),
    'slug_prefix' => $slugPrefix,
    'builder_context' => $postType,
    'menus' => blog_menu_designer_payload(),
]);
