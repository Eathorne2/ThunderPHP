<?php

/**
 * This file is part of the ThunderPHP Framework.
 * This file contains the class autoloader and includes essential files for the application.
 *
 * @package ThunderPHP
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/*
 *---------------------------------------------------------------
 * SET WHETHER TO DISPLAY ERRORS OR NOT
 *---------------------------------------------------------------
 */
ini_set('display_errors', DEBUG ? '1' : '0');

/*
 *---------------------------------------------------------------
 * VERBOSE DEBUG ERROR HANDLING
 *---------------------------------------------------------------
 * These helpers intentionally do not depend on the framework being fully
 * bootstrapped. This allows the error page to render even when bootstrapping,
 * autoloading, configuration, or database setup fails.
 */

if (!function_exists('thunder_debug_error_type')) {
    function thunder_debug_error_type($errno)
    {
        $types = [
            E_ERROR             => ['title' => 'ERROR',      'type' => 'Fatal Error',              'severity' => 'Critical'],
            E_WARNING           => ['title' => 'WARNING',    'type' => 'Runtime Warning',          'severity' => 'Warning'],
            E_PARSE             => ['title' => 'PARSE ERROR','type' => 'Parse Error',               'severity' => 'Critical'],
            E_NOTICE            => ['title' => 'NOTICE',     'type' => 'Runtime Notice',           'severity' => 'Notice'],
            E_CORE_ERROR        => ['title' => 'CORE ERROR', 'type' => 'PHP Core Error',           'severity' => 'Critical'],
            E_CORE_WARNING      => ['title' => 'CORE WARNING','type' => 'PHP Core Warning',        'severity' => 'Warning'],
            E_COMPILE_ERROR     => ['title' => 'COMPILE ERROR','type' => 'Compile Error',          'severity' => 'Critical'],
            E_COMPILE_WARNING   => ['title' => 'COMPILE WARNING','type' => 'Compile Warning',      'severity' => 'Warning'],
            E_USER_ERROR        => ['title' => 'ERROR',      'type' => 'User-Generated Error',      'severity' => 'Critical'],
            E_USER_WARNING      => ['title' => 'WARNING',    'type' => 'User-Generated Warning',    'severity' => 'Warning'],
            E_USER_NOTICE       => ['title' => 'NOTICE',     'type' => 'User-Generated Notice',     'severity' => 'Notice'],
            E_STRICT            => ['title' => 'STRICT',     'type' => 'Strict Standards Notice',   'severity' => 'Notice'],
            E_RECOVERABLE_ERROR => ['title' => 'ERROR',      'type' => 'Recoverable Fatal Error',   'severity' => 'Critical'],
            E_DEPRECATED        => ['title' => 'DEPRECATED', 'type' => 'Deprecated Feature',        'severity' => 'Deprecated'],
            E_USER_DEPRECATED   => ['title' => 'DEPRECATED', 'type' => 'User Deprecated Feature',   'severity' => 'Deprecated'],
        ];

        return isset($types[$errno])
            ? $types[$errno]
            : ['title' => 'UNKNOWN', 'type' => 'Unknown Error Type', 'severity' => 'Unknown'];
    }
}

if (!function_exists('thunder_debug_is_sensitive_key')) {
    function thunder_debug_is_sensitive_key($key)
    {
        return (bool) preg_match(
            '/(?:password|passwd|pwd|secret|token|api[_-]?key|authorization|cookie|set[_-]?cookie|csrf|session[_-]?id|private[_-]?key|client[_-]?secret|db[_-]?password)/i',
            (string) $key
        );
    }
}

if (!function_exists('thunder_debug_sanitize')) {
    function thunder_debug_sanitize($value, $depth = 0)
    {
        if ($depth > 6) {
            return '[maximum depth reached]';
        }

        if (is_array($value)) {
            $clean = [];
            $count = 0;

            foreach ($value as $key => $item) {
                if ($count >= 250) {
                    $clean['__truncated__'] = 'Additional entries were omitted.';
                    break;
                }

                $clean[$key] = thunder_debug_is_sensitive_key($key)
                    ? '[REDACTED]'
                    : thunder_debug_sanitize($item, $depth + 1);
                $count++;
            }

            return $clean;
        }

        if (is_object($value)) {
            $properties = [];

            try {
                $properties = get_object_vars($value);
            } catch (Throwable $exception) {
                $properties = ['__error__' => 'Object properties could not be read.'];
            }

            return [
                '__class__' => get_class($value),
                '__properties__' => thunder_debug_sanitize($properties, $depth + 1),
            ];
        }

        if (is_resource($value)) {
            return '[resource: ' . get_resource_type($value) . ']';
        }

        if (is_string($value)) {
            $maximum = 20000;
            if (strlen($value) > $maximum) {
                return substr($value, 0, $maximum) . "\n...[truncated]";
            }
        }

        return $value;
    }
}

