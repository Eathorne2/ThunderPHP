<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

require_once plugin_path('functions.php');

set_value(['thunder_blog' => ['plugin_route' => 'blog', 'admin_route' => 'admin', 'tables' => blog_tables()]]);

add_filter('admin_before_links', function (array $links): array {
    $links[plugin_id()] = [
        ['title' => 'Blog', 'slug' => 'thunder-blog', 'link' => ROOT . '/admin/blog', 'icon' => 'fa-solid fa-pen-nib', 'parent' => '', 'permission' => 'view-blog-admin'],
        ['title' => 'All Posts', 'slug' => 'thunder-blog-posts', 'link' => ROOT . '/admin/blog', 'icon' => 'fa-solid fa-newspaper', 'parent' => 'thunder-blog', 'permission' => 'view-blog-admin'],
        ['title' => 'Add Post', 'slug' => 'thunder-blog-create', 'link' => ROOT . '/admin/blog/create', 'icon' => 'fa-solid fa-square-plus', 'parent' => 'thunder-blog', 'permission' => 'create-blog-posts'],
        ['title' => 'Pages', 'slug' => 'thunder-blog-pages', 'link' => ROOT . '/admin/blog/pages', 'icon' => 'fa-regular fa-file-lines', 'parent' => 'thunder-blog', 'permission' => 'manage-blog-pages'],
        ['title' => 'Menus', 'slug' => 'thunder-blog-menus', 'link' => ROOT . '/admin/blog/menus', 'icon' => 'fa-solid fa-bars', 'parent' => 'thunder-blog', 'permission' => 'manage-blog-menus'],
        ['title' => 'Headers & Footers', 'slug' => 'thunder-blog-designs', 'link' => ROOT . '/admin/blog/designs', 'icon' => 'fa-solid fa-object-group', 'parent' => 'thunder-blog', 'permission' => 'manage-blog-designs'],
        ['title' => 'Shortcodes', 'slug' => 'thunder-blog-shortcodes', 'link' => ROOT . '/admin/blog/shortcodes', 'icon' => 'fa-solid fa-code', 'parent' => 'thunder-blog', 'permission' => 'use-blog-shortcode-generator'],
        ['title' => 'Categories', 'slug' => 'thunder-blog-categories', 'link' => ROOT . '/admin/blog/categories', 'icon' => 'fa-solid fa-folder-tree', 'parent' => 'thunder-blog', 'permission' => 'manage-blog-categories'],
        ['title' => 'Settings', 'slug' => 'thunder-blog-settings', 'link' => ROOT . '/admin/blog/settings', 'icon' => 'fa-solid fa-sliders', 'parent' => 'thunder-blog', 'permission' => 'manage-blog-settings'],
    ];
    return $links;
});

// Frontend posts and archives.
add_action('controller', static fn () => require_once plugin_path('controllers/frontend/index.php'), 10, 'blog.index');
add_action('controller', static fn () => require_once plugin_path('controllers/frontend/index.php'), 10, 'blog.category');
add_action('controller', static fn () => require_once plugin_path('controllers/frontend/index.php'), 10, 'blog.tag');
add_action('controller', static fn () => require_once plugin_path('controllers/frontend/search.php'), 10, 'blog.search');
add_action('controller', static function (): void { if (get_route_name() === 'blog.show') require_once plugin_path('controllers/frontend/show.php'); }, 10, 'blog.show');
foreach (['blog.index', 'blog.category', 'blog.tag'] as $route) {
    add_action('view', static fn () => blog_render_frontend('index.php', (array) get_value('thunder_blog_page')), 10, $route);
}
add_action('view', static fn () => blog_render_frontend('search.php', (array) get_value('thunder_blog_page')), 10, 'blog.search');
add_action('view', static function (): void { if (get_route_name() === 'blog.show') blog_render_frontend('show.php', (array) get_value('thunder_blog_page')); }, 10, 'blog.show');

// Standalone /slug pages deliberately run before ThunderPHP's normal 404 redirect.
add_action('before_404_redirect', static fn () => blog_try_render_page());

