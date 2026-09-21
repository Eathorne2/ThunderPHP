<?php

/**
 * This file is part of the ThunderPHP Framework.
 * This file contains common functions used throughout the application, that the user has access to.
 * 
 * @package ThunderPHP
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

/**
 * Get the current url.
 * 
 * @return string Returns path to the current page
 * 
 */
function current_url() 
{
  // Check if HTTPS is used
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

  // Get the host name (e.g., www.example.com or localhost)
  $host = $_SERVER['HTTP_HOST'];

  // Get the requested URI (e.g., /path/to/page.php?query=string)
  // htmlspecialchars() is used to mitigate potential XSS risks
  $requestUri = htmlspecialchars($_SERVER['REQUEST_URI']);

  // Combine the parts to form the full URL
  $fullUrl = $protocol . $host . $requestUri;

  return $fullUrl;
}

/**
 * Prepends root path to a provided string path.
 * 
 * @param string $path A path string you wish to add root path to
 * @return string Returns path which contains the root http path
 * 
 */
function base_url(string $path = ''): string
{
    return ROOT . '/' . ltrim($path, '/');
}

/**
 * Save values for use between functions. Works like a global variable.
 * Values are isolated between plugins to avoid collisions.
 * 
 * @param string $key A key to save your values to.
 * @return bool Returns true or false indicating saved or not.
 * 
 */
function set_value(string|array $key, mixed $value = ''):bool
{
	global $USER_DATA;

	$json = get_context(); 
	$plugin_id = $json->id;

	if(is_array($key))
	{
		foreach ($key as $k => $value) {
			
			$USER_DATA[$plugin_id][$k] = $value;
		}
		return true;
	}else
	{
		$USER_DATA[$plugin_id][$key] = $value;
		return true;
	}

	return false;
}

/**
 * Retrieve values saved throughout the plugin. Works like a global variable.
 * Values are isolated between plugins to avoid collisions.
 * 
 * @param string $key A key to identify your value.
 * @return mixed Returns a mixed value saved earlier using the $key.
 * 
 */
function get_value(string $key = ''):mixed
{
	global $USER_DATA;

	$json = get_context();  
	$plugin_id = $json->id;

	if(empty($key))
		return $USER_DATA[$plugin_id] ?? '';

	return !empty($USER_DATA[$plugin_id][$key]) ? $USER_DATA[$plugin_id][$key] : null;

}

/**
 * Returns the plugin id of the current plugin.
 * Plugn id is saved in the config.json file or each plugin.
 * 
 * @return string Returns the plugin id as a string.
 * 
 */
function plugin_id():string
{
	
	$json = get_context();
	return $json->id ?? '';
}

/**
 * Retrieves application level values. Works like a global variable.
 * Values are shared between plugins and not isolated.
 * 
 * @param string $key A key to the value you need to retrieve.
 * @return mixed Returns a mixed value saved earlier.
 * 
 */
function APP(string $key = ''):mixed
{
	global $APP;

	if(!empty($key))
	{
		return !empty($APP[$key]) ? $APP[$key] : null;
	}else{

		return $APP;
	}

	return null;
}

/**
 * Writes to the $APP global
 */
function APP_SET(string|array $key, mixed $value = ''): bool
{
    global $APP;

    if (!is_array($APP)) {
        $APP = [];
    }

    $setNested = function (array &$target, string $path, mixed $val): bool {
        if ($path === '') {
            return false;
        }

        $parts = explode('.', $path);
        $ref = &$target;
        $lastIndex = count($parts) - 1;

        foreach ($parts as $index => $part) {
            if ($part === '') {
                return false;
            }

            if ($index === $lastIndex) {
                $ref[$part] = $val;
                return true;
            }

            if (!isset($ref[$part]) || !is_array($ref[$part])) {
                $ref[$part] = [];
            }

            $ref = &$ref[$part];
        }

        return false;
    };

    if (is_array($key)) {
        foreach ($key as $k => $v) {
            if (!is_string($k) || $k === '') {
                continue;
            }

            $setNested($APP, $k, $v);
        }

        return true;
    }

    return $setNested($APP, $key, $value);
}

/**
 * Echoes loaded plugins on a given page.
 * Mostly used for debugging purposes.
 *  
 */
function show_plugins():void
{
	global $APP;
	
	$names = array_column($APP['plugins'] ?? [], 'name');
	dd($names ?? []);

}

/**
 * Splits the provided url string into segments.
 * Motly used internally by the Core.
 * 
 * @param string $url A URL to be split.
 * @return array Returns an array of individual segments of the URL.
 * 
 */
function split_url(string $url):array
{
	return explode("/", trim($url,'/'));
}