if (!function_exists('thunder_debug_request_id')) {
    function thunder_debug_request_id()
    {
        $incoming = isset($_SERVER['HTTP_X_REQUEST_ID']) ? trim((string) $_SERVER['HTTP_X_REQUEST_ID']) : '';

        if ($incoming !== '' && preg_match('/^[A-Za-z0-9._:-]{1,100}$/', $incoming)) {
            return $incoming;
        }

        try {
            return bin2hex(random_bytes(8));
        } catch (Throwable $exception) {
            return str_replace('.', '', uniqid('req_', true));
        }
    }
}

if (!function_exists('thunder_debug_request_headers')) {
    function thunder_debug_request_headers()
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$name] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true)) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                $headers[$name] = $value;
            }
        }

        return thunder_debug_sanitize($headers);
    }
}

if (!function_exists('thunder_debug_request_url')) {
    function thunder_debug_request_url()
    {
        $https = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
        $scheme = $https ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

        return $scheme . '://' . $host . $uri;
    }
}

if (!function_exists('thunder_debug_first_value')) {
    function thunder_debug_first_value($source, array $paths, $fallback = 'Not available')
    {
        foreach ($paths as $path) {
            $value = $source;
            $found = true;

            foreach (explode('.', $path) as $segment) {
                if (is_array($value) && array_key_exists($segment, $value)) {
                    $value = $value[$segment];
                } elseif (is_object($value) && isset($value->{$segment})) {
                    $value = $value->{$segment};
                } else {
                    $found = false;
                    break;
                }
            }

            if ($found && $value !== null && $value !== '') {
                return thunder_debug_sanitize($value);
            }
        }

        return $fallback;
    }
}

if (!function_exists('thunder_debug_source_excerpt')) {
    function thunder_debug_source_excerpt($file, $line, $padding = 6)
    {
        if (!$file || !$line || !is_readable($file)) {
            return [];
        }

        $lines = @file($file, FILE_IGNORE_NEW_LINES);
        if (!is_array($lines)) {
            return [];
        }

        $start = max(1, (int) $line - $padding);
        $end = min(count($lines), (int) $line + $padding);
        $excerpt = [];

        for ($number = $start; $number <= $end; $number++) {
            $excerpt[] = [
                'number' => $number,
                'code' => $lines[$number - 1],
                'is_error' => $number === (int) $line,
            ];
        }

        return $excerpt;
    }
}

if (!function_exists('thunder_debug_normalize_trace')) {
    function thunder_debug_normalize_trace(array $trace)
    {
        $normalized = [];

        foreach ($trace as $frame) {
            $function = isset($frame['function']) ? (string) $frame['function'] : '';
            if (strpos($function, 'thunder_debug_') === 0) {
                continue;
            }

            $normalized[] = [
                'file' => isset($frame['file']) ? $frame['file'] : '[internal function]',
                'line' => isset($frame['line']) ? $frame['line'] : null,
                'class' => isset($frame['class']) ? $frame['class'] : '',
                'type' => isset($frame['type']) ? $frame['type'] : '',
                'function' => $function !== '' ? $function : '[unknown]',
                'args' => isset($frame['args']) ? thunder_debug_sanitize($frame['args']) : [],
            ];

            if (count($normalized) >= 60) {
                break;
            }
        }

        return $normalized;
    }
}

if (!function_exists('thunder_debug_split_fatal_message')) {
    function thunder_debug_split_fatal_message($message)
    {
        $parts = preg_split('/\nStack trace:\s*\n?/i', (string) $message, 2);
        $cleanMessage = trim($parts[0]);
        $traceLines = [];

        if (isset($parts[1])) {
            $rawLines = preg_split('/\r\n|\r|\n/', $parts[1]);
            foreach ($rawLines as $line) {
                $line = trim($line);
                if ($line === '' || preg_match('/^thrown in\s/i', $line)) {
                    continue;
                }
                $traceLines[] = $line;
            }
        }

        return [$cleanMessage, $traceLines];
    }
}

