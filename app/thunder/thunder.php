<?php

namespace Thunder;

defined('ROOTPATH') or die("Direct script access denied");

class Thunder
{
    private $VERSION = '1.0.0';
    private $output = '';
    private function color($text, $colorCode)
    {
        if(strpos(PHP_SAPI, 'cli') !== 0)
            return $text;

        return "\033[" . $colorCode . "m" . $text . "\033[0m";
    }
    
    private function bgcolor($text, $bg = '34', $fg = '37')
    {
        if(strpos(PHP_SAPI, 'cli') !== 0)
            return $text;

        $start = "\033[" . $fg;
        if ($bg) $start .= ";" . $bg;
        return $start . "m" . $text . "\033[0m";
    }
 
    public function help($args = [])
    {

        echo "\n";
        echo $this->bgcolor(" THUNDERPHP CLI v$this->VERSION - Available Commands ", "44")." \n";

        echo "----------------------------------\n";
        echo $this->bgcolor(" Generators  ", "44") . "\n";
        echo $this->color(" make:plugin <name> [--force]         ", "32") . ":Create a new plugin\n";
        echo $this->color("                                      ", "32") . " - Use --force to overwrite\n";
        echo $this->color(" make:controller <plugin> <name>      ", "32") . ":Create a controller in plugin\n";
        echo $this->color(" make:model <plugin> <name>           ", "32") . ":Create a model in plugin\n";
        echo $this->color(" make:migration <plugin> <name>       ", "32") . ":Create a migration file in plugin\n";
        echo $this->color(" make:pager <plugin> <name>           ", "32") . ":Create a pagination file in plugin\n";
        echo $this->color(" make:view <plugin> <name>            ", "32") . ":Create a view file in plugin\n";
        echo $this->color(" help                                 ", "32") . ":Show this help message\n";
        echo "----------------------------------\n";

        echo $this->bgcolor(" Migration   ", "44") . "\n";
        echo $this->color(" migrate <plugin|all> [filename]      ", "32") . ":Runs pending migrations from the specified plugin folder\n";
        echo $this->color("                                      ", "32") . " - 'all' runs pending migrations from all plugin folders\n";
        echo $this->color("                                      ", "32") . " - Adding the optional filename runs that one migration if pending\n";
        echo $this->color(" migrate:rollback <plugin|all>        ", "32") . ":Rolls back the latest migration batch\n";
        echo $this->color(" migrate:refresh  <plugin> [filename] ", "32") . ":Runs a rollback followed by a migrate command\n";
        echo $this->color(" migrate:status [plugin|all]          ", "32") . ":Shows ran and pending migrations\n";
        echo "\n";

        echo "----------------------------------\n";

        echo $this->bgcolor(" Development ", "44") . "\n";
        echo $this->color(" serve [--host=127.0.0.1] [--port=8000]", "32") . ":Start the PHP development server\n";
        echo $this->color("                                      ", "32") . " - Example: php thunder serve --host=0.0.0.0 --port=8080\n";
        echo "\n";

        echo "----------------------------------\n";

        echo $this->bgcolor(" Info        ", "44") . "\n";
        echo $this->color(" list:plugins                         ", "32") . ":List all installed plugins\n";
        echo "\n";

        
    }

    public function make(array $args)
    {
        $action = $args[1] ?? '';
        $param1 = $args[2] ?? '';
        $param2 = $args[3] ?? '';
        $force  = in_array('--force', $args);

        if (!$action) {
            return $this->help();
        }

        switch ($action) {
            case 'make:plugin':
                $this->makePlugin($param1, $force);
                break;
            case 'make:controller':
                $this->makeFile($param1, 'controllers', $param2, 'controller-sample.php');
                break;
            case 'make:model':
                $this->makeFile($param1, 'models', $param2, 'model-sample.php');
                break;
            case 'make:pager':
                $this->makeFile($param1, 'models', $param2, 'pager-sample.php');
                break;
            case 'make:migration':
                $this->makeFile($param1, 'migrations', $param2, 'migration-sample.php');
                break;
            case 'make:validator':
                $this->makeFile($param1, 'models', $param2, 'validator-sample.php');
                break;
            case 'make:view':
                $this->makeFile($param1, 'looks', $param2, 'view-sample.php');
                break;
            default:
                echo "\n";
                echo $this->bgcolor(" UNKNOWN COMMAND: $action ", "41")."\n";
                $this->help();
                break;
        }
    }

