<?php

/**
 * Thunder Config
 *
 * Admin plugin for editing safe values in the root config.php file.
 */

use ThunderConfig\Controllers\ConfigController;

if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists('thunder_config_e'))
{
    function thunder_config_e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

add_filter('admin_before_links', function($links){
    $vars = get_value();
    $adminRoute = $vars['admin_route'] ?? 'admin';

    $links[plugin_id()] = [
        [
            'title' => 'Configuration',
            'slug' => 'configuration',
            'link' => ROOT . '/' . $adminRoute . '/thunder-config',
            'icon' => 'fa-solid fa-sliders',
            'parent' => '',
            'permission' => 'manage-config',
            'order' => 10,
        ],
    ];

    return $links;
}, 20);

add_action('before_controller', function(){
    if(URL(0) !== 'admin' || URL(1) !== 'thunder-config')
    {
        return;
    }

    $req = new \Core\Request;
    if(!$req->is_ajax())
        return;

    if(!empty($_POST['action']) && $_POST['action'] === 'test_database_config')
    {
        header('Content-Type: application/json');

        $host   = trim($_POST['LOCAL_DB_HOST'] ?? '');
        $name   = trim($_POST['LOCAL_DB_NAME'] ?? '');
        $user   = trim($_POST['LOCAL_DB_USER'] ?? '');
        $pass   = $_POST['LOCAL_DB_PASSWORD'] ?? '';
        $driver = trim($_POST['LOCAL_DB_DRIVER'] ?? 'mysql');
        $port   = trim($_POST['LOCAL_DB_PORT'] ?? '3306');

        $msg = ['success'=>false,'local'=>'','remote'=>''];
        //local
        try{
            $dsn = $driver.":host={$host};port={$port};dbname={$name};charset=utf8mb4";

            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_TIMEOUT            => 5,
            ]);

            $pdo->query("SELECT 1");
            $msg['local'] = 'Database connection successful.';
            $msg['success'] = true;

        }catch(Throwable $e){

            $msg['local'] = 'Connection failed: ' . $e->getMessage();
        }

        $host   = trim($_POST['REMOTE_DB_HOST'] ?? '');
        $name   = trim($_POST['REMOTE_DB_NAME'] ?? '');
        $user   = trim($_POST['REMOTE_DB_USER'] ?? '');
        $pass   = $_POST['REMOTE_DB_PASSWORD'] ?? '';
        $driver = trim($_POST['REMOTE_DB_DRIVER'] ?? 'mysql');
        $port   = trim($_POST['REMOTE_DB_PORT'] ?? '3306');

        //remote
        try{
            $dsn = $driver.":host={$host};port={$port};dbname={$name};charset=utf8mb4";

            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_TIMEOUT            => 5,
            ]);

            $pdo->query("SELECT 1");
            $msg['remote'] = 'Database connection successful.';
            $msg['success'] = true;
        }catch(Throwable $e){

            $msg['remote'] = 'Connection failed: ' . $e->getMessage();
        }

        echo json_encode($msg);
        die;
    }

}, 10);

add_action('controller', function(){
    if(URL(0) !== 'admin' || URL(1) !== 'thunder-config')
    {
        return;
    }

    require_once plugin_path('controllers/ConfigController.php');

    $controller = new ConfigController();
    $controller->handle();
}, 10);

add_action('admin_main_content', function(){
    if(URL(0) !== 'admin' || URL(1) !== 'thunder-config')
    {
        return;
    }

    require current_look('admin/config-form.php');
}, 10);