/**
 * Returns segments from the current URL.
 * Values are isolated between plugins to avoid collisions.
 * 
 * @param string $key A key to save your values to.
 * @return string Returns the value of a segment on the provided key.
 * @return array Returns an array of all segments in the URL if no key is provided.
 * 
 */
function URL(string $key = ''):string|array
{
	global $APP;

	if(is_numeric($key) || !empty($key))
	{
		if(!empty($APP['URL'][$key]))
		{
			return $APP['URL'][$key];
		}
	}else{
		return $APP['URL'];
	}

	return '';
}

/**
 * Check if a plugin exists using its plugin id.
 * 
 * @param string $plugin_id The required plugin id
 * @return bool Returns false if the plugin does not exist
 * @return object Returns an object of its config,json file if the plugin exists
 * 
 */
function plugin_exists(string $plugin_id):bool|object 
{
	global $APP;

	$folders = \Core\get_plugin_folders();
	$plugins = [];
	foreach ($folders as $folder)
	{
		$file = 'plugins/' . $folder . '/config.json';
		if(file_exists($file))
		{
			$json = json_decode(file_get_contents($file));
			
			if(is_object($json) && isset($json->id))
				if(!empty($json->active))
					$plugins[] = $json;

		}
	}

	$ids = array_column($plugins, 'id');
	$key = array_search($plugin_id, $ids);
	if($key !== false)
	{
		return $plugins[$key];
	}

	return false;
}

/**
 * Check if a route was matched on this particular request
 * 
 * @param string $route A route you wish to match e.g profile.edit
 * @return bool true or false
 * 
 */
function is_route(string $route = ''):bool
{

	if(empty($route))
		return true;

	global $APP;
	if(!empty($APP['matched_routes'][plugin_id()]))
		return in_array($route, $APP['matched_routes'][plugin_id()]);

	return false;
}

/**
 * Get the current route that was matched for current plugin
 * 
 * @return string The matched route
 * 
 */
function get_route_name():string
{

	global $APP;
	if(!empty($APP['matched_routes'][plugin_id()]))
		return $APP['matched_routes'][plugin_id()][0];

	return '';
}

/**
 * Return a specific value from the URL of the matched route 
 * 
 * @param string $key A key of the value you wish to get
 * @return string the value of the key
 * 
 */
function get_param(string $key = ''):string
{

	global $APP;
	if(!empty($APP['matched_routes_params'][$key]))
		return $APP['matched_routes_params'][$key];

	return '';
}

/**
 * Ensure hook runtime diagnostics are available for the current request.
 *
 * The runtime data is stored in $APP only for the life of the request. It is
 * never written to the session. HOOK_MAX_DEPTH and HOOK_HISTORY_LIMIT may be
 * defined in config.php to override the defaults.
 */
function thunder_hook_runtime_init():void
{
	global $APP;

	if(!is_array($APP ?? null))
		$APP = [];

	if(!isset($APP['hook_runtime']) || !is_array($APP['hook_runtime']))
		$APP['hook_runtime'] = [];

	$runtime = &$APP['hook_runtime'];

	if(!isset($runtime['max_depth']))
		$runtime['max_depth'] = defined('HOOK_MAX_DEPTH') ? max(1, (int)HOOK_MAX_DEPTH) : 100;

	if(!isset($runtime['history_limit']))
		$runtime['history_limit'] = defined('HOOK_HISTORY_LIMIT') ? max(1, (int)HOOK_HISTORY_LIMIT) : 250;

	if(!isset($runtime['active_stack']) || !is_array($runtime['active_stack']))
		$runtime['active_stack'] = [];

	if(!isset($runtime['execution_history']) || !is_array($runtime['execution_history']))
		$runtime['execution_history'] = [];

	if(!array_key_exists('last_error', $runtime))
		$runtime['last_error'] = null;

	if(!isset($runtime['sequence']))
		$runtime['sequence'] = 0;

	if(!isset($runtime['history_dropped']))
		$runtime['history_dropped'] = 0;
}

/**
 * Read a value from a plugin context object or array.
 */
function thunder_hook_context_value(mixed $context, string $key, mixed $default = ''):mixed
{
	if(is_object($context) && isset($context->{$key}))
		return $context->{$key};

	if(is_array($context) && array_key_exists($key, $context))
		return $context[$key];

	return $default;
}

/**
 * Return a project-relative file path when possible.
 */
function thunder_hook_relative_file(?string $file):string
{
	if(empty($file))
		return '';

	$normalized = str_replace('\\', '/', $file);
	if(defined('ROOTPATH'))
	{
		$root = rtrim(str_replace('\\', '/', ROOTPATH), '/') . '/';
		if(strpos($normalized, $root) === 0)
			return substr($normalized, strlen($root));
	}

	return $normalized;
}

/**
 * Create a concise, serializable description of a callback.
 */