    private function makePlugin(string $name, bool $force = false)
    {
        echo "\n";
        if (!$name) {
            echo $this->color("\nPlease provide a plugin name.", "33")."\n";
            return;
        }

        $base = "plugins/$name";

        if (file_exists($base)) {
            if ($force) {
                echo $this->color("Overwriting existing plugin: $name", "33")."\n";
            } else {
                echo $this->color("That plugin already exists. Use --force to overwrite.", "33")."\n";
                return;
            }
        }

        @mkdir($base, 0777, true);
        foreach (['looks/main/assets/css', 'looks/main/assets/js', 'looks/main/assets/fonts', 'looks/main/assets/images', 'controllers', 'looks', 'migrations', 'models'] as $sub) {
            @mkdir("$base/$sub", 0777, true);
        }

        $notFound = [];
        $sampleFiles = [
            'php'=>[
                'plugin'=>'',
                'controller'=>'controllers',
                'home'=>'looks/main',
            ],
            'json'=>[
                'config'=>'',
                'look'=>'looks/main',
            ],
            'js'=>[
                'plugin'=>'looks/main/assets/js',
            ],
            'png'=>[
                'thumbnail'=>'',
            ],
            'css'=>[
                'style'=>'looks/main/assets/css',
            ],

        ];

        foreach ($sampleFiles as $type => $filesArr) {
            
            foreach ($filesArr as $myFile => $myFolder) {
                
                $sample = FCPATH . "app/thunder/samples/$myFile-sample.".$type;
                $target = "$base/".$myFolder."/$myFile.$type";
                if (file_exists($sample)) {
                    copy($sample, $target);

                    //replace placeholders if any
                    $conte = file_get_contents($target);
                    $namespace = str_replace("-", " ", $name);
                    $namespace = ucwords($namespace);
                    $namespace = str_replace(" ", "", $namespace);
                    $conte = str_replace("{NAMESPACE}", $namespace, $conte);
                    file_put_contents($target, $conte);

                }else{
                    $notFound[] = $sample;
                }
            }
        }


        echo "\n";
        if(!empty($notFound))
        {
            echo $this->bgcolor(" MISSING FILES: ", "41")."\n";
            foreach ($notFound as $file) {
                
                echo $this->color(" $file ", "33")."\n";
            }
            echo "\n";
        }

        echo $this->bgcolor(" PLUGIN CREATED: $name ", "42")."\n";
    }

    private function makeFile(string $plugin, string $folder, string $name, string $sampleFile)
    {
        if (!$plugin || !$name) {
            echo "\n" . $this->color("Usage: make:$folder <plugin> <name>", "33")."\n";
            return;
        }

        $base = "plugins/$plugin/$folder/";
        if(!file_exists($base))
            @mkdir($base, 0777, true);

        $subfolder = dirname($name);
        if($subfolder != '.')
        {
            $base .= $subfolder . '/'; 
            if(!file_exists($base))
                @mkdir($base, 0777, true);

            $name = basename($name);
        }

        $filename = $base.$name.".php";
        if($folder == 'models')
            $filename = $base.ucfirst($name).".php";

        if($folder == 'migrations')
            $filename = $base . date("Y-m-d_His_") . $name . '.php';

        if (file_exists($filename)) {
            echo $this->color("File already exists: $filename", "33")."\n";
            return;
        }

        $stub = "<?php\n\n// $folder: $name\n\n";
        $sampleFile = __DIR__."/samples/".$sampleFile;

        if(file_exists($sampleFile))
        {
            $stub = file_get_contents($sampleFile);
           
            $class_name = preg_replace("/[^a-zA-Z_\-]/", "", $name);
            $class_name = str_replace("-", "_", $class_name);
            $class_name = ucfirst($class_name);

            $table_name = strtolower($class_name);
            $stub = str_replace("{TABLE_NAME}", $table_name, $stub);
            $stub = str_replace("{CLASS_NAME}", $class_name, $stub);

            $namespace = str_replace("-", " ", $plugin);
            $namespace = ucwords($namespace);
            $namespace = str_replace(" ", "", $namespace);
            $stub = str_replace("{NAMESPACE}", $namespace, $stub);

        }

        file_put_contents($filename, $stub);
        echo "\n".$this->bgcolor(strtoupper($folder) . " CREATED: $filename", "42")."\n";
    }

