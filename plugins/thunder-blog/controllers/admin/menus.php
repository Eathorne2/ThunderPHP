<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

blog_require_permission('manage-blog-menus');

$route = get_route_name();
$menuId = max(0, (int) get_param('id'));
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST)) {
        $error = 'The form token expired. Refresh the page and try again.';
    } else {
        try {
            if ($route === 'blog.admin.menus.store') {
                $created = blog_save_menu_name((string) ($_POST['name'] ?? ''));
                message('success', 'Menu created. Add links and arrange its hierarchy.');
                redirect('admin/blog/menus/' . $created);
                exit;
            }

            if ($route === 'blog.admin.menu.delete') {
                blog_delete_menu($menuId);
                message('success', 'Menu deleted.');
                redirect('admin/blog/menus');
                exit;
            }

            if ($route === 'blog.admin.menu.update') {
                $action = (string) ($_POST['menu_action'] ?? 'save_items');
                $deleteItemId = max(0, (int) ($_POST['delete_item_id'] ?? 0));
                if ($deleteItemId > 0) {
                    blog_delete_menu_item($menuId, $deleteItemId);
                    message('success', 'Menu item deleted. Its direct children were moved to the top level.');
                } elseif ($action === 'save_name') {
                    blog_save_menu_name((string) ($_POST['name'] ?? ''), $menuId);
                    message('success', 'Menu name updated.');
                } elseif ($action === 'add_item') {
                    $sourceType = (string) ($_POST['item_type'] ?? 'custom');
                    $objectId = $sourceType === 'post'
                        ? (int) ($_POST['post_id'] ?? 0)
                        : ($sourceType === 'page' ? (int) ($_POST['page_id'] ?? 0) : 0);
                    blog_save_menu_item($menuId, [
                        'item_type' => $sourceType,
                        'object_id' => $objectId,
                        'title' => (string) ($_POST['item_title'] ?? ''),
                        'url' => (string) ($_POST['custom_url'] ?? ''),
                        'target' => (string) ($_POST['target'] ?? '_self'),
                    ]);
                    message('success', 'Menu item added.');
                } else {
                    blog_update_menu_items($menuId, (array) ($_POST['items'] ?? []));
                    message('success', 'Menu titles, order and hierarchy updated.');
                }
                redirect('admin/blog/menus/' . $menuId);
                exit;
            }
        } catch (\Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

$menu = $menuId > 0 ? blog_get_menu($menuId) : false;
if ($menuId > 0 && !$menu) {
    message('fail', 'The requested menu was not found.');
    redirect('admin/blog/menus');
    exit;
}

$menus = blog_menus(false);
foreach ($menus as $menuRow) {
    $menuRow->item_count = count(blog_menu_items((int) $menuRow->id));
}

$posts = blog_linkable_content('post');
$pages = blog_linkable_content('page');

set_value('thunder_blog_admin', [
    'menus' => $menus,
    'menu' => $menu,
    'menu_items' => $menu ? blog_menu_items((int) $menu->id) : [],
    'menu_tree' => $menu ? blog_menu_tree((int) $menu->id) : [],
    'posts' => $posts,
    'pages' => $pages,
    'error' => $error,
]);
