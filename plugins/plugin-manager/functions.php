<?php

namespace PluginManager;

if(!defined('ROOT')) exit('No direct script access allowed');

require_once __DIR__ . '/lib/SafeMarkdown.php';

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function plugin_root(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR;
}

function plugin_path(string $pluginId = ''): string
{
    $pluginId = clean_id($pluginId);
    return plugin_root() . ($pluginId ? $pluginId . DIRECTORY_SEPARATOR : '');
}

function backup_root(): string
{
    $path = plugin_root() . '_plugin-manager-backups' . DIRECTORY_SEPARATOR;
    ensure_dir($path);
    return $path;
}

function temp_root(): string
{
    $path = plugin_root() . '_plugin-manager-temp' . DIRECTORY_SEPARATOR;
    ensure_dir($path);
    return $path;
}

function ensure_dir(string $path): void
{
    if(!is_dir($path)) mkdir($path, 0775, true);
}

function clean_id(string $id): string
{
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $id);
}

function safe_join(string $base, string $relative): string
{
    $relative = str_replace(['\\', "\0"], ['/', ''], $relative);
    $relative = ltrim($relative, '/');
    $parts = [];
    foreach(explode('/', $relative) as $part){
        if($part === '' || $part === '.') continue;
        if($part === '..') continue;
        $parts[] = $part;
    }
    return rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
}

function read_json_file(string $file): ?object
{
    if(!is_file($file)) return null;
    $json = json_decode(file_get_contents($file));
    return json_last_error() === JSON_ERROR_NONE ? $json : null;
}

function write_json_file(string $file, array|object $data): bool
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if($json === false) return false;
    return atomic_write($file, $json . PHP_EOL);
}

function atomic_write(string $file, string $content): bool
{
    $tmp = $file . '.tmp';
    if(file_put_contents($tmp, $content, LOCK_EX) === false) return false;
    return rename($tmp, $file);
}

function plugin_config_path(string $pluginId): string
{
    return plugin_path($pluginId) . 'config.json';
}

function plugin_config(string $pluginId): ?object
{
    return read_json_file(plugin_config_path($pluginId));
}

function installed_plugins(): array
{
    $items = [];
    foreach(glob(plugin_root() . '*', GLOB_ONLYDIR) ?: [] as $dir){
        $id = basename($dir);
        if(str_starts_with($id, '_')) continue;
        $configFile = $dir . DIRECTORY_SEPARATOR . 'config.json';
        $json = read_json_file($configFile);
        if(!$json) continue;
        $items[$id] = plugin_summary($id, $json);
    }
    uasort($items, fn($a, $b) => (($a['index'] ?? 100) <=> ($b['index'] ?? 100)) ?: strcmp($a['name'], $b['name']));
    return $items;
}

function plugin_summary(string $id, object $json): array
{
    $dir = plugin_path($id);
    return [
        'id' => $id,
        'name' => $json->name ?? $id,
        'version' => $json->version ?? '',
        'description' => $json->description ?? '',
        'author' => $json->author ?? '',
        'website' => $json->website ?? '',
        'thumbnail' => thumbnail_url($id, $json->thumbnail ?? ''),
        'active' => !empty($json->active),
        'look' => $json->look ?? '',
        'index' => (int)($json->index ?? 100),
        'core_requires' => $json->core_requires ?? '',
        'dependencies' => dependencies($json),
        'routes_count' => count(routes($json)),
        'permissions_count' => count(permissions($json)),
        'looks_count' => count(looks($id)),
        'readme' => is_file($dir . 'README.md'),
        'has_plugin_php' => is_file($dir . 'plugin.php'),
        'health' => health($id, $json),
    ];
}

function thumbnail_url(string $pluginId, string $thumbnail): string
{
    if(trim($thumbnail) === '') return '';
    $path = plugin_path($pluginId) . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $thumbnail);
    if(!is_file($path)) return '';
    return ROOT . '/plugins/' . rawurlencode($pluginId) . '/' . str_replace('%2F', '/', rawurlencode(str_replace('\\', '/', $thumbnail)));
}