    public function list(array $args = [])
    {
        echo "\n" .$this->color("INSTALLED PLUGINS:", "34")."\n";
        echo "------------------\n";

        $pluginDirs = glob("plugins/*", GLOB_ONLYDIR);
        foreach ($pluginDirs as $dir) {
            $configPath = $dir . "/config.json";
            if (!file_exists($configPath)) continue;

            $meta = json_decode(file_get_contents($configPath), true);
            if (!$meta || empty($meta['name'])) continue;

            echo $this->color("Name: ", "32") . $meta['name'] . "\n";
            echo $this->color("ID: ", "36") . $meta['id'] . "\n";
            echo $this->color("Author: ", "33") . ($meta['author'] ?? 'Unknown') . "\n";
            echo $this->color("Version: ", "33") . ($meta['version'] ?? '1.0') . "\n";
            echo $this->color("Status: ", $meta['active'] ? "32" : "31") . ($meta['active'] ? 'Active' : 'Inactive') . "\n\n";
        }
    }

    /**
     * Start PHP's built-in development server.
     *
     * Usage:
     *   php thunder serve
     *   php thunder serve --host=0.0.0.0 --port=8080
     *   php thunder serve --host 0.0.0.0 --port 8080
     */
    public function serve(array $args = []): void
    {
        $options = $this->parseServeOptions($args);

        if ($options['help']) {
            echo "\n";
            echo $this->bgcolor(" THUNDERPHP DEVELOPMENT SERVER ", "44") . "\n";
            echo $this->color(" php thunder serve", "32") . "\n";
            echo $this->color(" php thunder serve --host=127.0.0.1 --port=8000", "32") . "\n";
            echo $this->color(" php thunder serve --host 0.0.0.0 --port 8080", "32") . "\n\n";
            return;
        }

        $host = $options['host'];
        $port = $options['port'];

        if (!$this->isValidServeHost($host)) {
            echo "\n" . $this->color("Invalid host: {$host}", "31") . "\n";
            echo $this->color("Use an IP address or hostname such as 127.0.0.1, localhost, or 0.0.0.0.", "33") . "\n";
            return;
        }

        if ($port < 1 || $port > 65535) {
            echo "\n" . $this->color("Invalid port: {$port}. Use a value from 1 to 65535.", "31") . "\n";
            return;
        }

        $router = FCPATH . 'app' . DIRECTORY_SEPARATOR . 'thunder' . DIRECTORY_SEPARATOR . 'server.php';
        if (!is_file($router)) {
            echo "\n" . $this->color("Development server router not found: {$router}", "31") . "\n";
            return;
        }

        $documentRoot = rtrim(FCPATH, DIRECTORY_SEPARATOR);
        $listenHost = $this->formatListenHost($host);
        $address = $listenHost . ':' . $port;
        $displayHost = in_array($host, ['0.0.0.0', '::'], true) ? '127.0.0.1' : $this->formatUrlHost($host);
        $url = 'http://' . $displayHost . ':' . $port;

        echo "\n";
        echo $this->bgcolor(" THUNDERPHP DEVELOPMENT SERVER ", "44") . "\n";
        echo $this->color("Application: ", "36") . $url . "\n";
        echo $this->color("Listening:   ", "36") . $address . "\n";
        echo $this->color("Document root:", "36") . " " . $documentRoot . "\n";
        if (defined('LOCAL_ROOT') && LOCAL_ROOT !== '') {
            echo $this->color("Note: LOCAL_ROOT is set to " . LOCAL_ROOT . ". Generated URLs will continue using that value.", "33") . "\n";
        }

        echo $this->color("Press Ctrl+C to stop the server.", "33") . "\n\n";

        // The root CLI script starts an output buffer. End it before launching
        // the long-running server so the startup message and request log appear
        // immediately instead of waiting until the process exits.
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        flush();

        $command = escapeshellarg(PHP_BINARY)
            . ' -S ' . escapeshellarg($address)
            . ' -t ' . escapeshellarg($documentRoot)
            . ' ' . escapeshellarg($router);

        passthru($command, $exitCode);

        if ($exitCode !== 0) {
            echo "\n" . $this->color("The development server stopped with exit code {$exitCode}.", "31") . "\n";
        }
    }

