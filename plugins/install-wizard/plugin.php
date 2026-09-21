<?php

/**
 * Install Wizard plugin for ThunderPHP.
 *
 * Runs only when config.php is missing, then creates config.php from collected
 * first-run installation data. This plugin intentionally avoids database access
 * except during the explicit connection test on step 2.
 */

namespace InstallWizard;

if(!defined('IW_PLUGIN_ID'))
{
    define('IW_PLUGIN_ID', 'install-wizard');
}

function root_path(): string
{

    if(defined('ROOTPATH'))
    {
        return trim((string) ROOTPATH, DIRECTORY_SEPARATOR . '/\\');
    }

    if(defined('ROOT'))
    {
        return trim((string) ROOT, DIRECTORY_SEPARATOR . '/\\');
    }

    return trim(dirname(__DIR__, 2), DIRECTORY_SEPARATOR . '/\\');
}

function root_url_path(): string
{
    
    if(defined('ROOT'))
    {
        return trim((string) ROOT, DIRECTORY_SEPARATOR . '/\\');
    }

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = trim(dirname($script), '/');

    return $dir === '.' ? '' : '/' . $dir;
}

function config_path(): string
{
    return root_path() . DIRECTORY_SEPARATOR . 'config.php';
}

function sample_config_path(): string
{
    return root_path() . DIRECTORY_SEPARATOR . 'config-sample.php';
}

function plugin_config_path(): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . 'config.json';
}

function assets_logo_path(): string
{
    return root_path() . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'logo.jpg';
}

function is_installed(): bool
{
    return is_file(config_path());
}

function is_install_route(): bool
{
    if(function_exists('page') && page() === 'install')
    {
        return true;
    }

    if(function_exists('URL') && URL(0) === 'install')
    {
        return true;
    }

    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    return trim($path, '/') === 'install' || substr(trim($path, '/'), -8) === '/install';
}

function redirect_to_install(): void
{
    if(function_exists('redirect'))
    {
        redirect('install');
        exit;
    }

    header('Location: ' . root_url_path() . '/install');
    exit;
}

function redirect_to_login(): void
{
    if(function_exists('redirect'))
    {
        redirect('login');
        exit;
    }

    header('Location: ' . root_url_path() . '/login');
    exit;
}

function ensure_session(): void
{
    if(session_status() !== PHP_SESSION_ACTIVE)
    {
        @session_start();
    }

    if(empty($_SESSION['install_wizard']))
    {
        $_SESSION['install_wizard'] = [];
    }
}

function csrf_token(): string
{
    ensure_session();

    if(empty($_SESSION['install_wizard']['csrf']))
    {
        $_SESSION['install_wizard']['csrf'] = bin2hex(random_bytes(24));
    }

    return $_SESSION['install_wizard']['csrf'];
}

function csrf_valid(): bool
{
    ensure_session();
    return isset($_POST['_token'], $_SESSION['install_wizard']['csrf'])
        && hash_equals((string) $_SESSION['install_wizard']['csrf'], (string) $_POST['_token']);
}