function dependencies(object $json): array
{
    $deps = [];
    foreach((array)($json->dependencies ?? []) as $id => $dep){
        $deps[$id] = [
            'id' => $id,
            'name' => $dep->name ?? $id,
            'version' => $dep->version ?? '',
            'required' => !empty($dep->required),
            'installed' => is_file(plugin_config_path((string)$id)),
        ];
    }
    return $deps;
}

function routes(object $json): array
{
    return (array)($json->routes->routes ?? []);
}

function permissions(object $json): array
{
    return (array)($json->permissions ?? []);
}

function looks(string $pluginId): array
{
    $base = plugin_path($pluginId) . 'looks' . DIRECTORY_SEPARATOR;
    $items = [];
    if(!is_dir($base)) return [];
    foreach(glob($base . '*', GLOB_ONLYDIR) ?: [] as $dir){
        $name = basename($dir);
        $json = read_json_file($dir . DIRECTORY_SEPARATOR . 'look.json');
        $items[$name] = [
            'folder' => $name,
            'path' => $dir,
            'valid' => (bool)$json,
            'name' => $json->name ?? $name,
            'version' => $json->version ?? '',
            'plugin' => $json->plugin ?? '',
            'plugin_requires' => $json->plugin_requires ?? '',
            'author' => $json->author ?? '',
            'description' => $json->description ?? '',
            'thumbnail' => $json->thumbnail ?? '',
        ];
    }
    return $items;
}

function health(string $pluginId, object $json): array
{
    $issues = [];
    if(!is_file(plugin_path($pluginId) . 'plugin.php')) $issues[] = 'Missing plugin.php';
    if(empty($json->id)) $issues[] = 'Missing plugin id';
    if(($json->id ?? $pluginId) !== $pluginId) $issues[] = 'Folder name does not match config id';
    if(!empty($json->look) && !is_file(plugin_path($pluginId) . 'looks/' . $json->look . '/look.json')) $issues[] = 'Active look folder is missing or invalid';
    foreach(dependencies($json) as $dep){
        if($dep['required'] && !$dep['installed']) $issues[] = 'Missing required dependency: ' . $dep['name'];
    }
    return $issues;
}

function missing_dependency_rows(): array
{
    return \missing_plugins();;
}

function outdated_core_rows(): array
{
    return \outdated_plugins();;
}

function readme(string $pluginId): string
{
    $file = plugin_path($pluginId) . 'README.md';
    return is_file($file) ? (string)file_get_contents($file) : '';
}

function load_parsedown(): bool
{
    if(class_exists('\Parsedown')) return true;

    $candidates = [
        getcwd() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'erusev' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR . 'Parsedown.php',
        dirname(plugin_root()) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'erusev' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR . 'Parsedown.php',
        dirname(dirname(plugin_root())) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'erusev' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR . 'Parsedown.php',
        getcwd() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'third-party' . DIRECTORY_SEPARATOR . 'Parsedown.php',
        getcwd() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'thunder' . DIRECTORY_SEPARATOR . 'Parsedown.php',
    ];

    foreach(array_unique($candidates) as $file){
        if(!is_file($file)) continue;
        require_once $file;
        if(class_exists('\Parsedown')) return true;
    }

    return false;
}

function readme_html(string $pluginId, ?string $markdown = null): string
{
    $markdown ??= readme($pluginId);
    if(trim($markdown) === '') return '';

    // Prefer Parsedown when the application provides it; otherwise use the bundled safe fallback.
    if(load_parsedown()){
        try{
            $parsedown = new \Parsedown();
            if(method_exists($parsedown, 'setSafeMode')) $parsedown->setSafeMode(true);
            if(method_exists($parsedown, 'setBreaksEnabled')) $parsedown->setBreaksEnabled(false);
            return (string)$parsedown->text($markdown);
        }catch(\Throwable $e){
            // Fall through to the bundled renderer so README pages still work.
        }
    }

    return (new SafeMarkdown($pluginId))->text($markdown);
}

function create_backup_zip(string $pluginId): string
{
    $source = plugin_path($pluginId);
    if(!is_dir($source)) return '';
    $dest = backup_root() . $pluginId . '-backup-' . date('Ymd-His') . '.zip';
    zip_dir($source, $dest, $pluginId);
    return $dest;
}