function thunder_hook_callback_description(mixed $callback):array
{
	$description = [
		'name' => 'Unknown callback',
		'file' => '',
		'line' => null,
	];

	try
	{
		$reflection = null;

		if($callback instanceof \Closure)
		{
			$description['name'] = 'Closure';
			$reflection = new \ReflectionFunction($callback);
		}elseif(is_string($callback))
		{
			$description['name'] = $callback;
			if(strpos($callback, '::') !== false)
			{
				[$class, $method] = explode('::', $callback, 2);
				$reflection = new \ReflectionMethod($class, $method);
			}elseif(function_exists($callback))
			{
				$reflection = new \ReflectionFunction($callback);
			}
		}elseif(is_array($callback) && count($callback) >= 2)
		{
			$class = is_object($callback[0]) ? get_class($callback[0]) : (string)$callback[0];
			$method = (string)$callback[1];
			$description['name'] = $class . '::' . $method;
			$reflection = new \ReflectionMethod($callback[0], $method);
		}elseif(is_object($callback) && method_exists($callback, '__invoke'))
		{
			$description['name'] = get_class($callback) . '::__invoke';
			$reflection = new \ReflectionMethod($callback, '__invoke');
		}

		if($reflection)
		{
			$description['file'] = thunder_hook_relative_file($reflection->getFileName() ?: '');
			$description['line'] = $reflection->getStartLine() ?: null;
		}
	}catch(\Throwable $exception)
	{
		// Reflection is diagnostic only. Callback execution must not depend on it.
	}

	return $description;
}

/**
 * Determine whether a registered callback is eligible for the current route.
 */
function thunder_hook_route_matches(array $func_data):bool
{
	global $APP;

	$route = trim((string)($func_data['route'] ?? ''));
	if($route === '')
		return true;

	$plugin_id = (string)thunder_hook_context_value($func_data['context'] ?? null, 'id', '');
	if($plugin_id === '')
		return false;

	$matched_routes = $APP['matched_routes'][$plugin_id] ?? [];
	return is_array($matched_routes) && in_array($route, $matched_routes, true);
}

/**
 * Check whether at least one callback on a hook can run on this request.
 */
function thunder_hook_has_eligible_callbacks(array $callbacks):bool
{
	foreach($callbacks as $func_data)
	{
		if(is_array($func_data) && thunder_hook_route_matches($func_data))
			return true;
	}

	return false;
}

/**
 * Build the diagnostic details for a callback that is about to run.
 */
function thunder_hook_callback_frame(string $type, string $hook, int $priority, array $func_data, int $depth):array
{
	global $APP;

	$context = $func_data['context'] ?? null;
	$plugin_id = (string)thunder_hook_context_value($context, 'id', '');
	$callback = thunder_hook_callback_description($func_data['func'] ?? null);

	return [
		'type' => $type,
		'hook' => $hook,
		'depth' => $depth,
		'plugin_id' => $plugin_id,
		'plugin_name' => (string)thunder_hook_context_value($context, 'name', ''),
		'callback' => $callback['name'],
		'file' => $callback['file'],
		'line' => $callback['line'],
		'priority' => $priority,
		'registered_route' => (string)($func_data['route'] ?? ''),
		'matched_routes' => $plugin_id !== '' && isset($APP['matched_routes'][$plugin_id])
			? array_values((array)$APP['matched_routes'][$plugin_id])
			: [],
	];
}

/**
 * Return the callback currently responsible for triggering a nested hook.
 */
function thunder_hook_current_callback():?array
{
	global $APP;
	thunder_hook_runtime_init();

	$stack = $APP['hook_runtime']['active_stack'];
	if(empty($stack))
		return null;

	$frame = $stack[array_key_last($stack)];
	return !empty($frame['current_callback']) && is_array($frame['current_callback'])
		? $frame['current_callback']
		: null;
}

/**
 * Locate the application line that called do_action() or do_filter().
 *
 * The recursion guard itself throws from this core file, but that location is
 * rarely useful to the developer. This call site points to the nested hook
 * invocation that attempted to re-enter an active hook.
 */
function thunder_hook_dispatch_call_site():array
{
	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

	foreach($trace as $frame)
	{
		$function = (string)($frame['function'] ?? '');
		if($function !== 'do_action' && $function !== 'do_filter')
			continue;

		$file = (string)($frame['file'] ?? '');
		$line = (int)($frame['line'] ?? 0);

		return [
			'dispatcher' => $function,
			'file' => $file,
			'relative_file' => thunder_hook_relative_file($file),
			'line' => $line,
		];
	}

	return [
		'dispatcher' => '',
		'file' => '',
		'relative_file' => '',
		'line' => 0,
	];
}

/**
 * Format one hook frame for a compact exception message.
 */
