<?php

/**
 * This file is part of the ThunderPHP Framework.
 * It contains functions only used by the core.
 * 
 * @package ThunderPHP
 * @version 1.0.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace Core;

/**
 * @ignore
 * Used by the Core
 */
function get_plugin_folders()
{
	global $APP;

	if(empty($APP['all_plugin_folders']))
	{
		$plugins_folder = 'plugins/';
		$res = [];
		$folders = scandir($plugins_folder);
		foreach ($folders as $folder) {
			if($folder != '.' && $folder != '..' && is_dir($plugins_folder . $folder))
				$res[] = $folder;
		}
		
		$APP['all_plugin_folders'] = $res;
		return $res;
	}
	
	return $APP['all_plugin_folders'];

}

/**
 * @ignore
 * Used by the Core
 */
function load_plugins($plugin_folders)
{
	global $APP;
	$loaded = false;
	$dependencies = [];

	$method = $_SERVER['REQUEST_METHOD'];
	$url_no_params = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$url_no_params = str_replace('/home', '/', $url_no_params);

	$base_url = rtrim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']),'/');

	foreach ($plugin_folders as $folder) {
		
		$file = 'plugins/' . $folder . '/config.json';
		if(file_exists($file))
		{
			$json = json_decode(file_get_contents($file));
			$validation = validate_plugin_config($json);

			if($validation['valid'])
			{
				if(!is_plugin_compatible($json))
				{
					$APP['outdated_plugins'][] = (object)[
						"name"=>$json->name,
		                "id"=>$json->id,
		                "version"=>$json->version,
		                "core_requires"=> $json->core_requires,
		                "core_version"=> VERSION,
					];
					continue;
				}

				if(!empty($json->active))
				{
					//save permissions
					if(!empty($json->permissions))
						APP_SET('permissions.'.$json->id,$json->permissions);

					$file = 'plugins/' . $folder . '/plugin.php';
					$has_matched_route = false;
					if(!empty($json->routes->routes))
					{
						foreach($json->routes->routes as $route)
						{
							if(preg_match("/$route->method/", $method))
							{

								$new_pattern = preg_replace("/({[^\/]+})/", "([\w\-\.\_]+)", $base_url .$route->pattern);
								$new_pattern = preg_replace("#/+#", '/', $new_pattern);

								preg_match('#^'.$new_pattern.'$#', $url_no_params, $matches);
								if(!empty($matches[0])){

									$APP['matched_routes'][$json->id][] = $route->name;
									$has_matched_route = true;

									//get route params if empty
									if(empty($APP['matched_routes_params']))
									{
										unset($matches[0]);
										preg_match_all("/{([^\/]+)}/", $route->pattern, $param_keys);
										if(!empty($param_keys[1]))
											$APP['matched_routes_params'] = array_combine($param_keys[1], $matches);

									}

								}

							}
						}
					}
					
					if(file_exists($file) && (valid_route($json) || $has_matched_route))
					{
						$json->index = $json->index ?? 1;
						$json->version = $json->version ?? "1.0.0";
						$json->dependencies = $json->dependencies ?? (object)[];
						$json->index_file = $file;
						$json->path = 'plugins/' . $folder . '/';
						$json->http_path = ROOT . '/' . $json->path;
						$APP['plugins'][] = $json;


					}
				}
			}else
			{
				if(DEBUG){
					dd("Invalid plugin config file: " . $file);
			    	dd("Plugin config invalid:\n- " . implode("\n- ", $validation['errors']));
			    	die;
				}
			}
		}
	}

	if(!empty($APP['plugins']))
	{
		$APP['plugins'] = sort_plugins($APP['plugins']);
		foreach ($APP['plugins'] as $akey => $json)
		{
			/** check for plugin dependencies **/
			if(!empty((array)$json->dependencies))
			{
				foreach ((array)$json->dependencies as $plugin_id => $mp_data) {
					
					$version = $mp_data->version ?? '0';
					if($plugin_data = plugin_exists($plugin_id))
					{
						$required_version = (int)trim(str_replace(".", "", $version),0);
						$existing_version = (int)trim(str_replace(".", "", $plugin_data->version),0);

						if(compare_versions($existing_version,$required_version) == -1)
						{

							$APP['missing_plugins'][$plugin_id] = (object)[
								"name"=>$plugin_data->name,
				                "id"=>$plugin_id,
				                "version"=>$version,
				                "required"=> $mp_data->required ?? true,
				                "requester_id"=>$json->id,
				                "requester_name"=>$json->name
							];

							//if(DEBUG)
								//dd("Outdated plugin: Missing plugin dependency: ". $plugin_id . " version: ".$version.", Requested by plugin: ". $json->id);

						}
					}else
					{
						$APP['missing_plugins'][$plugin_id] = (object)[
							"name"=>$mp_data->name,
			                "id"=>$plugin_id,
			                "version"=>$version,
			                "required"=> $mp_data->required ?? true,
			                "requester_id"=>$json->id,
			                "requester_name"=>$json->name
						];

						//if(DEBUG)
							//dd("Missing plugin dependency: ". $plugin_id . " version: ".$version.", Requested by plugin: ". $json->id);

					}
				}

			}

			/** load plugin file **/
			if(file_exists($json->index_file))
			{
				$APP['context'] = $json;
				require_once $json->index_file;
				$loaded = true;
			}
		}
	}

	return $loaded;
}