function zip_dir(string $source, string $dest, string $rootName): bool
{
    $zip = new \ZipArchive();
    if($zip->open($dest, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) return false;
    $source = rtrim($source, DIRECTORY_SEPARATOR);
    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS));
    foreach($files as $file){
        if(!$file->isFile()) continue;
        $path = $file->getPathname();
        $relative = $rootName . '/' . ltrim(str_replace($source, '', $path), DIRECTORY_SEPARATOR);
        $zip->addFile($path, str_replace('\\', '/', $relative));
    }
    return $zip->close();
}

function inspect_zip(string $zipFile, string $type = 'plugin'): array
{
    $zip = new \ZipArchive();
    $result = ['ok'=>false, 'message'=>'Could not open ZIP file.', 'root'=>'', 'config'=>null, 'files'=>[], 'unsafe'=>[]];
    if($zip->open($zipFile) !== true) return $result;
    $roots = [];
    for($i = 0; $i < $zip->numFiles; $i++){
        $name = $zip->getNameIndex($i);
        $result['files'][] = $name;
        if(str_contains($name, '../') || str_starts_with($name, '/') || preg_match('#^[a-zA-Z]:#', $name)) $result['unsafe'][] = $name;
        $first = explode('/', trim($name, '/'))[0] ?? '';
        if($first !== '') $roots[$first] = true;
    }
    if(!empty($result['unsafe'])){ $zip->close(); $result['message'] = 'Unsafe paths found in ZIP.'; return $result; }
    if(count($roots) !== 1){ $zip->close(); $result['message'] = 'ZIP must contain one root folder.'; return $result; }
    $root = array_key_first($roots);
    $configName = $type === 'look' ? 'look.json' : 'config.json';
    $configText = $zip->getFromName($root . '/' . $configName);
    if($configText === false){ $zip->close(); $result['message'] = 'Missing ' . $configName . ' in root folder.'; return $result; }
    $json = json_decode($configText);
    if(json_last_error() !== JSON_ERROR_NONE){ $zip->close(); $result['message'] = 'Invalid ' . $configName . ': ' . json_last_error_msg(); return $result; }
    $zip->close();
    $result['ok'] = true; $result['message'] = 'Package looks valid.'; $result['root'] = $root; $result['config'] = $json;
    return $result;
}

function extract_package(string $zipFile, string $destRoot, string $expectedRoot = ''): bool
{
    $zip = new \ZipArchive();
    if($zip->open($zipFile) !== true) return false;
    for($i = 0; $i < $zip->numFiles; $i++){
        $name = $zip->getNameIndex($i);
        if(str_ends_with($name, '/')) continue;
        if(str_contains($name, '../') || str_starts_with($name, '/') || preg_match('#^[a-zA-Z]:#', $name)) continue;
        $target = safe_join($destRoot, $name);
        ensure_dir(dirname($target));
        copy('zip://' . $zipFile . '#' . $name, $target);
    }
    $zip->close();
    return true;
}

function run_migrations(string $pluginId): array
{
    $pluginId = clean_id($pluginId);
    try{
        require_once 'app/thunder/Database.php';
        require_once 'app/models/Migration.php';
        require_once 'app/thunder/MigrationTracker.php';
        require_once 'app/thunder/thunder.php';
        $thunder = new \Thunder\Thunder;

        ob_start();
        $thunder->migrate([
            '0',
            'migrate',
            $pluginId
        ]);

        $output = trim(ob_get_clean());
        return ["ok" => !str_contains(strtolower($output), "no direct migration runner"), "output" => $output !== "" ? $output : "Migrations completed."];
    }catch(\Throwable $e){
        if(ob_get_level() > 0) ob_end_clean();
        return ["ok"=>false, "output"=>"Migration failed: " . $e->getMessage()];
    }
}

function set_config_value(string $pluginId, string $key, $value): bool
{
    $json = plugin_config($pluginId);
    if(!$json) return false;
    $json->{$key} = $value;
    create_backup_zip($pluginId);
    return write_json_file(plugin_config_path($pluginId), $json);
}