function thunder_hook_frame_label(array $frame):string
{
	$label = ($frame['type'] ?? 'hook') . ':' . ($frame['hook'] ?? 'unknown');
	$callback = $frame['current_callback'] ?? $frame['requested_by'] ?? null;

	if(is_array($callback))
	{
		$plugin = $callback['plugin_id'] ?? $callback['plugin_name'] ?? '';
		$name = $callback['callback'] ?? '';
		$actor = trim($plugin . ($plugin !== '' && $name !== '' ? '::' : '') . $name);
		if($actor !== '')
			$label .= ' [' . $actor . ']';
	}

	return $label;
}

/**
 * Store a hook snapshot against an exception until the global exception
 * handler confirms that the exception escaped application code.
 */
function thunder_hook_store_exception_snapshot(\Throwable $exception, array $snapshot):void
{
	$id = spl_object_id($exception);
	if(isset($GLOBALS['THUNDER_HOOK_EXCEPTION_SNAPSHOTS'][$id]))
		return;

	$GLOBALS['THUNDER_HOOK_EXCEPTION_SNAPSHOTS'][$id] = $snapshot;

	if(count($GLOBALS['THUNDER_HOOK_EXCEPTION_SNAPSHOTS']) > 10)
	{
		$oldest_id = array_key_first($GLOBALS['THUNDER_HOOK_EXCEPTION_SNAPSHOTS']);
		unset($GLOBALS['THUNDER_HOOK_EXCEPTION_SNAPSHOTS'][$oldest_id]);
	}
}

/**
 * Create a hook failure exception and preserve its full runtime snapshot.
 */
function thunder_hook_failure_exception(string $kind, string $message, array $active_stack, array $path):\RuntimeException
{
	global $APP;
	thunder_hook_runtime_init();

	$exception = new \RuntimeException($message);
	$last_frame = !empty($path) ? $path[array_key_last($path)] : [];
	$failure_site = isset($last_frame['call_site']) && is_array($last_frame['call_site'])
		? $last_frame['call_site']
		: [];

	thunder_hook_store_exception_snapshot($exception, [
		'kind' => $kind,
		'message' => $message,
		'failure_site' => $failure_site,
		'active_stack' => $active_stack,
		'path' => $path,
		'execution_history' => $APP['hook_runtime']['execution_history'],
		'history_dropped' => $APP['hook_runtime']['history_dropped'],
		'captured_at' => microtime(true),
	]);

	return $exception;
}

/**
 * Enter a hook dispatcher and detect direct or indirect recursive re-entry.
 */
function thunder_hook_enter(string $type, string $hook):int
{
	global $APP;
	thunder_hook_runtime_init();

	$runtime = &$APP['hook_runtime'];
	$active_stack = $runtime['active_stack'];
	$depth = count($active_stack) + 1;
	$requested_by = thunder_hook_current_callback();
	$call_site = thunder_hook_dispatch_call_site();

	$attempted_frame = [
		'type' => $type,
		'hook' => $hook,
		'depth' => $depth,
		'requested_by' => $requested_by,
		'call_site' => $call_site,
		'current_callback' => null,
		'attempted' => false,
		'repeated_from' => null,
	];

	foreach($active_stack as $index => $frame)
	{
		if(($frame['type'] ?? '') === $type && ($frame['hook'] ?? '') === $hook)
		{
			$attempted_frame['attempted'] = true;
			$attempted_frame['repeated_from'] = $index;
			$path = array_merge($active_stack, [$attempted_frame]);
			$labels = array_map('thunder_hook_frame_label', $path);
			$message = 'Hook recursion detected: ' . implode(' -> ', $labels);

			throw thunder_hook_failure_exception('recursion', $message, $active_stack, $path);
		}
	}

	if($depth > (int)$runtime['max_depth'])
	{
		$attempted_frame['attempted'] = true;
		$path = array_merge($active_stack, [$attempted_frame]);
		$labels = array_map('thunder_hook_frame_label', $path);
		$message = 'Maximum hook depth of ' . (int)$runtime['max_depth'] . ' exceeded: ' . implode(' -> ', $labels);

		throw thunder_hook_failure_exception('depth', $message, $active_stack, $path);
	}

	$runtime['active_stack'][] = $attempted_frame;
	return array_key_last($runtime['active_stack']);
}

/**
 * Mark a callback as active and append it to the request hook history.
 */
function thunder_hook_begin_callback(int $frame_index, array $callback):void
{
	global $APP;
	thunder_hook_runtime_init();

	$runtime = &$APP['hook_runtime'];
	if(isset($runtime['active_stack'][$frame_index]))
		$runtime['active_stack'][$frame_index]['current_callback'] = $callback;

	$runtime['sequence']++;
	$history_entry = $callback;
	$history_entry['sequence'] = $runtime['sequence'];
	$runtime['execution_history'][] = $history_entry;

	while(count($runtime['execution_history']) > (int)$runtime['history_limit'])
	{
		array_shift($runtime['execution_history']);
		$runtime['history_dropped']++;
	}
}