if (!function_exists('thunder_debug_collect_context')) {
    function thunder_debug_collect_context()
    {
        $app = isset($GLOBALS['APP']) ? $GLOBALS['APP'] : [];
        $session = session_status() === PHP_SESSION_ACTIVE
            ? (isset($_SESSION) ? $_SESSION : [])
            : ['__status__' => 'No active PHP session'];

        $rawBody = '';
        $contentType = isset($_SERVER['CONTENT_TYPE']) ? strtolower((string) $_SERVER['CONTENT_TYPE']) : '';
        if (strpos($contentType, 'application/json') !== false || strpos($contentType, 'text/') === 0) {
            $rawBody = @file_get_contents('php://input');
        }

        $input = [
            'GET' => isset($_GET) ? $_GET : [],
            'POST' => isset($_POST) ? $_POST : [],
            'FILES' => isset($_FILES) ? $_FILES : [],
        ];

        if ($rawBody !== false && $rawBody !== '') {
            $decoded = json_decode($rawBody, true);
            $input['RAW_BODY'] = json_last_error() === JSON_ERROR_NONE ? $decoded : $rawBody;
        }

        $environment = [];
        $getenv = @getenv();
        if (is_array($getenv)) {
            $environment = $getenv;
        }
        if (!empty($_ENV) && is_array($_ENV)) {
            $environment = array_merge($environment, $_ENV);
        }

        $currentRoute = thunder_debug_first_value(
            $app,
            ['current_route', 'route.name', 'route.path', 'request.route', 'context.route'],
            isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'Not available'
        );

        $controller = thunder_debug_first_value(
            $app,
            ['controller', 'current_controller', 'route.controller', 'context.controller'],
            'Not available'
        );

        $action = thunder_debug_first_value(
            $app,
            ['action', 'current_action', 'route.action', 'route.method', 'context.action'],
            'Not available'
        );

        $userInfo = thunder_debug_first_value(
            $session,
            ['user', 'auth.user', 'logged_in_user', 'user_id', 'id'],
            thunder_debug_first_value($app, ['user', 'auth.user', 'current_user'], 'Not available')
        );

        return [
            'request_id' => thunder_debug_request_id(),
            'request' => [
                'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : PHP_SAPI,
                'url' => thunder_debug_request_url(),
                'ip_address' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Not available',
                'forwarded_for' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : 'Not available',
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Not available',
                'referrer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Not available',
                'query_string' => isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? $_SERVER['QUERY_STRING'] : 'None',
                'headers' => thunder_debug_request_headers(),
            ],
            'input' => thunder_debug_sanitize($input),
            'session' => thunder_debug_sanitize($session),
            'environment' => thunder_debug_sanitize($environment),
            'application' => [
                'current_route' => $currentRoute,
                'controller' => $controller,
                'action' => $action,
                'user_info' => $userInfo,
                'app_state' => thunder_debug_sanitize($app),
                'matched_routes' => thunder_debug_sanitize(
                    is_array($app) && isset($app['matched_routes']) ? $app['matched_routes'] : []
                ),
                'hook_runtime' => thunder_debug_sanitize(
                    is_array($app) && isset($app['hook_runtime']) ? $app['hook_runtime'] : []
                ),
            ],
            'extra' => [
                'php_sapi' => PHP_SAPI,
                'php_binary' => defined('PHP_BINARY') ? PHP_BINARY : 'Not available',
                'operating_system' => PHP_OS,
                'operating_system_family' => defined('PHP_OS_FAMILY') ? PHP_OS_FAMILY : PHP_OS,
                'server_software' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Not available',
                'document_root' => isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'Not available',
                'script_filename' => isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : 'Not available',
                'working_directory' => @getcwd(),
                'timezone' => @date_default_timezone_get(),
                'memory_usage_bytes' => memory_get_usage(true),
                'peak_memory_usage_bytes' => memory_get_peak_usage(true),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'error_reporting' => error_reporting(),
                'display_errors' => ini_get('display_errors'),
                'included_files' => get_included_files(),
                'loaded_extensions' => get_loaded_extensions(),
            ],
        ];
    }
}