// Administration controllers.
$editorRoutes = ['blog.admin.create', 'blog.admin.create.store', 'blog.admin.edit', 'blog.admin.edit.update', 'blog.admin.page.create', 'blog.admin.page.create.store', 'blog.admin.page.edit', 'blog.admin.page.edit.update', 'blog.admin.design.create', 'blog.admin.design.create.store', 'blog.admin.design.edit', 'blog.admin.design.edit.update'];
add_action('controller', static fn () => require_once plugin_path('controllers/admin/posts.php'), 10, 'blog.admin.posts');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/posts.php'), 10, 'blog.admin.pages');
foreach ($editorRoutes as $route) add_action('controller', static fn () => require_once plugin_path('controllers/admin/editor.php'), 10, $route);
add_action('controller', static fn () => require_once plugin_path('controllers/admin/delete.php'), 10, 'blog.admin.delete');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/categories.php'), 10, 'blog.admin.categories');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/categories.php'), 10, 'blog.admin.categories.store');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/settings.php'), 10, 'blog.admin.settings');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/settings.php'), 10, 'blog.admin.settings.update');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/designs.php'), 10, 'blog.admin.designs');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/designs.php'), 10, 'blog.admin.designs.activate');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/shortcodes.php'), 10, 'blog.admin.shortcodes');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/menus.php'), 10, 'blog.admin.menus');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/menus.php'), 10, 'blog.admin.menus.store');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/menus.php'), 10, 'blog.admin.menu.edit');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/menus.php'), 10, 'blog.admin.menu.update');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/menus.php'), 10, 'blog.admin.menu.delete');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/api-blocks.php'), 10, 'blog.admin.api.blocks');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/api-block.php'), 10, 'blog.admin.api.block');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/api-images.php'), 10, 'blog.admin.api.images');
add_action('controller', static fn () => require_once plugin_path('controllers/admin/api-schema.php'), 10, 'blog.admin.api.schema');

// Administration views injected into Thunder Admin.
add_action('admin_main_content', static fn () => blog_admin_view('posts.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.posts');
add_action('admin_main_content', static fn () => blog_admin_view('posts.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.pages');
foreach ($editorRoutes as $route) add_action('admin_main_content', static fn () => blog_admin_view('editor.php', (array) get_value('thunder_blog_admin')), 10, $route);
add_action('admin_main_content', static fn () => blog_admin_view('categories.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.categories');
add_action('admin_main_content', static fn () => blog_admin_view('categories.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.categories.store');
add_action('admin_main_content', static fn () => blog_admin_view('settings.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.settings');
add_action('admin_main_content', static fn () => blog_admin_view('settings.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.settings.update');
add_action('admin_main_content', static fn () => blog_admin_view('designs.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.designs');
add_action('admin_main_content', static fn () => blog_admin_view('designs.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.designs.activate');
add_action('admin_main_content', static fn () => blog_admin_view('shortcodes.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.shortcodes');
add_action('admin_main_content', static fn () => blog_admin_view('menus.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.menus');
add_action('admin_main_content', static fn () => blog_admin_view('menus.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.menus.store');
add_action('admin_main_content', static fn () => blog_admin_view('menus.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.menu.edit');
add_action('admin_main_content', static fn () => blog_admin_view('menus.php', (array) get_value('thunder_blog_admin')), 10, 'blog.admin.menu.update');

// Built-in dynamic listing shortcode. ThunderPHP shortcode callbacks echo output.
if (function_exists('add_shortcode')) {
    \add_shortcode('thunder-blog.posts', static function (array $attrs = [], array $context = []): void {
        echo blog_render_posts_shortcode($attrs, $context);
    });
}

// Public extension points.
add_filter('thunder_blog_block_index', static fn (array $blocks): array => $blocks);
add_filter('thunder_blog_author', static fn (array $author): array => $author);
add_filter('thunder_blog_post_query', static fn (array $query): array => $query);