/**
 * Clear the callback currently attached to a hook dispatcher frame.
 */
function thunder_hook_end_callback(int $frame_index):void
{
	global $APP;
	thunder_hook_runtime_init();

	if(isset($APP['hook_runtime']['active_stack'][$frame_index]))
		$APP['hook_runtime']['active_stack'][$frame_index]['current_callback'] = null;
}

/**
 * Leave the current hook dispatcher frame.
 */
function thunder_hook_leave():void
{
	global $APP;
	thunder_hook_runtime_init();
	array_pop($APP['hook_runtime']['active_stack']);
}

/**
 * Preserve a stack snapshot for an exception until the global exception
 * handler knows whether that exception escaped application code.
 */
function thunder_hook_capture_exception(\Throwable $exception):void
{
	global $APP;
	thunder_hook_runtime_init();

	if(!empty($APP['hook_runtime']['last_error']))
		return;

	thunder_hook_store_exception_snapshot($exception, [
		'kind' => 'callback_exception',
		'message' => $exception->getMessage(),
		'active_stack' => $APP['hook_runtime']['active_stack'],
		'path' => $APP['hook_runtime']['active_stack'],
		'execution_history' => $APP['hook_runtime']['execution_history'],
		'history_dropped' => $APP['hook_runtime']['history_dropped'],
		'captured_at' => microtime(true),
	]);
}

/**
 * Register a function to run when a specific hook is fired using do_action().
 *
 * @param string $hook Hook name.
 * @param mixed $func Callback to execute.
 * @param int $priority Lower values execute first.
 * @param string $route Optional registered route name.
 */
function add_action(string $hook, mixed $func, int $priority = 10, string $route = ''):void
{
	global $ACTIONS;
	global $APP;

	while(!empty($ACTIONS[$hook][$priority]))
		$priority++;

	$ACTIONS[$hook][$priority] = [
		'func' => $func,
		'context' => $APP['context'],
		'route' => $route,
	];
}

/**
 * Run callbacks registered for an action hook.
 */
function do_action(string $hook, array $data = []):void
{
	global $ACTIONS;
	global $APP;

	if(empty($ACTIONS[$hook]))
		return;

	ksort($ACTIONS[$hook]);
	if(!thunder_hook_has_eligible_callbacks($ACTIONS[$hook]))
		return;

	$frame_index = thunder_hook_enter('action', $hook);

	try
	{
		foreach($ACTIONS[$hook] as $priority => $func_data)
		{
			if(!thunder_hook_route_matches($func_data))
				continue;

			$cur_context = $APP['context'] ?? (object)[];
			$APP['context'] = $func_data['context'];
			$callback = thunder_hook_callback_frame('action', $hook, (int)$priority, $func_data, $frame_index + 1);
			thunder_hook_begin_callback($frame_index, $callback);

			try
			{
				$func_data['func']($data);

				if($hook === 'view')
					$APP['route_handled'] = true;
			}catch(\Throwable $exception)
			{
				thunder_hook_capture_exception($exception);
				throw $exception;
			}finally
			{
				thunder_hook_end_callback($frame_index);
				$APP['context'] = $cur_context;
			}
		}
	}finally
	{
		thunder_hook_leave();
	}
}

/**
 * Get the current app context i.e the current plugin data.
 */
function get_context():object
{
	global $APP;
	return $APP['context'] ?? (object)[];
}

/**
 * Register a function to run when a specific filter hook is fired.
 */
function add_filter(string $hook, mixed $func, int $priority = 10, string $route = ''):void
{
	global $FILTER;
	global $APP;

	while(!empty($FILTER[$hook][$priority]))
		$priority++;

	$FILTER[$hook][$priority] = [
		'func' => $func,
		'context' => $APP['context'],
		'route' => $route,
	];
}

/**
 * Run callbacks registered for a filter hook and return the altered data.
 */
function do_filter(string $hook, mixed $data = ''):mixed
{
	global $FILTER;
	global $APP;

	if(empty($FILTER[$hook]))
		return $data;

	ksort($FILTER[$hook]);
	if(!thunder_hook_has_eligible_callbacks($FILTER[$hook]))
		return $data;

	$frame_index = thunder_hook_enter('filter', $hook);

	try
	{
		foreach($FILTER[$hook] as $priority => $func_data)
		{
			if(!thunder_hook_route_matches($func_data))
				continue;

			$cur_context = $APP['context'] ?? (object)[];
			$APP['context'] = $func_data['context'];
			$callback = thunder_hook_callback_frame('filter', $hook, (int)$priority, $func_data, $frame_index + 1);
			thunder_hook_begin_callback($frame_index, $callback);

			try
			{
				$data = $func_data['func']($data);

				if($hook === 'view')
					$APP['route_handled'] = true;
			}catch(\Throwable $exception)
			{
				thunder_hook_capture_exception($exception);
				throw $exception;
			}finally
			{
				thunder_hook_end_callback($frame_index);
				$APP['context'] = $cur_context;
			}
		}
	}finally
	{
		thunder_hook_leave();
	}

	return $data;
}