if (!function_exists('thunder_debug_render')) {
    function thunder_debug_render(array $error)
    {
        if (!empty($GLOBALS['THUNDER_ERROR_RENDERED'])) {
            return;
        }

        $GLOBALS['THUNDER_ERROR_RENDERED'] = true;
        $GLOBALS['THUNDER_ERROR_HANDLING'] = true;

        $context = thunder_debug_collect_context();

        $errno = isset($error['type']) ? $error['type'] : E_ERROR;
        $errstr = isset($error['message']) ? $error['message'] : 'Unknown application error';
        $errfile = isset($error['file']) ? $error['file'] : 'Unknown file';
        $errline = isset($error['line']) ? $error['line'] : 0;
        $error_type = isset($error['classification'])
            ? $error['classification']
            : thunder_debug_error_type($errno);
        $error_code = isset($error['code']) ? $error['code'] : $errno;
        $trace = thunder_debug_normalize_trace(isset($error['trace']) && is_array($error['trace']) ? $error['trace'] : []);
        $fatal_trace_lines = isset($error['fatal_trace_lines']) && is_array($error['fatal_trace_lines'])
            ? $error['fatal_trace_lines']
            : [];
        $source_excerpt = thunder_debug_source_excerpt($errfile, $errline);
        $request_id = $context['request_id'];
        $error_timestamp = time();
        $request_details = $context['request'];
        $input_data = $context['input'];
        $session_data = $context['session'];
        $env_data = $context['environment'];
        $extra_context = $context['extra'];
        $application_state = $context['application'];
        $matched_routes = $context['application']['matched_routes'];
        $hook_runtime = $context['application']['hook_runtime'];
        $http_status = 500;

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        if (!headers_sent()) {
            http_response_code($http_status);
            header('Content-Type: text/html; charset=UTF-8');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('X-Request-ID: ' . $request_id);
        }

        require __DIR__ . DIRECTORY_SEPARATOR . 'error.php';
        exit(1);
    }
}

if (DEBUG) {
    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
        if (!empty($GLOBALS['THUNDER_ERROR_HANDLING'])) {
            return false;
        }

        // Respect @-suppressed errors and the configured error_reporting mask.
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        array_shift($trace);

        thunder_debug_render([
            'type' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'trace' => $trace,
        ]);

        return true;
    });

    set_exception_handler(function ($exception) {
        if (!empty($GLOBALS['THUNDER_ERROR_HANDLING'])) {
            return;
        }

        $hookExceptionSnapshot = null;

        if (is_object($exception) && function_exists('spl_object_id')) {
            $exceptionId = spl_object_id($exception);
            if (!empty($GLOBALS['THUNDER_HOOK_EXCEPTION_SNAPSHOTS'][$exceptionId])) {
				$hookExceptionSnapshot = $GLOBALS['THUNDER_HOOK_EXCEPTION_SNAPSHOTS'][$exceptionId];
                if (!isset($GLOBALS['APP']) || !is_array($GLOBALS['APP'])) {
                    $GLOBALS['APP'] = [];
                }
                if (!isset($GLOBALS['APP']['hook_runtime']) || !is_array($GLOBALS['APP']['hook_runtime'])) {
                    $GLOBALS['APP']['hook_runtime'] = [];
                }

                $GLOBALS['APP']['hook_runtime']['last_error'] = $GLOBALS['THUNDER_HOOK_EXCEPTION_SNAPSHOTS'][$exceptionId];
                unset($GLOBALS['THUNDER_HOOK_EXCEPTION_SNAPSHOTS'][$exceptionId]);
            }
        }

        $class = is_object($exception) ? get_class($exception) : 'Exception';
        $trace = is_object($exception) && method_exists($exception, 'getTrace') ? $exception->getTrace() : [];
		$exceptionFile = is_object($exception) && method_exists($exception, 'getFile')
			? $exception->getFile()
			: 'Unknown file';
		$exceptionLine = is_object($exception) && method_exists($exception, 'getLine')
			? $exception->getLine()
			: 0;

		// Hook guard exceptions are created inside functions.php. Prefer the
		// application line that actually called the duplicate do_action()/do_filter().
		if (is_array($hookExceptionSnapshot)
			&& !empty($hookExceptionSnapshot['failure_site'])
			&& is_array($hookExceptionSnapshot['failure_site'])) {
			$failureSite = $hookExceptionSnapshot['failure_site'];
			if (!empty($failureSite['file'])) {
				$exceptionFile = (string) $failureSite['file'];
			}
			if (!empty($failureSite['line'])) {
				$exceptionLine = (int) $failureSite['line'];
			}
		}

        thunder_debug_render([
            'type' => E_ERROR,
            'message' => is_object($exception) && method_exists($exception, 'getMessage')
                ? $exception->getMessage()
                : 'Uncaught exception',
			'file' => $exceptionFile,
			'line' => $exceptionLine,
            'code' => is_object($exception) && method_exists($exception, 'getCode')
                ? $exception->getCode()
                : 0,
            'trace' => $trace,
            'classification' => [
                'title' => 'UNCAUGHT EXCEPTION',
                'type' => $class,
                'severity' => 'Critical',
            ],
        ]);
    });

    register_shutdown_function(function () {
        if (!empty($GLOBALS['THUNDER_ERROR_RENDERED']) || !empty($GLOBALS['THUNDER_ERROR_HANDLING'])) {
            return;
        }

        $error = error_get_last();
        $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR];

        if (!$error || !in_array($error['type'], $fatalTypes, true)) {
            return;
        }

        list($message, $fatalTraceLines) = thunder_debug_split_fatal_message($error['message']);

        thunder_debug_render([
            'type' => $error['type'],
            'message' => $message,
            'file' => $error['file'],
            'line' => $error['line'],
            'trace' => [],
            'fatal_trace_lines' => $fatalTraceLines,
        ]);
    });
}

