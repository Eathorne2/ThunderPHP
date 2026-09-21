<?php

namespace PluginManager;

use PluginManager\Controllers\PluginManagerController;

if(!defined('ROOT')) exit('No direct script access allowed');

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/controllers/PluginManagerController.php';

add_filter('permissions', function($permissions){
    $config = plugin_config('plugin-manager');
    if($config && !empty($config->permissions)){
        foreach($config->permissions as $permission){
            $permissions[] = $permission;
        }
    }
    return $permissions;
});

add_filter('admin_before_links', function($links){
    $vars = get_value();
    $adminRoute = $vars['admin_route'] ?? 'admin';

    $links[plugin_id()] = [
        [
            'title' => 'Plugins',
            'slug' => 'plugins',
            'link' => ROOT . '/' . $adminRoute . '/plugins',
            'icon' => 'fa-solid fa-plug',
            'parent' => '',
            'permission' => 'manage-plugins',
            'order' => 10,
        ],
        [
            'title' => 'All Plugins',
            'slug' => 'all-plugin',
            'link' => ROOT . '/' . $adminRoute . '/plugins',
            'icon' => 'fa-solid fa-plug',
            'parent' => 'plugins',
            'permission' => 'manage-plugins',
            'order' => 20,
        ],
        [
            'title' => 'Install Plugin',
            'slug' => 'install-plugin',
            'link' => ROOT . '/' . $adminRoute . '/plugins/install',
            'icon' => 'fa-solid fa-file-arrow-up',
            'parent' => 'plugins',
            'permission' => 'manage-plugins',
            'order' => 20,
        ],
        [
            'title' => 'Marketplace',
            'slug' => 'plugin-marketplace',
            'link' => ROOT . '/' . $adminRoute . '/plugins/marketplace',
            'icon' => 'fa-solid fa-store',
            'parent' => 'plugins',
            'permission' => 'install-marketplace-plugins',
            'order' => 30,
        ],
        [
            'title' => 'Migrations',
            'slug' => 'plugin-migrations',
            'link' => ROOT . '/' . $adminRoute . '/plugins/migrations',
            'icon' => 'fa-solid fa-database',
            'parent' => 'plugins',
            'permission' => 'manage-plugins',
            'order' => 40,
        ],
        [
            'title' => 'Dependencies',
            'slug' => 'plugin-dependencies',
            'link' => ROOT . '/' . $adminRoute . '/plugins/dependencies',
            'icon' => 'fa-solid fa-link',
            'parent' => 'plugins',
            'permission' => 'manage-plugins',
            'order' => 50,
        ],
    ];

    return $links;
});


add_filter('admin_page_title', function($title){
    $pluginTitle = get_value('plugin_manager_title');
    return !empty($pluginTitle) ? $pluginTitle : $title;
});

add_filter('admin_body_classes', function($classes){
    if(URL(0) === (get_value()['admin_route'] ?? 'admin') && URL(1) === 'plugins'){
        if(is_array($classes)) $classes[] = 'pm-admin-page';
        elseif(is_string($classes)) $classes .= ' pm-admin-page';
    }
    return $classes;
});

add_action('controller', function(){
    $controller = new PluginManagerController();
    $controller->handle();
});

add_action('admin_main_content', function(){
    $view = get_value('plugin_manager_view');
    if(empty($view)) return;
    $file = current_look('admin/' . $view . '.php');
    if(is_file($file)) include $file;
});

add_action('html_admin_head', function(){
    echo '<link rel="stylesheet" href="' . current_look_http('assets/css/plugin-manager.css') . '">' . PHP_EOL;
});

add_action('html_admin_footer', function(){
    echo '<script src="' . current_look_http('assets/js/plugin-manager.js') . '"></script>' . PHP_EOL;
});