function delete_dir(string $dir): bool
{
    if(!is_dir($dir)) return false;
    $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
    foreach($it as $file){
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    return rmdir($dir);
}

function marketplace_default_url(): string
{
    $config = plugin_config('plugin-manager');
    return trim((string)($config->marketplace_url ?? ''));
}

function marketplace_cache_root(): string
{
    $path = temp_root() . 'marketplace' . DIRECTORY_SEPARATOR;
    ensure_dir($path);
    return $path;
}

function fetch_url(string $url): string
{
    $url = trim($url);
    if($url === '') throw new \Exception('Marketplace URL is empty.');

    $context = stream_context_create([
        'http' => ['timeout' => 20, 'follow_location' => 1, 'user_agent' => 'ThunderPHP Plugin Manager/1.0'],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $data = @file_get_contents($url, false, $context);
    if($data === false) throw new \Exception('Could not fetch remote URL: ' . $url);
    return $data;
}

function marketplace_data(string $url = ''): array
{
    $url = $url ?: marketplace_default_url();
    if($url === '') return ['ok'=>false, 'message'=>'No marketplace URL configured.', 'data'=>null, 'plugins'=>[]];

    try{
        $body = fetch_url($url);
        $json = json_decode($body);
        if(json_last_error() !== JSON_ERROR_NONE) return ['ok'=>false, 'message'=>'Invalid marketplace JSON: ' . json_last_error_msg(), 'data'=>null, 'plugins'=>[]];
        return ['ok'=>true, 'message'=>'Marketplace loaded.', 'data'=>$json, 'plugins'=>(array)($json->plugins ?? [])];
    }catch(\Throwable $e){
        return ['ok'=>false, 'message'=>$e->getMessage(), 'data'=>null, 'plugins'=>[]];
    }
}

function marketplace_plugin_status(object $remote): array
{
    $id = clean_id((string)($remote->id ?? ''));
    $local = $id ? plugin_config($id) : null;
    $installed = (bool)$local;
    $localVersion = $local->version ?? '';
    $remoteVersion = $remote->version ?? '';
    $update = $installed && $localVersion !== '' && $remoteVersion !== '' && version_compare((string)$remoteVersion, (string)$localVersion, '>');

    $deps = [];
    foreach((array)($remote->requires ?? []) as $dep){
        $depId = clean_id((string)(is_object($dep) ? ($dep->id ?? '') : $dep));
        if($depId === '') continue;
        $deps[] = ['id'=>$depId, 'installed'=>is_file(plugin_config_path($depId)), 'required'=>true];
    }
    foreach((array)($remote->optional ?? []) as $dep){
        $depId = clean_id((string)(is_object($dep) ? ($dep->id ?? '') : $dep));
        if($depId === '') continue;
        $deps[] = ['id'=>$depId, 'installed'=>is_file(plugin_config_path($depId)), 'required'=>false];
    }

    return [
        'id'=>$id,
        'installed'=>$installed,
        'local_version'=>$localVersion,
        'remote_version'=>$remoteVersion,
        'update_available'=>$update,
        'dependencies'=>$deps,
        'missing_required'=>array_values(array_filter($deps, fn($d) => $d['required'] && !$d['installed'])),
    ];
}

function find_marketplace_plugin(string $pluginId, string $url = ''): ?object
{
    $data = marketplace_data($url);
    if(!$data['ok']) return null;
    foreach($data['plugins'] as $plugin){
        if(clean_id((string)($plugin->id ?? '')) === $pluginId) return $plugin;
    }
    return null;
}

function download_marketplace_package(object $remote): string
{
    $url = trim((string)($remote->download_url ?? ''));
    if($url === '') throw new \Exception('This marketplace item has no download_url.');

    $id = clean_id((string)($remote->id ?? 'plugin'));
    $version = clean_id(str_replace('.', '-', (string)($remote->version ?? date('YmdHis'))));
    $zipPath = marketplace_cache_root() . $id . '-' . $version . '-' . bin2hex(random_bytes(4)) . '.zip';

    $body = fetch_url($url);
    if(file_put_contents($zipPath, $body, LOCK_EX) === false) throw new \Exception('Could not save downloaded package.');

    $expected = trim((string)($remote->checksum ?? ''));
    $expected = preg_replace('/^sha256[:\-]/i', '', $expected);
    if($expected !== ''){
        $actual = hash_file('sha256', $zipPath);
        if(!hash_equals(strtolower($expected), strtolower($actual))){
            @unlink($zipPath);
            throw new \Exception('Downloaded file checksum does not match.');
        }
    }

    return $zipPath;
}

function install_package_file(string $zipPath, bool $runMigrations = true): array
{
    $inspection = inspect_zip($zipPath, 'plugin');
    if(!$inspection['ok']) return ['ok'=>false, 'message'=>$inspection['message'], 'plugin_id'=>''];

    $id = clean_id((string)($inspection['config']->id ?? $inspection['root']));
    if($id === '') return ['ok'=>false, 'message'=>'Plugin ID is missing from package config.json.', 'plugin_id'=>''];

    if(is_dir(plugin_path($id))) create_backup_zip($id);
    extract_package($zipPath, plugin_root());

    $migration = ['ok'=>true, 'output'=>'Migrations were not run.'];
    if($runMigrations) $migration = run_migrations($id);

    return ['ok'=>true, 'message'=>'Plugin installed or updated. ' . trim((string)$migration['output']), 'plugin_id'=>$id, 'inspection'=>$inspection, 'migration'=>$migration];
}

function migration_files(string $pluginId): array
{
    $dir = plugin_path($pluginId) . 'migrations' . DIRECTORY_SEPARATOR;
    $items = [];
    if(!is_dir($dir)) return [];
    foreach(glob($dir . '*.php') ?: [] as $file){
        $name = basename($file);
        $items[$name] = ['name'=>$name, 'path'=>$file, 'checksum'=>hash_file('sha256', $file)];
    }
    ksort($items);
    return $items;
}

function ran_migrations(string $pluginId): array
{
    $db = new \Core\Database;
    $rows = $db->query(
        "SELECT migration_name, batch, checksum, ran_at FROM thunder_migrations WHERE plugin_id = :plugin_id ORDER BY migration_name ASC",
        ['plugin_id' => $pluginId]
    );
    $items = [];
    if(!empty($rows)){
        foreach($rows as $row){
            $items[$row->migration_name] = ['name'=>$row->migration_name, 'batch'=>$row->batch, 'checksum'=>$row->checksum, 'ran_at'=>$row->ran_at];
        }
    }
    return $items;
}

function migration_status(string $pluginId): array
{
    $files = migration_files($pluginId);
    $ran = ran_migrations($pluginId);
    $rows = [];

    foreach($files as $name => $file){
        $hasRun = isset($ran[$name]);
        $checksumChanged = $hasRun && !empty($ran[$name]['checksum']) && $ran[$name]['checksum'] !== $file['checksum'];
        $rows[] = [
            'name'=>$name,
            'status'=>$hasRun ? ($checksumChanged ? 'changed' : 'ran') : 'pending',
            'file_checksum'=>$file['checksum'],
            'db_checksum'=>$ran[$name]['checksum'] ?? '',
            'batch'=>$ran[$name]['batch'] ?? '',
            'ran_at'=>$ran[$name]['ran_at'] ?? '',
        ];
    }

    foreach($ran as $name => $row){
        if(isset($files[$name])) continue;
        $rows[] = ['name'=>$name, 'status'=>'missing-file', 'file_checksum'=>'', 'db_checksum'=>$row['checksum'] ?? '', 'batch'=>$row['batch'] ?? '', 'ran_at'=>$row['ran_at'] ?? ''];
    }

    return $rows;
}

function all_migration_statuses(): array
{
    $plugins = installed_plugins();
    $items = [];
    foreach($plugins as $id => $plugin){
        $status = migration_status($id);
        $items[$id] = [
            'plugin'=>$plugin,
            'rows'=>$status,
            'pending'=>count(array_filter($status, fn($row) => $row['status'] === 'pending')),
            'changed'=>count(array_filter($status, fn($row) => $row['status'] === 'changed')),
            'ran'=>count(array_filter($status, fn($row) => $row['status'] === 'ran')),
        ];
    }
    return $items;
}