    /**
     * @return array{host:string, port:int, help:bool}
     */
    private function parseServeOptions(array $args): array
    {
        $host = '127.0.0.1';
        $port = 8000;
        $help = false;
        $count = count($args);

        for ($i = 2; $i < $count; $i++) {
            $argument = (string)$args[$i];

            if ($argument === '--help' || $argument === '-h') {
                $help = true;
                continue;
            }

            if (str_starts_with($argument, '--host=')) {
                $host = trim(substr($argument, 7));
                continue;
            }

            if ($argument === '--host' && isset($args[$i + 1])) {
                $host = trim((string)$args[++$i]);
                continue;
            }

            if (str_starts_with($argument, '--port=')) {
                $portValue = trim(substr($argument, 7));
                $port = ctype_digit($portValue) ? (int)$portValue : 0;
                continue;
            }

            if ($argument === '--port' && isset($args[$i + 1])) {
                $portValue = trim((string)$args[++$i]);
                $port = ctype_digit($portValue) ? (int)$portValue : 0;
                continue;
            }

            echo $this->color("Ignoring unknown serve option: {$argument}", "33") . "\n";
        }

        $host = trim($host, "[] \t\n\r\0\x0B");

        return [
            'host' => $host,
            'port' => $port,
            'help' => $help,
        ];
    }

    private function isValidServeHost(string $host): bool
    {
        if ($host === '') {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return true;
        }

        return (bool)preg_match('/^(?=.{1,253}$)(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)(?:\.(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?))*$/', $host);
    }

    private function formatListenHost(string $host): string
    {
        return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false
            ? '[' . $host . ']'
            : $host;
    }

    private function formatUrlHost(string $host): string
    {
        return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false
            ? '[' . $host . ']'
            : $host;
    }

    public function validate(array $args = [])
    {

        echo "\n" . $this->color("PLUGIN VALIDATION REPORT", "34")."\n";
        echo "-------------------------\n";

        $pluginDirs = glob("plugins/*", GLOB_ONLYDIR);
        $valid = 0;
        $invalid = 0;

        foreach ($pluginDirs as $dir) {
            $name = basename($dir);
            $configPath = $dir . "/config.json";

            if (!file_exists($configPath)) {
                echo $this->color("❌ $name — missing config.json", "31")."\n";
                $invalid++;
                continue;
            }

            $json = file_get_contents($configPath);
            $meta = json_decode($json, true);

            if (!$meta) {
                echo $this->color("❌ $name — malformed JSON", "31")."\n";
                $invalid++;
                continue;
            }

            $required = ['name', 'id', 'routes', 'version'];
            $missing = array_filter($required, fn($key) => empty($meta[$key]));

            if (!empty($missing)) {
                echo $this->color("❌ $name — missing fields: " . implode(', ', $missing), "31")."\n";
                $invalid++;
                continue;
            }

            echo $this->color("✅ $name — valid", "32")."\n";
            $valid++;
        }

        echo "\n" . $this->color("SUMMARY: $valid valid, $invalid invalid", $invalid ? "33" : "32")."\n";
    }


    public function migrate(array $args = [])
    {
        $this->output = '';

        $action = $args[1] ?? '';
        $plugin = $args[2] ?? '';
        $filename = $args[3] ?? '';

        if (!$action) {
            return $this->help();
        }

        switch ($action) {
            case 'migrate':
                $this->migration($plugin, $filename, 'up');
                break;
            case 'migrate:rollback':
                $this->migration($plugin, $filename, 'down');
                break;
            case 'migrate:refresh':
                $this->migrate(['thunder','migrate:rollback',$plugin,$filename]);
                $this->migrate(['thunder','migrate',$plugin,$filename]);
                break;
            case 'migrate:status':
                $this->migrationStatus($plugin ?: 'all');
                break;
 
            default:
                echo "\n";
                echo $this->bgcolor(" UNKNOWN COMMAND: $action ", "41")."\n";
                $this->help();
                break;
        }

    }

