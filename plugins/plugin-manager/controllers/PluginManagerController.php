<?php

namespace PluginManager\Controllers;

use Core\Request;
use function PluginManager\all_migration_statuses;
use function PluginManager\clean_id;
use function PluginManager\create_backup_zip;
use function PluginManager\delete_dir;
use function PluginManager\download_marketplace_package;
use function PluginManager\extract_package;
use function PluginManager\find_marketplace_plugin;
use function PluginManager\install_package_file;
use function PluginManager\inspect_zip;
use function PluginManager\installed_plugins;
use function PluginManager\looks;
use function PluginManager\marketplace_data;
use function PluginManager\marketplace_default_url;
use function PluginManager\marketplace_plugin_status;
use function PluginManager\migration_status;
use function PluginManager\missing_dependency_rows;
use function PluginManager\outdated_core_rows;
use function PluginManager\plugin_config;
use function PluginManager\plugin_config_path;
use function PluginManager\plugin_path;
use function PluginManager\plugin_root;
use function PluginManager\readme;
use function PluginManager\readme_html;
use function PluginManager\run_migrations;
use function PluginManager\set_config_value;
use function PluginManager\temp_root;
use function PluginManager\write_json_file;
use function PluginManager\zip_dir;

if(!defined('ROOT')) exit('No direct script access allowed');

class PluginManagerController
{
    protected Request $request;
    protected string $adminRoute = 'admin';

    public function __construct()
    {
        $this->request = new Request();
        $vars = get_value();
        $this->adminRoute = $vars['admin_route'] ?? 'admin';
    }

    public function handle(): void
    {
        if(URL(0) !== $this->adminRoute || URL(1) !== 'plugins') return;

        $this->guard('manage-plugins');
        $this->handle_post();

        $page = URL(2) ?: 'index';

        match($page){
            'view' => $this->view(clean_id((string)URL(3))),
            'install' => $this->install(),
            'config' => $this->config(clean_id((string)URL(3))),
            'looks' => $this->looks(clean_id((string)URL(3))),
            'export' => $this->export(clean_id((string)URL(3))),
            'dependencies' => $this->dependencies(),
            'migrations' => $this->migrations(clean_id((string)URL(3))),
            'marketplace' => $this->marketplace(),
            'marketplace-settings' => $this->marketplace_settings(),
            default => $this->index(),
        };
    }

    protected function guard(string $permission): void
    {
        if(!user_can($permission)){
            message('fail', 'You do not have permission to access the plugin manager.');
            redirect($this->adminRoute);
        }
    }

    protected function verify_csrf(array $post): void
    {
        if(!csrf_verify($post)){
            message('fail', 'Invalid security token. Please try again.');
            redirect($this->adminRoute . '/plugins');
        }
    }

    protected function handle_post(): void
    {
        if(!$this->request->posted()) return;

        $post = $this->request->post();
        $this->verify_csrf($post);
        $action = $post['pm_action'] ?? '';

        if($action === 'toggle_active'){
            $id = clean_id($post['plugin_id'] ?? '');
            $json = plugin_config($id);
            if($json){
                create_backup_zip($id);
                $json->active = empty($json->active);
                write_json_file(plugin_config_path($id), $json);
                message('success', 'Plugin status updated.');
            }
            redirect($this->adminRoute . '/plugins');
        }

        if($action === 'save_index'){
            $id = clean_id($post['plugin_id'] ?? '');
            set_config_value($id, 'index', (int)($post['index'] ?? 100));
            message('success', 'Plugin priority updated.');
            redirect($this->adminRoute . '/plugins');
        }

        if($action === 'save_config'){
            $this->guard('edit-plugin-config');
            $id = clean_id($post['plugin_id'] ?? '');
            $raw = trim($post['config_json'] ?? '');
            $json = json_decode($raw);
            if(json_last_error() !== JSON_ERROR_NONE){
                message('fail', 'Invalid JSON: ' . json_last_error_msg());
                redirect($this->adminRoute . '/plugins/config/' . $id);
            }
            create_backup_zip($id);
            write_json_file(plugin_config_path($id), $json);
            message('success', 'Config file saved.');
            redirect($this->adminRoute . '/plugins/view/' . $id);
        }

        if($action === 'save_marketplace_settings'){
            $url = trim($post['marketplace_url'] ?? '');
            set_config_value('plugin-manager', 'marketplace_url', $url);
            message('success', 'Marketplace settings saved.');
            redirect($this->adminRoute . '/plugins/marketplace');
        }

        if($action === 'delete_plugin'){
            $this->guard('delete-plugins');
            $id = clean_id($post['plugin_id'] ?? '');
            if($id === 'plugin-manager'){
                message('fail', 'Plugin Manager cannot delete itself.');
            }elseif(is_dir(plugin_path($id))){
                create_backup_zip($id);
                delete_dir(plugin_path($id));
                message('success', 'Plugin deleted after creating a backup.');
            }
            redirect($this->adminRoute . '/plugins');
        }

        if($action === 'set_active_look'){
            $this->guard('manage-plugin-looks');
            $id = clean_id($post['plugin_id'] ?? '');
            $look = clean_id($post['look'] ?? '');
            if(is_file(plugin_path($id) . 'looks/' . $look . '/look.json')){
                set_config_value($id, 'look', $look);
                message('success', 'Active look updated.');
            }
            redirect($this->adminRoute . '/plugins/looks/' . $id);
        }

        if($action === 'delete_look'){
            $this->guard('delete-plugins');
            $id = clean_id($post['plugin_id'] ?? '');
            $look = clean_id($post['look'] ?? '');
            $json = plugin_config($id);
            if($json && ($json->look ?? '') === $look){
                message('fail', 'You cannot delete the active look. Switch looks first.');
            }elseif(is_dir(plugin_path($id) . 'looks/' . $look)){
                create_backup_zip($id);
                delete_dir(plugin_path($id) . 'looks/' . $look);
                message('success', 'Look deleted after creating a plugin backup.');
            }
            redirect($this->adminRoute . '/plugins/looks/' . $id);
        }

        if($action === 'upload_plugin') $this->upload_plugin();
        if($action === 'confirm_install') $this->confirm_install($post);

        if($action === 'upload_look'){
            $this->guard('manage-plugin-looks');
            $this->upload_look($post);
        }

        if($action === 'run_migrations'){
            $id = clean_id($post['plugin_id'] ?? '');
            $result = run_migrations($id);
            message($result['ok'] ? 'success' : 'fail', nl2br($result['output']));
            redirect($this->adminRoute . '/plugins/migrations/' . $id);
        }

        if($action === 'marketplace_install'){
            $this->marketplace_install($post);
        }
    }