/**
 * @ignore
 * Used by the Core
 */
function valid_route(object $json):bool
{
	if(!empty($json->routes->off) && is_array($json->routes->off))
	{
		if(in_array(page(), $json->routes->off))
			return false;
	}

	if(!empty($json->routes->on) && is_array($json->routes->on))
	{
		if(in_array('all',$json->routes->on))
			return true;

		if(in_array(page(), $json->routes->on))
			return true;
	}

	return false;
}


/**
 * @ignore
 * Used by the Core
 */
function sort_plugins(array $plugins):array
{
	$to_sort = [];
	$sorted  = [];

	foreach ($plugins as $key => $obj) {
		$to_sort[$key] = $obj->index;
	}
	
	asort($to_sort);
	
	foreach ($to_sort as $key => $value) {
		$sorted[] = $plugins[$key];
	}

	return $sorted;
}

/**
 * @ignore
 * Used by the Core
 */
function get_plugin_dir(string $filepath):string
{

	$path = "";

	$basename = basename($filepath);
	$path = str_replace($basename, "", $filepath);

	if(strstr($path, DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR))
	{
		$parts = explode(DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR, $path);
		$parts = explode(DIRECTORY_SEPARATOR, $parts[1]);
		$path = 'plugins' . DIRECTORY_SEPARATOR . $parts[0].DIRECTORY_SEPARATOR;

	}

	return $path;
}

/**
 * @ignore
 * Used by the Core
 */
function get_app_url(bool $local = true): string
{
    static $url = null;

    if ($url !== null) {
        return $url;
    }

    // 1. Config override
    if($local)
    {
	    if (!empty(LOCAL_ROOT))
	        return $url = rtrim(LOCAL_ROOT, '/');
    }else{
    	if (!empty(REMOTE_ROOT))
	        return $url = rtrim(REMOTE_ROOT, '/');
    }

    // 2. Detect scheme (proxy-aware)
    $isHttps =
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        ($_SERVER['SERVER_PORT'] ?? null) == 443 ||
        ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https' ||
        ($_SERVER['HTTP_CF_VISITOR'] ?? '') === '{"scheme":"https"}';

    $scheme = $isHttps ? 'https://' : 'http://';

    // 3. Detect host (proxy-aware)
    $host =
        $_SERVER['HTTP_X_FORWARDED_HOST'] ??
        $_SERVER['HTTP_HOST'] ??
        $_SERVER['SERVER_NAME'] ??
        'localhost';

    // 4. Detect port (safe)
    $port = $_SERVER['SERVER_PORT'] ?? null;

    // If behind proxy, ignore port
    if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $port = null;
    }

    $portPart = '';
    if ($port && !in_array($port, [80, 443])) {
        if (strpos($host, ':') === false) {
            $portPart = ':' . $port;
        }
    }

    // 5. Detect subfolder
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = str_replace('\\', '/', dirname($scriptName));

    // Remove trailing /public if used
    $basePath = rtrim($basePath, '/');

    // Avoid returning "."
    if ($basePath === '.' || $basePath === '/') {
        $basePath = '';
    }

    return $url = rtrim($scheme . $host . $portPart . $basePath, '/');
}

function is_plugin_compatible(object $pluginConfig): bool {
    $required = $pluginConfig->core_requires ?? '>=1.0';
    return version_compare_constraint(VERSION, $required);
}


/**
 * Parse a version string into an array of integer parts (missing parts become 0).
 * e.g. "1.2" -> [1,2,0]; "5" -> [5,0,0]
 */
function parse_version_parts(string $version): array {
    $parts = explode('.', $version);
    $result = [];
    for ($i = 0; $i < 3; $i++) {
        $result[] = isset($parts[$i]) ? (int)$parts[$i] : 0;
    }
    return $result;
}

/**
 * Compare two version strings (e.g., "1.0.1", "4.3.0", "5.1").
 * Returns -1 if v1 < v2, 0 if equal, 1 if v1 > v2.
 */