/*
 *---------------------------------------------------------------
 * AUTOLOAD CLASSES DURING INSTANTIATION
 *---------------------------------------------------------------
 */
spl_autoload_register(function($classname){

	$parts = explode("\\", $classname);
	$classname = array_pop($parts);

	/*
	 *---------------------------------------------------------------
	 * Check the main models folder first for the class file
	 *---------------------------------------------------------------
	 */
	$path = 'app'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR. ucfirst($classname) . '.php';
	if(file_exists($path))
	{
		require_once $path;
	}else{

		/*
		 *---------------------------------------------------------------
		 * Check the backtrace to know which plugin is trying to load a class
		 *---------------------------------------------------------------
		 * so we check its own models folder for the file
		 *---------------------------------------------------------------
		 */
 
		$path = get_context()->path . 'models'. DIRECTORY_SEPARATOR . ucfirst($classname . '.php');
		if(file_exists($path))
		{
			require_once $path;
		}
	}
});


/*
 *---------------------------------------------------------------
 * LOAD REQUIRED FUNCTIONS AND CHECK FOR EXTENSIONS
 *---------------------------------------------------------------
 */
	require 'core-functions.php';
	require 'functions.php';
	require 'extensions.php';

/*
 *---------------------------------------------------------------
 * SET LOCAL OR REMOTE CONSTANTS
 *---------------------------------------------------------------
 */
if((empty($_SERVER['SERVER_NAME']) && strpos(PHP_SAPI, 'cgi') !== 0) || (!empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost'))
{
    define( 'DB_NAME', LOCAL_DB_NAME );
    define( 'DB_USER', LOCAL_DB_USER );
    define( 'DB_PASSWORD', LOCAL_DB_PASSWORD );
    define( 'DB_HOST', LOCAL_DB_HOST );
    define( 'DB_DRIVER', LOCAL_DB_DRIVER );
    define( 'DB_PORT', LOCAL_DB_PORT );

    define( 'ROOT', \Core\get_app_url() );
}else
{
    define( 'DB_NAME', REMOTE_DB_NAME );
    define( 'DB_USER', REMOTE_DB_USER );
    define( 'DB_PASSWORD', REMOTE_DB_PASSWORD );
    define( 'DB_HOST', REMOTE_DB_HOST );
    define( 'DB_DRIVER', REMOTE_DB_DRIVER );
    define( 'DB_PORT', REMOTE_DB_PORT );

    define( 'ROOT', \Core\get_app_url(false) );	
}

/*
 *---------------------------------------------------------------
 * LOAD REQUIRED FILES AND CLASSES
 *---------------------------------------------------------------
 */
 
	require 'QueryBuilder.php';
	require 'Database.php';
	require 'Model.php';
	require 'App.php';