    protected function marketplace_install(array $post): void
    {
        $id = clean_id($post['plugin_id'] ?? '');
        $remote = find_marketplace_plugin($id);
        if(!$remote){
            message('fail', 'Marketplace plugin not found.');
            redirect($this->adminRoute . '/plugins/marketplace');
        }

        $status = marketplace_plugin_status($remote);
        if(!empty($status['missing_required'])){
            message('fail', 'Install blocked. Required dependencies are missing.');
            redirect($this->adminRoute . '/plugins/marketplace');
        }

        try{
            $zip = download_marketplace_package($remote);
            $result = install_package_file($zip, true);
            @unlink($zip);
            message($result['ok'] ? 'success' : 'fail', $result['message']);
            redirect($this->adminRoute . '/plugins/view/' . ($result['plugin_id'] ?: $id));
        }catch(\Throwable $e){
            message('fail', $e->getMessage());
            redirect($this->adminRoute . '/plugins/marketplace');
        }
    }

    protected function upload_look(array $post): void
    {
        $id = clean_id($post['plugin_id'] ?? '');
        if(empty($_FILES['look_zip']['tmp_name'])){
            message('fail', 'Please select a look ZIP file.');
            redirect($this->adminRoute . '/plugins/looks/' . $id);
        }
        $tmpName = temp_root() . 'look-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.zip';
        move_uploaded_file($_FILES['look_zip']['tmp_name'], $tmpName);
        $inspection = inspect_zip($tmpName, 'look');
        if(!$inspection['ok']){
            @unlink($tmpName);
            message('fail', $inspection['message']);
            redirect($this->adminRoute . '/plugins/looks/' . $id);
        }
        $lookPlugin = $inspection['config']->plugin ?? '';
        if($lookPlugin && $lookPlugin !== $id){
            @unlink($tmpName);
            message('fail', 'This look belongs to a different plugin: ' . $lookPlugin);
            redirect($this->adminRoute . '/plugins/looks/' . $id);
        }
        create_backup_zip($id);
        extract_package($tmpName, plugin_path($id) . 'looks' . DIRECTORY_SEPARATOR);
        @unlink($tmpName);
        message('success', 'Look installed.');
        redirect($this->adminRoute . '/plugins/looks/' . $id);
    }

    protected function upload_plugin(): void
    {
        if(empty($_FILES['plugin_zip']['tmp_name'])){
            message('fail', 'Please select a plugin ZIP file.');
            redirect($this->adminRoute . '/plugins/install');
        }
        $tmpName = temp_root() . 'package-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.zip';
        move_uploaded_file($_FILES['plugin_zip']['tmp_name'], $tmpName);
        $inspection = inspect_zip($tmpName, 'plugin');
        if(!$inspection['ok']){
            @unlink($tmpName);
            message('fail', $inspection['message']);
            redirect($this->adminRoute . '/plugins/install');
        }
        set_value(['plugin_manager_view'=>'install', 'plugin_manager_title'=>'Install Plugin', 'plugin_manager_inspection'=>$inspection, 'plugin_manager_temp_package'=>basename($tmpName)]);
    }