/**
 * Display the provided data to the page as HTML. Good for debugging.
 * 
 * @param mixed $data The data to display.
 * @param bool $stop Whether or not to stop the script after displaying the data. Defaults to false.
 * @return void
 * 
 */
function dd(mixed $data, bool $stop = false):void
{
	echo "<pre><div style='margin:1px;background-color:#444;color:white;padding:5px 10px'>";
	print_r($data);
	echo "</div></pre>";

	if($stop)
		exit();
}

/**
 * Returns the current page segment from the URL. Same as URL(0).
 * Example: if(page() == 'home'){}
 */
function page():string
{
	return URL(0);
}

/**
 * Redirect the user to the specified page. The root path is prepended to the provided link.
 * 
 * @param string $url A URL to redirect to, without the root path e.g redirect('home');
 */
function redirect(string $url):void
{
	header("Location: ". ROOT .'/'. trim($url,'/'));
	die;
}

/**
 * Prepends the path for the current LOOK directory to the path provided.
 * The current LOOK is set in the config.json file of each plugin.
 * 
 * @param string $path A path to the view file
 * @return string A full path to the view file with LOOK directory prepended
 */
function current_look(string $path):string
{
	$json = get_context();
	$look = $json->look ?? 'main';
	return $json->path . "looks/{$look}/" . $path;

}

/**
 * Prepends the HTTP path for the current LOOK directory to the path provided.
 * The current LOOK is set in the config.json file of each plugin.
 * 
 * @param string $path A path to the asset file(css or js)
 * @return string A full HTTP path to the asset file with LOOK directory prepended
 */
function current_look_http(string $path):string
{
	$json = get_context();
	$look = $json->look ?? 'main';
	return ROOT .'/'.$json->path . "looks/{$look}/" . $path;

}

/**
 * Returns the plugin path to the file calling this function. Used mainly to get paths to PHP files
 * 
 * @param string $path A path to append to the plugin path
 * @return string A full path with plugin path prepended
 */
function plugin_path(string $path = ''):string
{
 	return get_context()->path . $path;
}

/**
 * Returns the plugin HTTP path to the file calling this function. Used mainly to get paths to css,js or image files
 * 
 * @param string $path A path to append to the plugin path
 * @return string A full path with plugin path prepended
 */
function plugin_http_path(string $path = '')
{

	return get_context()->http_path . $path;
}

/**
 * Returns an array of all roles the current user has.
 * Roles are added using the filter hook 'user_roles'.
 * 
 * @return array An array of roles
 */
function user_roles():array
{
	global $APP;

	if(empty($APP['user_roles']))
		$APP['user_roles'] = [];
	else
		return $APP['user_roles'];

	$APP['user_roles'] = do_filter('user_roles',$APP['user_roles']);

	return is_array($APP['user_roles']) ? array_unique($APP['user_roles']) : [];
}

/**
 * Check if current user has a specified role, e.g if(contains_role('admin')).
 * Roles are added using the filter hook 'user_roles'.
 * 
 * @param string $role A role to check for
 * @return bool Return true or false depending if the user has said role
 */
function contains_role(string $role):bool
{
	return in_array(strtolower($role), array_map('strtolower', user_roles()));
}

/**
 * Check if current user has a specified permission, e.g if(user_can('edit posts')).
 * Permissions are added using the filter hook 'user_permissions'.
 * 
 * @param string $permission A permission to check for
 * @return bool Return true or false depending if the user has said permission
 */
function user_can(?string $permission):bool
{
	if(empty($permission)) return true;

	$ses = new \Core\Session;
	
	if($permission == 'logged_in')
	{
		if($ses->is_logged_in())
			return true;

		return false;
	}

	if($permission == 'not_logged_in')
	{
		if(!$ses->is_logged_in())
			return true;

		return false;
	}
	
	if(in_array('admin', user_roles()))
		return true;

	global $APP;
	
	if(empty($APP['user_permissions'])){
		$APP['user_permissions'] = do_filter('user_permissions',[]);
	}
 
	if(in_array('all', $APP['user_permissions'][plugin_id()] ?? []))
		return true;
	
	if(in_array($permission, $APP['user_permissions'][plugin_id()] ?? []))
		return true;

	return false;
}