function compare_versions(string $version1, string $version2): int {
    $parts1 = parse_version_parts($version1);
    $parts2 = parse_version_parts($version2);
    
    for ($i = 0; $i < 3; $i++) {
        if ($parts1[$i] < $parts2[$i]) return -1;
        if ($parts1[$i] > $parts2[$i]) return 1;
    }
    return 0;
}

/**
 * Check if a version satisfies a constraint string.
 * Supported constraints:
 *   - Exact: "1.2.3"
 *   - Range: ">=1.0", ">1.0", "<2.0", "<=", ">="
 *   - Caret: "^1.0"  -> >=1.0.0, <2.0.0
 *   - Tilde: "~1.2.5" -> >=1.2.5, <1.3.0
 *   - Tilde with two parts: "~1.2" -> >=1.2.0, <1.3.0
 *   - Tilde with one part: "~1" -> >=1.0.0, <2.0.0 (same as ^1)
 *
 * @param string $version    The actual version to check (e.g., "1.3.2")
 * @param string $constraint The constraint string (e.g., "^1.0", ">=1.2.0")
 * @return bool
 */
function version_compare_constraint(string $version, string $constraint): bool {
    $constraint = trim($constraint);
    
    // Exact match (no operator)
    if (preg_match('/^\d+(?:\.\d+)*(?:\.\d+)?$/', $constraint)) {
        return compare_versions($version, $constraint) === 0;
    }
    
    // Caret ^ operator
    if (strpos($constraint, '^') === 0) {
        $base = substr($constraint, 1);
        $baseParts = parse_version_parts($base);
        // Major version bump allowed, but not next major
        $lower = $base;
        // Upper bound: next major version (major+1.0.0)
        $upper = ($baseParts[0] + 1) . '.0.0';
        return compare_versions($version, $lower) >= 0 && compare_versions($version, $upper) < 0;
    }
    
    // Tilde ~ operator
    if (strpos($constraint, '~') === 0) {
        $base = substr($constraint, 1);
        $baseParts = parse_version_parts($base);
        // Lower bound is the base itself
        $lower = $base;
        // Determine upper bound based on number of parts in base
        $partsCount = count(explode('.', trim($base, '.')));
        if ($partsCount === 1) {
            // ~1 -> next major
            $upper = ($baseParts[0] + 1) . '.0.0';
        } elseif ($partsCount === 2) {
            // ~1.2 -> next minor (1.3.0)
            $upper = $baseParts[0] . '.' . ($baseParts[1] + 1) . '.0';
        } else {
            // ~1.2.3 -> next patch (1.2.4)
            $upper = $baseParts[0] . '.' . $baseParts[1] . '.' . ($baseParts[2] + 1);
        }
        return compare_versions($version, $lower) >= 0 && compare_versions($version, $upper) < 0;
    }
    
    // Standard operators: >=, >, <=, <
    if (preg_match('/^(>=|>|<=|<)(.+)$/', $constraint, $matches)) {
        $op = $matches[1];
        $target = trim($matches[2]);
        $cmp = compare_versions($version, $target);
        switch ($op) {
            case '>=': return $cmp >= 0;
            case '>':  return $cmp > 0;
            case '<=': return $cmp <= 0;
            case '<':  return $cmp < 0;
        }
    }
    
    // Unknown constraint format – treat as invalid (fail safe)
    return false;
}

/**
 * Validate a plugin's config array (decoded from config.json).
 * Required fields: name, version, core_requires, id.
 * Also validates structure of optional fields (routes, dependencies).
 *
 * @param array $config The plugin config array.
 * @return array Returns an array with keys 'valid' (bool) and 'errors' (array of strings).
 */