    protected function confirm_install(array $post): void
    {
        $package = basename($post['package'] ?? '');
        $file = temp_root() . $package;
        if(!is_file($file)){
            message('fail', 'Temporary package was not found. Upload the ZIP again.');
            redirect($this->adminRoute . '/plugins/install');
        }
        $result = install_package_file($file, true);
        @unlink($file);
        message($result['ok'] ? 'success' : 'fail', $result['message']);
        redirect($this->adminRoute . '/plugins/view/' . ($result['plugin_id'] ?: ''));
    }

    protected function index(): void
    {
        set_value(['plugin_manager_view'=>'index', 'plugin_manager_title'=>'Plugin Manager', 'plugin_manager_plugins'=>installed_plugins(), 'plugin_manager_missing'=>missing_dependency_rows(), 'plugin_manager_outdated'=>outdated_core_rows(), 'plugin_manager_migrations'=>all_migration_statuses()]);
    }

    protected function view(string $id): void
    {
        $json = plugin_config($id);
        if(!$json){ message('fail', 'Plugin not found.'); redirect($this->adminRoute . '/plugins'); }
        $readme = readme($id);
        set_value(['plugin_manager_view'=>'view', 'plugin_manager_title'=>$json->name ?? $id, 'plugin_manager_plugin_id'=>$id, 'plugin_manager_config'=>$json, 'plugin_manager_readme'=>$readme, 'plugin_manager_readme_html'=>readme_html($id, $readme), 'plugin_manager_looks'=>looks($id), 'plugin_manager_migration_status'=>migration_status($id)]);
    }

    protected function install(): void
    {
        set_value(['plugin_manager_view'=>'install', 'plugin_manager_title'=>'Install Plugin']);
    }

    protected function config(string $id): void
    {
        $this->guard('edit-plugin-config');
        $file = plugin_config_path($id);
        if(!is_file($file)){ message('fail', 'Config file not found.'); redirect($this->adminRoute . '/plugins'); }
        set_value(['plugin_manager_view'=>'config', 'plugin_manager_title'=>'Edit Config', 'plugin_manager_plugin_id'=>$id, 'plugin_manager_config_raw'=>file_get_contents($file)]);
    }

    protected function looks(string $id): void
    {
        $this->guard('manage-plugin-looks');
        $json = plugin_config($id);
        if(!$json){ message('fail', 'Plugin not found.'); redirect($this->adminRoute . '/plugins'); }
        set_value(['plugin_manager_view'=>'looks', 'plugin_manager_title'=>'Looks', 'plugin_manager_plugin_id'=>$id, 'plugin_manager_config'=>$json, 'plugin_manager_looks'=>looks($id)]);
    }

    protected function export(string $id): void
    {
        $this->guard('manage-plugins');
        if(!is_dir(plugin_path($id))){ message('fail', 'Plugin not found.'); redirect($this->adminRoute . '/plugins'); }
        $zip = temp_root() . $id . '-' . date('Ymd-His') . '.zip';
        zip_dir(plugin_path($id), $zip, $id);
        if(!is_file($zip)){ message('fail', 'Could not create export ZIP.'); redirect($this->adminRoute . '/plugins/view/' . $id); }
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zip) . '"');
        header('Content-Length: ' . filesize($zip));
        readfile($zip);
        @unlink($zip);
        exit;
    }

    protected function dependencies(): void
    {
        set_value(['plugin_manager_view'=>'dependencies', 'plugin_manager_title'=>'Dependencies', 'plugin_manager_missing'=>missing_dependency_rows(), 'plugin_manager_outdated'=>outdated_core_rows()]);
    }

    protected function migrations(string $id = ''): void
    {
        set_value(['plugin_manager_view'=>'migrations', 'plugin_manager_title'=>'Migration Status', 'plugin_manager_plugin_id'=>$id, 'plugin_manager_migrations'=>$id ? [$id => ['plugin'=>installed_plugins()[$id] ?? [], 'rows'=>migration_status($id)]] : all_migration_statuses()]);
    }

    protected function marketplace(): void
    {
        $data = marketplace_data();
        set_value(['plugin_manager_view'=>'marketplace', 'plugin_manager_title'=>'Marketplace', 'plugin_manager_marketplace'=>$data, 'plugin_manager_marketplace_url'=>marketplace_default_url()]);
    }

    protected function marketplace_settings(): void
    {
        set_value(['plugin_manager_view'=>'marketplace_settings', 'plugin_manager_title'=>'Marketplace Settings', 'plugin_manager_marketplace_url'=>marketplace_default_url()]);
    }
}