/**
 * A form helper to persist POST or GET variables as input values after a page refresh.
 * Mostly used for input type text or textarea, in their value attribute
 * 
 * @param string $key The array key to persist
 * @param string $default A value to display in the input on first page load
 * @param string $type Whether to get values from the GET or POST array
 * 
 * @return string A value from either the POST or GET array
 */  
function old_value(string $key, string $default = '',string $type = 'post'):string
{
	$array = $_POST;
	if($type == 'get')
		$array = $_GET;

	if(!empty($array[$key]))
		return $array[$key];

	return $default;
}

/**
 * A form helper to persist POST or GET variables as input values after a page refresh.
 * Mostly used for input type select and would be added to each option as an attribute
 * 
 * @param string $key The array key to persist
 * @param string $value A value of an option in the select input
 * @param string $default An option to select on first page load
 * @param string $type Whether to get values from the GET or POST array
 * 
 * @return string Returns an empty string or 'selected' if the option is selected
 */ 
function old_select(string $key, string $value, string $default = '',string $type = 'post'):string
{
	$array = $_POST;
	if($type == 'get')
		$array = $_GET;

	if(!empty($array[$key]))
	{
		if($array[$key] == $value)
			return ' selected ';
	}else
	{
		if($default == $value)
			return ' selected ';
	}

	return '';
}

/**
 * A form helper to persist POST or GET variables as input values after a page refresh.
 * Mostly used for input type radio or checkbox and would be added to each input as an attribute
 * 
 * @param string $key The array key to persist
 * @param string $value A value of an option in the select input
 * @param string $default An option to select on first page load
 * @param string $type Whether to get values from the GET or POST array
 * 
 * @return string Returns an empty string or 'checked' if the option is selected
 */ 
function old_checked(string $key, string $value, string $default = '',string $type = 'post'):string
{
	$array = $_POST;
	if($type == 'get')
		$array = $_GET;

	if(!empty($array[$key]))
	{
		if($array[$key] == $value)
			return ' checked ';
	}else
	{
		if($default == $value)
			return ' checked ';
	}

	return '';
}

/**
 * Creates a CSRF token that will be verified using the csrf_verify() function placed in a controller
 * The token can be returned as plain string or as a complete hidden HTML input
 * 
 * @param string $sesKey The input name to use when returned as an input
 * @param bool $returnAsInput Whether to return as a s tring or complete input. Default is input
 * @param int $hours How long to keep the token valid
 * @return string Returns a plain token or HTML input string
 */
function csrf($sesKey = 'csrf', $hours = 1, $return_input = true)
{
	$key = '';

	$ses = new \Core\Session;
	$key = hash('sha256', time() . rand(0,99));
	$expires = time() + ((60*60)*$hours);

	$data = $ses->get($sesKey);
	if(is_array($data))
	{
		$arr = [];
		$arr[] = [
			'key'=> $key,
			'expires'=>$expires
		];
		$ses->set($sesKey,array_merge($arr,$data));
	}else{
		$arr = [];
		$arr[] = [
			'key'=> $key,
			'expires'=>$expires
		];
		$ses->set($sesKey,$arr);
	}

	if($return_input)
		return "<input type='hidden' value='".esc($key)."' name='$sesKey' />";
	else
		return $key;
}


/**
 * Verify a CSRF token that was created using the csrf() function placed in a form
 * Example: if(csrf_verify($_POST)){ //success }
 * 
 * @param array $post Post data from a form. Usually the $_POST array
 * @param string $sesKey The name of the input containing the token
 * @return bool Returns true when the token is valid or false if not
 */ 
function csrf_verify($post, $sesKey = 'csrf')
{
	
	if(empty($post[$sesKey]))
		return false;

	$ses = new \Core\Session;
	$data = $ses->get($sesKey);

	if(is_array($data))
	{

		foreach ($data as $i => $arr) {
			if(!is_array($arr)) continue;

			if($arr['key'] === $post[$sesKey])
			{
				if($arr['expires'] > time()){
					unset($data[$i]);
					$ses->set($sesKey,$data);
					return true;
				}
				else{
					return false;
				}

			}

		}
		
	}

	return false;
}


/**
 * Return an absolute HTTP path to an image file or returns a placeholder image of the file does not exist.
 * 
 * @param string|null $path A relative path to the image
 * @param string $type The type of image you're trying to load. Used to know what kind of placeholder to load if the file does not exist
 * @return string An HTTP path to the image file
 */
function get_image(?string $path = '', string $type = 'post'):string
{
	$path = $path ?? '';

	if(file_exists($path))
		return ROOT . '/' . $path;
	
	if($type == 'post')
		return ROOT . '/assets/images/no_image.jpg';

	if($type == 'male')
		return ROOT . '/assets/images/user_male.jpg';

	if($type == 'female')
		return ROOT . '/assets/images/user_female.jpg';

	return ROOT . '/assets/images/no_image.jpg';
}