function flash(string $type, string $message): void
{
    ensure_session();
    $_SESSION['install_wizard']['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash(): array
{
    ensure_session();
    $flash = $_SESSION['install_wizard']['flash'] ?? [];
    $_SESSION['install_wizard']['flash'] = [];
    return $flash;
}

function old(string $key, $default = '')
{
    ensure_session();
    return $_SESSION['install_wizard']['data'][$key] ?? $default;
}

function save_data(array $data): void
{
    ensure_session();
    $_SESSION['install_wizard']['data'] = array_merge($_SESSION['install_wizard']['data'] ?? [], $data);
}

function detect_environment(): string
{
    $host = strtolower($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');
    $server = strtolower($_SERVER['SERVER_NAME'] ?? '');

    $local_hosts = ['localhost', '127.0.0.1', '::1'];

    foreach($local_hosts as $local)
    {
        if($host === $local || $server === $local || substr($host, 0, strlen($local . ':')) === $local . ':')
        {
            return 'local';
        }
    }

    if(substr($host, -5) === '.test' || substr($host, -6) === '.local')
    {
        return 'local';
    }

    return 'remote';
}

function current_step(): int
{
    ensure_session();

    $step = (int) ($_GET['step'] ?? $_SESSION['install_wizard']['step'] ?? 1);
    return max(1, min(5, $step));
}

function set_step(int $step): void
{
    ensure_session();
    $_SESSION['install_wizard']['step'] = max(1, min(5, $step));
}

function go_step(int $step): void
{
    set_step($step);

    $target = 'install?step=' . $step;

    if(function_exists('redirect'))
    {
        redirect($target);
        exit;
    }

    header('Location: ' . root_url_path() . '/' . $target);
    exit;
}

function test_db_connection(array $data): array
{
    $driver = trim((string) ($data['db_driver'] ?? 'mysql')) ?: 'mysql';
    $host = trim((string) ($data['db_host'] ?? 'localhost')) ?: 'localhost';
    $port = trim((string) ($data['db_port'] ?? '3306')) ?: '3306';
    $name = trim((string) ($data['db_name'] ?? ''));
    $user = trim((string) ($data['db_user'] ?? ''));
    $pass = (string) ($data['db_password'] ?? '');

    if($name === '')
    {
        return [false, 'Database name is required.'];
    }

    try
    {
        if($driver === 'mysql')
        {
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        }
        elseif($driver === 'pgsql')
        {
            $dsn = "pgsql:host={$host};port={$port};dbname={$name}";
        }
        else
        {
            $dsn = "{$driver}:host={$host};port={$port};dbname={$name}";
        }

        $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            \PDO::ATTR_TIMEOUT => 5,
        ]);

        $pdo = null;
        return [true, 'Database connection successful.'];
    }
    catch(\Throwable $e)
    {
        return [false, 'Database connection failed: ' . $e->getMessage()];
    }
}

function app_logo_url(): string
{
    $path = assets_logo_path();

    if(!is_file($path))
    {
        return '';
    }

    return root_url_path() . '/assets/images/logo.jpg?v=' . filemtime($path);
}

function process_logo_upload(): array
{
    if(empty($_FILES['app_logo_file']) || ($_FILES['app_logo_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)
    {
        return [true, old('app_logo', '/assets/images/logo.jpg')];
    }

    $file = $_FILES['app_logo_file'];

    if(($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK)
    {
        return [false, 'Logo upload failed. PHP upload error: ' . (int) $file['error']];
    }

    $tmp = $file['tmp_name'] ?? '';
    $info = @getimagesize($tmp);

    if(!$info || empty($info['mime']))
    {
        return [false, 'The uploaded logo is not a valid image.'];
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if(!in_array($info['mime'], $allowed, true))
    {
        return [false, 'Logo must be JPG, PNG, GIF, or WEBP.'];
    }

    $dir = dirname(assets_logo_path());
    if(!is_dir($dir) && !mkdir($dir, 0775, true))
    {
        return [false, 'Could not create logo directory: ' . $dir];
    }

    if(!function_exists('imagecreatetruecolor'))
    {
        if(!move_uploaded_file($tmp, assets_logo_path()))
        {
            return [false, 'Could not save uploaded logo.'];
        }
        return [true, '/assets/images/logo.jpg'];
    }

    [$width, $height] = $info;
    $max = 512;
    $ratio = min($max / max($width, 1), $max / max($height, 1), 1);
    $new_w = max(1, (int) round($width * $ratio));
    $new_h = max(1, (int) round($height * $ratio));

    switch($info['mime'])
    {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($tmp);
            break;
        case 'image/png':
            $src = imagecreatefrompng($tmp);
            break;
        case 'image/gif':
            $src = imagecreatefromgif($tmp);
            break;
        case 'image/webp':
            $src = function_exists('imagecreatefromwebp') ? imagecreatefromwebp($tmp) : false;
            break;
        default:
            $src = false;
    }

    if(!$src)
    {
        return [false, 'Could not read the uploaded logo image.'];
    }

    $dst = imagecreatetruecolor($new_w, $new_h);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_w, $new_h, $width, $height);

    $saved = imagejpeg($dst, assets_logo_path(), 90);
    imagedestroy($src);
    imagedestroy($dst);

    if(!$saved)
    {
        return [false, 'Could not save resized logo.'];
    }

    return [true, '/assets/images/logo.jpg'];
}

function build_config(array $data): string
{
    $env = $data['environment'] ?? detect_environment();

    $local = [
        'LOCAL_ROOT' => $env === 'local' ? ($data['root_url'] ?? '') : '',
        'LOCAL_DB_NAME' => $env === 'local' ? ($data['db_name'] ?? '') : old('LOCAL_DB_NAME', 'pluginphp_db'),
        'LOCAL_DB_USER' => $env === 'local' ? ($data['db_user'] ?? '') : old('LOCAL_DB_USER', 'root'),
        'LOCAL_DB_PASSWORD' => $env === 'local' ? ($data['db_password'] ?? '') : old('LOCAL_DB_PASSWORD', ''),
        'LOCAL_DB_HOST' => $env === 'local' ? ($data['db_host'] ?? 'localhost') : old('LOCAL_DB_HOST', 'localhost'),
        'LOCAL_DB_DRIVER' => $env === 'local' ? ($data['db_driver'] ?? 'mysql') : old('LOCAL_DB_DRIVER', 'mysql'),
        'LOCAL_DB_PORT' => $env === 'local' ? ($data['db_port'] ?? '3306') : old('LOCAL_DB_PORT', '3306'),
    ];

    $remote = [
        'REMOTE_ROOT' => $env === 'remote' ? ($data['root_url'] ?? '') : '',
        'REMOTE_DB_NAME' => $env === 'remote' ? ($data['db_name'] ?? '') : old('REMOTE_DB_NAME', 'pluginphp_db'),
        'REMOTE_DB_USER' => $env === 'remote' ? ($data['db_user'] ?? '') : old('REMOTE_DB_USER', 'root'),
        'REMOTE_DB_PASSWORD' => $env === 'remote' ? ($data['db_password'] ?? '') : old('REMOTE_DB_PASSWORD', ''),
        'REMOTE_DB_HOST' => $env === 'remote' ? ($data['db_host'] ?? 'localhost') : old('REMOTE_DB_HOST', 'localhost'),
        'REMOTE_DB_DRIVER' => $env === 'remote' ? ($data['db_driver'] ?? 'mysql') : old('REMOTE_DB_DRIVER', 'mysql'),
        'REMOTE_DB_PORT' => $env === 'remote' ? ($data['db_port'] ?? '3306') : old('REMOTE_DB_PORT', '3306'),
    ];

    $values = array_merge([
        'USE_SESSIONS' => !empty($data['use_sessions']),
        'DEBUG' => !empty($data['debug']),
        'APP_NAME' => $data['app_name'] ?? 'Thunder PHP',
        'APP_DESCRIPTION' => $data['app_description'] ?? 'A plugin based PHP Framework',
        'APP_LOGO' => $data['app_logo'] ?? '/assets/images/logo.jpg',
    ], $local, $remote);

    $lines = [];
    $lines[] = '<?php';
    $lines[] = '';
    $lines[] = '/*';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' * ALLOW SESSIONS FOR USER LOGIN';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' */';
    $lines[] = "define('USE_SESSIONS', " . var_export((bool) $values['USE_SESSIONS'], true) . ');';
    $lines[] = '';
    $lines[] = '/*';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' * ACTIVATE OR DISABLE ERROR REPORTING';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' */';
    $lines[] = "define('DEBUG', " . var_export((bool) $values['DEBUG'], true) . ');';
    $lines[] = '';
    $lines[] = '/*';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' * WEBSITE NAME AND DESCRIPTION';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' */';
    $lines[] = "define('APP_NAME', " . var_export((string) $values['APP_NAME'], true) . ');';
    $lines[] = "define('APP_DESCRIPTION', " . var_export((string) $values['APP_DESCRIPTION'], true) . ');';
    $lines[] = "define('APP_LOGO', " . var_export((string) $values['APP_LOGO'], true) . ');';
    $lines[] = '';
    $lines[] = '/*';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' * SET ROOT PATH FOR WHEN RUNNING LOCALLY OR ONLINE SERVER';
    $lines[] = ' * e.g http://localhost/myfolder or https://mywebsite.com';
    $lines[] = ' * auto detected if left empty';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' */';
    $lines[] = "define('LOCAL_ROOT', " . var_export((string) $values['LOCAL_ROOT'], true) . ');';
    $lines[] = "define('REMOTE_ROOT', " . var_export((string) $values['REMOTE_ROOT'], true) . ');';
    $lines[] = '';
    $lines[] = '/*';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' * LOCAL DATABASE DETAILS';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' */';
    foreach(['LOCAL_DB_NAME', 'LOCAL_DB_USER', 'LOCAL_DB_PASSWORD', 'LOCAL_DB_HOST', 'LOCAL_DB_DRIVER', 'LOCAL_DB_PORT'] as $key)
    {
        $lines[] = "define('{$key}', " . var_export((string) $values[$key], true) . ');';
    }
    $lines[] = '';
    $lines[] = '/*';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' * REMOTE DATABASE DETAILS';
    $lines[] = ' *---------------------------------------------------------------';
    $lines[] = ' */';
    foreach(['REMOTE_DB_NAME', 'REMOTE_DB_USER', 'REMOTE_DB_PASSWORD', 'REMOTE_DB_HOST', 'REMOTE_DB_DRIVER', 'REMOTE_DB_PORT'] as $key)
    {
        $lines[] = "define('{$key}', " . var_export((string) $values[$key], true) . ');';
    }

    return implode(PHP_EOL, $lines) . PHP_EOL;
}

function write_config_file(array $data): array
{
    $root = root_path();

    if(is_file(config_path()))
    {
        return [false, 'config.php already exists. The installer will not overwrite it.'];
    }

    if(!is_file(sample_config_path()))
    {
        return [false, 'config-sample.php was not found in the main directory.'];
    }

    if(!is_writable($root))
    {
        return [false, 'The main directory is not writable. Please check folder permissions before continuing.'];
    }

    $content = build_config($data);
    $tmp = config_path() . '.tmp';

    if(file_put_contents($tmp, $content, LOCK_EX) === false)
    {
        return [false, 'Could not write temporary config file.'];
    }

    if(!rename($tmp, config_path()))
    {
        @unlink($tmp);
        return [false, 'Could not rename temporary config file to config.php.'];
    }

    return [true, 'config.php created successfully.'];
}

function run_migrations(): array
{
    $paths = [
        root_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'thunder' . DIRECTORY_SEPARATOR . 'Database.php',
        root_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Migration.php',
        root_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'thunder' . DIRECTORY_SEPARATOR . 'MigrationTracker.php',
        root_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'thunder' . DIRECTORY_SEPARATOR . 'thunder.php',
    ];

    try
    {
        foreach($paths as $path)
        {
            if(!is_file($path))
            {
                return [
                    'ok' => false,
                    'output' => 'Migration failed: required file not found: ' . $path,
                ];
            }

            require_once $path;
        }

        if(!class_exists('\\Thunder\\Thunder'))
        {
            return [
                'ok' => false,
                'output' => 'Migration failed: Thunder migration runner class was not found.',
            ];
        }

        $thunder = new \Thunder\Thunder;

        ob_start();
        $thunder->migrate([
            '0',
            'migrate',
            'all',
        ]);

        $output = trim((string) ob_get_clean());
        $lower_output = strtolower($output);

        return [
            'ok' => !str_contains($lower_output, 'no direct migration runner') && !str_contains($lower_output, 'fatal error'),
            'output' => $output !== '' ? $output : 'Migrations completed.',
        ];
    }
    catch(\Throwable $e)
    {
        if(ob_get_level() > 0)
        {
            ob_end_clean();
        }

        return [
            'ok' => false,
            'output' => 'Migration failed: ' . $e->getMessage(),
        ];
    }
}

function run_pending_migrations(): void
{
    ensure_session();

    if(empty($_SESSION['install_wizard']['pending_migrations']))
    {
        return;
    }

    if(!is_installed())
    {
        flash('fail', 'config.php must exist before migrations can run.');
        unset($_SESSION['install_wizard']['pending_migrations']);
        go_step(3);
    }

    $result = run_migrations();

    $_SESSION['install_wizard']['migration_result'] = $result;
    $_SESSION['install_wizard']['migrations_ran'] = true;
    unset($_SESSION['install_wizard']['pending_migrations']);

    flash($result['ok'] ? 'success' : 'fail', $result['ok'] ? 'Plugin migrations completed.' : 'Plugin migrations failed. Review the output below.');
}

function migration_result(): array
{
    ensure_session();

    return $_SESSION['install_wizard']['migration_result'] ?? [
        'ok' => null,
        'output' => '',
    ];
}

function disable_this_plugin(): bool
{
    $path = plugin_config_path();

    if(!is_file($path) || !is_writable($path))
    {
        return false;
    }

    $json = json_decode((string) file_get_contents($path));
    if(!is_object($json))
    {
        return false;
    }

    $json->active = false;

    return file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL, LOCK_EX) !== false;
}

function handle_request(): void
{
    ensure_session();

    if(is_installed() && !is_install_route())
    {
        return;
    }

    if(!is_installed() && !is_install_route())
    {
        redirect_to_install();
    }

    if(is_installed() && is_install_route() && empty($_SESSION['install_wizard']['installed_recently']))
    {
        redirect_to_login();
    }

    if(($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST')
    {
        if(current_step() === 4 && !empty($_SESSION['install_wizard']['pending_migrations']))
        {
            run_pending_migrations();
        }

        return;
    }

    if(!csrf_valid())
    {
        flash('fail', 'Security token expired. Please try again.');
        go_step(current_step());
    }

    $action = $_POST['action'] ?? '';

    if($action === 'step1')
    {
        go_step(2);
    }

    if($action === 'step2')
    {
        $env = $_POST['environment'] ?? detect_environment();
        $data = [
            'environment' => in_array($env, ['local', 'remote'], true) ? $env : detect_environment(),
            'root_url' => trim((string) ($_POST['root_url'] ?? '')),
            'db_name' => trim((string) ($_POST['db_name'] ?? '')),
            'db_user' => trim((string) ($_POST['db_user'] ?? '')),
            'db_password' => (string) ($_POST['db_password'] ?? ''),
            'db_host' => trim((string) ($_POST['db_host'] ?? 'localhost')) ?: 'localhost',
            'db_driver' => trim((string) ($_POST['db_driver'] ?? 'mysql')) ?: 'mysql',
            'db_port' => trim((string) ($_POST['db_port'] ?? '3306')) ?: '3306',
        ];

        save_data($data);

        [$ok, $msg] = test_db_connection($data);
        flash($ok ? 'success' : 'fail', $msg);

        if($ok)
        {
            go_step(3);
        }

        go_step(2);
    }

    if($action === 'step3')
    {
        [$logo_ok, $logo_result] = process_logo_upload();

        if(!$logo_ok)
        {
            flash('fail', $logo_result);
            go_step(3);
        }

        $data = [
            'app_name' => trim((string) ($_POST['app_name'] ?? 'Thunder PHP')) ?: 'Thunder PHP',
            'app_description' => trim((string) ($_POST['app_description'] ?? 'A plugin based PHP Framework')) ?: 'A plugin based PHP Framework',
            'app_logo' => $logo_result ?: '/assets/images/logo.jpg',
            'debug' => !empty($_POST['debug']),
            'use_sessions' => !empty($_POST['use_sessions']),
        ];

        save_data($data);

        [$ok, $msg] = write_config_file($_SESSION['install_wizard']['data'] ?? []);
        flash($ok ? 'success' : 'fail', $msg);

        if($ok)
        {
            $_SESSION['install_wizard']['installed_recently'] = true;
            $_SESSION['install_wizard']['pending_migrations'] = true;
            go_step(4);
        }

        go_step(3);
    }

    if($action === 'skip_migrations')
    {
        $_SESSION['install_wizard']['migration_result'] = [
            'ok' => null,
            'output' => 'Migrations were skipped during installation.',
        ];
        $_SESSION['install_wizard']['migrations_ran'] = false;
        unset($_SESSION['install_wizard']['pending_migrations']);
        flash('fail', 'Migrations were skipped. You can run them later from the CLI or plugin manager.');
        go_step(5);
    }

    if($action === 'rerun_migrations')
    {
        $_SESSION['install_wizard']['pending_migrations'] = true;
        go_step(4);
    }

    if($action === 'finish')
    {
        if(!empty($_POST['disable_wizard']))
        {
            if(disable_this_plugin())
            {
                flash('success', 'Install Wizard plugin disabled.');
            }
            else
            {
                flash('fail', 'The wizard could not disable itself. You can disable it later from the plugin manager.');
            }
        }

        unset($_SESSION['install_wizard']);
        redirect_to_login();
    }
}

function render_view(): void
{
    if(!is_install_route())
    {
        return;
    }

    ensure_session();

    $step = current_step();
    $detected_environment = detect_environment();
    $flash = get_flash();
    $token = csrf_token();
    $config_exists = is_installed();
    $root_writable = is_writable(root_path());
    $sample_exists = is_file(sample_config_path());
    $logo_url = app_logo_url();
    $migration_result = migration_result();

    include current_look('install.php');
    exit;
}

\add_action('before_controller', function($data){
    handle_request();
}, 1);

\add_action('view', function($data){
    render_view();
}, 1, 'install.index');