    private function migration(string $folder, string $file_name, string $type): void
    {
        $tracker = new MigrationTracker();
        $tracker->ensureTable();

        $folders = $this->resolvePluginFolders($folder ?: 'all');

        if ($type === 'up') {
            $globalBatch = $tracker->getNextBatch();
        }

        foreach ($folders as $pluginFolder) {
            $pluginId = basename($pluginFolder);
            $migrationFolder = rtrim($pluginFolder, '/') . '/migrations/';

            if (!is_dir($migrationFolder)) {
                echo $this->color("❌ No migration files found in this location: $migrationFolder", "31")."\n";
                continue;
            }

            $files = [];

            if (!empty($file_name)) {
                $fullPath = $migrationFolder . $file_name;
                if (!file_exists($fullPath)) {
                    echo $this->color("❌ Migration file not found: $fullPath", "31")."\n";
                    continue;
                }
                $files[] = $fullPath;
            } else {
                if ($type === 'up') {
                    $files = glob($migrationFolder . '*.php') ?: [];
                    sort($files);
                } else {
                    $runs = $tracker->getLastBatchMigrations($folder === 'all' ? null : $pluginId);
                    foreach ($runs as $run) {
                        if ($folder !== 'all' && $run->plugin_id !== $pluginId) {
                            continue;
                        }
                        $fullPath = $pluginFolder . '/migrations/' . $run->migration_name;
                        if (file_exists($fullPath)) {
                            $files[] = $fullPath;
                        }
                    }
                }
            }

            if (empty($files)) {
                echo "\n" . $this->color("❌ No migration files to process for $pluginId", "31") . "\n";
                continue;
            }

            if ($type === 'down') {
                rsort($files);
            }

            foreach ($files as $file) {
                $migrationName = basename($file);

                if ($type === 'up' && $tracker->hasRun($pluginId, $migrationName)) {
                    echo $this->color("⏭ Skipping already-ran migration: $migrationName", "33") . "\n";
                    continue;
                }

                echo $this->color("MIGRATING FILE: $file", "32") . "\n";

                require_once $file;

                $className = $this->resolveMigrationClassName($file);
                if (!$className || !class_exists($className, false)) {
                    echo $this->color("❌ Could not resolve migration class for: $migrationName", "31") . "\n";
                    continue;
                }

                $instance = new $className();

                try {
                    if ($type === 'up') {
                        $instance->up();
                        $tracker->logRun($pluginId, $migrationName, $globalBatch, hash_file('sha256', $file));
                    } else {
                        if (!$tracker->hasRun($pluginId, $migrationName)) {
                            echo $this->color("⏭ Skipping not-yet-ran migration: $migrationName", "33") . "\n";
                            continue;
                        }
                        $instance->down();
                        $tracker->removeRun($pluginId, $migrationName);
                    }

                    echo $this->color("\n✅ Done: $migrationName", "32") . "\n";
                } catch (\Throwable $e) {
                    echo $this->color("❌ Migration failed: $migrationName", "31") . "\n";
                    echo $this->color($e->getMessage(), "31") . "\n";
                    break;
                }
            }
        }
    }

    private function migrationStatus(string $folder = 'all'): void
    {
        $tracker = new MigrationTracker();
        $tracker->ensureTable();

        $folders = $this->resolvePluginFolders($folder ?: 'all');

        echo "\n" . $this->bgcolor(" MIGRATION STATUS ", "44") . "\n";

        foreach ($folders as $pluginFolder) {
            $pluginId = basename($pluginFolder);
            $migrationFolder = rtrim($pluginFolder, '/') . '/migrations/';

            echo "\n" . $this->color("Plugin: $pluginId", "36") . "\n";

            if (!is_dir($migrationFolder)) {
                echo $this->color("  No migrations folder", "33") . "\n";
                continue;
            }

            $files = glob($migrationFolder . '*.php') ?: [];
            sort($files);
            $runs = [];
            foreach ($tracker->allRuns($pluginId) as $run) {
                $runs[$run->migration_name] = $run;
            }

            if (empty($files)) {
                echo $this->color("  No migration files", "33") . "\n";
                continue;
            }

            foreach ($files as $file) {
                $name = basename($file);
                if (isset($runs[$name])) {
                    $run = $runs[$name];
                    echo $this->color("  [RAN]    ", "32") . $name . "  batch=" . $run->batch . "  ran_at=" . $run->ran_at . "\n";
                } else {
                    echo $this->color("  [PENDING]", "33") . " " . $name . "\n";
                }
            }
        }

        echo "\n";
    }

    private function resolvePluginFolders(string $folder): array
    {
        if ($folder === 'all') {
            return glob('plugins/*', GLOB_ONLYDIR) ?: [];
        }

        return ['plugins/' . $folder];
    }

    private function resolveMigrationClassName(string $file): string
    {
        $className = basename($file, '.php');
        $className = preg_replace('/^\d{4}-\d{2}-\d{2}_\d{6}_/', '', $className);
        $className = preg_replace('/[^a-zA-Z0-9_\-]/', '', $className);
        $className = str_replace('-', '_', $className);
        $className = ucfirst(trim($className, '_'));

        return "\\Migration\\{$className}";
    }
}