/**
 * Escapes HTML special characters. Same as built in htmlspecialchars function.
 * Used to escape data before displaying it on the page.
 * 
 * @param string|null $str A string to escape
 * @return string|null An escaped string
 */
function esc(?string $str):?string
{
	return htmlspecialchars($str);
}


/**
 * Return a more human readable date.
 * Example: 2025-01-10 will return 10th Jan, 2025
 * 
 * @param string|null $date A date to format
 * @return string A human readable date
 */
function get_date(?string $date):string
{
	$date = $date ?? '';
	return date("jS M, Y", strtotime($date));
}

/**
 * Save or retrieve a flash message. 
 * Mainly used to inform the user if actions their taking were successful or not.
 * 
 * @param string $type The type of message to save or retrieve. Can be any string.
 * @param string $msg The message to save. If provided, its a save, if omitted, its a read.
 * @param bool $erase Whether to delete the message or not after reading.
 * @return string|null Returns a saved message as null or string 
 */
function message(string $type, ?string $msg = '', bool $erase = false):?string
{

	$ses = new \Core\Session;
	if(!empty($msg))
	{
		$ses->set('message_'.$type,$msg);
	}else
	if(!empty($ses->get('message_'.$type)))
	{
		$msg = $ses->get('message_'.$type);

		if($erase)
			$ses->pop('message_'.$type);

		return $msg;
	}

	return '';
}

/**
 * Return path to class file from another plugin. Only use this if using hooks is not an option.
 * 
 * @param string $folder A folder name of the target plugin
 * @param string $class_name The name of the class to be targetted
 * @return string A path to the target class
 */
function class_path(string $folder, string $class_name):string
{
	return 'plugins/' . $folder. '/models/'. $class_name . '.php';
}

function missing_plugins()
{
	global $APP;
	return $APP['missing_plugins'] ?: []; 
}

function outdated_plugins()
{
	global $APP;
	return $APP['outdated_plugins'] ?: []; 
}

/**
 * Returns content where shortcodes have been replaced with actual content
 * 
 * */ 
function do_shortcode(string $content, array $context = []): string
{
    global $APP;

	$cur_context = $APP['context'] ?? (object)[];
    $new_content = preg_replace_callback('/\[([a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+)(.*?)\]/', function ($matches) use ($APP, $context) {

    	global $APP;
        $name = $matches[1];
        $raw_attrs = trim($matches[2]);

        if (empty($APP['shortcodes'][$name])) {
            return $matches[0];
        }

        $attrs = \Core\shortcode_parse_attrs($raw_attrs);
   	
		$APP['context'] = $APP['shortcodes'][$name]['context'];
        ob_start();
        call_user_func($APP['shortcodes'][$name]['func'], $attrs,$context);
        $result = ob_get_clean();
        if (is_array($result) || is_object($result)) {
            return '';
        }

        return (string) $result;

    }, $content);

	//give back previous context after runing function
	$APP['context'] = $cur_context;
    return $new_content;

}

/**
 * Adds a callback function to replace shortcodes with actual content
 * The callback should echo content to replace shortcodes with
 * 
 * */ 
function add_shortcode(string $name, callable $callback): void
{
    global $APP;
    $APP['shortcodes'][$name] = ['func'=>$callback,'context'=>$APP['context']];
}

/**
 * Read a nested array or object value using dot notation.
 *
 * Examples:
 * get_nested_value($data, 'user.profile.name');
 * get_nested_value($data, 'posts.0.title');
 * get_nested_value($data, 'settings.theme', 'default');
 *
 * @param mixed  $scope   The array, object, or ArrayAccess value to read.
 * @param string $path    Dot-separated path to the required value.
 * @param mixed  $default Value returned when the path does not exist.
 */
function get_nested_value(
    mixed $scope,
    string $path,
    mixed $default = null
): mixed {
    $segments = array_values(array_filter(
        explode('.', $path),
        static fn (string $segment): bool => $segment !== ''
    ));

    if ($segments === []) {
        return $default;
    }

    $value = $scope;

    foreach ($segments as $segment) {
        if (is_array($value)) {
            if (!array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
            continue;
        }

        if ($value instanceof \ArrayAccess) {
            if (!$value->offsetExists($segment)) {
                return $default;
            }

            $value = $value[$segment];
            continue;
        }

        if (is_object($value)) {
            if (
                !isset($value->{$segment})
                && !property_exists($value, $segment)
            ) {
                return $default;
            }

            $value = $value->{$segment};
            continue;
        }

        return $default;
    }

    return $value;
}