function validate_plugin_config(?object $config): array {
    $errors = [];

    if(!$config)
    {
    	$errors[] = "Invalid json file. Check the file for trailing commas or other errors";
    	return [
	        'valid' => empty($errors),
	        'errors' => $errors
	    ];
    }

    // Required fields
    $required = ['name', 'version', 'core_requires', 'id'];
    foreach ($required as $field) {
        if (empty($config->$field)) {
            $errors[] = "Missing required field: '$field'";
        }
    }
    
    // If core_requires is missing, plugin is invalid (as requested)
    if (empty($config->core_requires)) {
        $errors[] = "Plugins must specify 'core_requires' (e.g., '^1.0')";
    }
    
    // Validate version format (should be dot-separated numbers)
    if (!empty($config->version) && !preg_match('/^\d+(?:\.\d+)*$/', $config->version)) {
        $errors[] = "Version must be dot-separated numbers (e.g., '1.0.0')";
    }
    
    // Validate id (alphanumeric + hyphens/underscores, no spaces)
    if (!empty($config->id) && !preg_match('/^[a-zA-Z0-9_-]+$/', $config->id)) {
        $errors[] = "ID must contain only letters, numbers, hyphens and underscores";
    }
    
    // Optional: validate routes structure
    if (isset($config->routes)) {
        if (!is_object($config->routes)) {
            $errors[] = "'routes' must be an object";
        } else {
            // Check 'on' and 'off' arrays if present
            if (isset($config->routes->on) && !is_array($config->routes->on)) {
                $errors[] = "routes.on must be an array";
            }
            if (isset($config->routes->off) && !is_array($config->routes->off)) {
                $errors[] = "routes.off must be an array";
            }
            // Check named routes array if present
            if (isset($config->routes->routes) && is_array($config->routes->routes)) {
                foreach ($config->routes->routes as $idx => $route) {
                    if (!isset($route->method, $route->pattern, $route->name)) {
                        $errors[] = "Route at index $idx missing method, pattern, or name";
                    }
                }
            }
        }
    }
    
    // Validate dependencies structure
    if (isset($config->dependencies)) {
        if (!is_object($config->dependencies)) {
            $errors[] = "'dependencies' must be an object";
        } else {
            foreach ((array)$config->dependencies as $depId => $dep) {
                if (!isset($dep->name) || !isset($dep->version)) {
                    $errors[] = "Dependency '$depId' missing 'name' or 'version'";
                }
                // required is optional, but if present must be boolean
                if (isset($dep->required) && !is_bool($dep->required)) {
                    $errors[] = "Dependency '$depId' 'required' must be boolean";
                }
            }
        }
    }

    // Validate permissions structure
    if (isset($config->permissions)) {
        if (!is_array($config->permissions)) {
            $errors[] = "'permissions' must be an array";
        } else {
            foreach ((array)$config->permissions as $depId => $dep) {
                if (!isset($dep->name) || !isset($dep->slug) || !isset($dep->group) || !isset($dep->description)) {
                    $errors[] = "Permission '$depId' missing 'name' or 'slug' or 'description' or 'group'";
                }
            }
        }
    }
    
    
    // Validate look (if present) – should be a string, not empty
    if (isset($config->look) && !is_string($config->look)) {
        $errors[] = "'look' must be a string";
    }
    
    // Validate active (if present) – should be boolean
    if (isset($config->active) && !is_bool($config->active)) {
        $errors[] = "'active' must be a boolean";
    }
    
    // Validate index (if present) – should be integer
    if (isset($config->index) && !is_int($config->index)) {
        $errors[] = "'index' must be an integer";
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}
 
/**
 * Validate a look's manifest (look.json) and check compatibility with a plugin.
 *
 * @param string $lookFolderPath   Path to the look folder (contains look.json)
 * @param array  $pluginConfig     The parent plugin's config array (must have 'version')
 * @return array                   ['valid' => bool, 'errors' => array, 'data' => array|null]
 */
function validate_look_manifest(string $lookFolderPath, object $pluginConfig): array
{
    $manifestPath = $lookFolderPath . '/look.json';
    if (!file_exists($manifestPath)) {
        return ['valid' => false, 'errors' => ['Missing look.json file'], 'data' => null];
    }

    $json = file_get_contents($manifestPath);
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['valid' => false, 'errors' => ['Invalid JSON: ' . json_last_error_msg()], 'data' => null];
    }

    $errors = [];

    // Required fields
    $required = ['name', 'plugin'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            $errors[] = "Missing required field: '$field'";
        }
    }

    // Optional but recommended: version and plugin_requires
    if (isset($data['version']) && !preg_match('/^\d+(?:\.\d+)*$/', $data['version'])) {
        $errors[] = "Look version must be dot-separated numbers (e.g., '1.0.0')";
    }

    // Check that the 'plugin' field matches the plugin we're validating against
    if (!empty($data['plugin']) && !empty($pluginConfig->id) && $data['plugin'] !== $pluginConfig->id) {
        $errors[] = "Look belongs to plugin '{$data['plugin']}', but the provided plugin is '{$pluginConfig->id}'";
    }

    // Version compatibility with parent plugin
    if (!empty($data['plugin_requires'])) {
        if (empty($pluginConfig->version)) {
            $errors[] = "Parent plugin has no version defined, cannot check compatibility";
        } else {
            if (!version_compare_constraint($pluginConfig->version, $data['plugin_requires'])) {
                $errors[] = "Look requires plugin version {$data['plugin_requires']}, but plugin version is {$pluginConfig->version}";
            }
        }
    } else {
        $errors[] = "Missing 'plugin_requires' field – please specify required plugin version (e.g., '^1.0')";
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'data' => $data
    ];
}

function shortcode_parse_attrs(string $text): array
{
    $attrs = [];

    preg_match_all('/([a-zA-Z0-9_-]+)="([^"]*)"/', $text, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $attrs[$match[1]] = $match[2];
    }

    return $attrs;
}
