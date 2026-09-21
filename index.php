<?php

/**
 * This file is the main entry point of the application.
 * All requests to the app go through this file.
 * 
 * @package ThunderPHP
 * @version 1.0.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

/*
 *---------------------------------------------------------------
 * IF CONFIG FILE NOT FOUND, LOAD THE BACKUP
 *---------------------------------------------------------------
 */

if(file_exists('config.php')){
	require 'config.php';
}else{
	require 'app/core/config-backup.php';
}

/*
 *---------------------------------------------------------------
 * START SESSION & CHECK PHP VERSION
 *---------------------------------------------------------------
 */
if(USE_SESSIONS)
	session_start();

define('VERSION', '1.0.0');

$minPHPVersion = '8.0';
if(phpversion() < $minPHPVersion)
	die("You need a minimum of PHP version $minPHPVersion to run this app");

/*
 *---------------------------------------------------------------
 * DEFINE APPLICATION CONSTANTS
 *---------------------------------------------------------------
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOTPATH', __DIR__);

/*
 *---------------------------------------------------------------
 * LOAD INIT FILE
 *---------------------------------------------------------------
 * This file contains the class autoloader and other file includes
 * like the functions.php & Database.php files
 */
require 'app'.DS.'core'.DS.'init.php';

/*
 *---------------------------------------------------------------
 * CHECK WHETHER TO DISPLAY ERRORS DEPENDING ON DEBUG MODE
 *---------------------------------------------------------------
 */
DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

/*
 *---------------------------------------------------------------
 * SET APPLICATION LEVEL ARRAYS TO EMPTY
 *---------------------------------------------------------------
 * These arrays are used by app functions to store or retrieve 
 * application wide data e.g user permissions and url params.
 */
$ACTIONS 			= [];
$FILTER 			= [];
$APP['URL'] 		= split_url($_GET['url'] ?? 'home');
$APP['shortcodes'] 	= [];
$APP['roles'] 		= [];
$APP['permissions'] = [];
$APP['missing_plugins'] 	= [];
$APP['outdated_plugins'] 	= [];
$APP['matched_routes'] 		= [];
$APP['matched_routes_params'] = [];
$APP['outdated_plugins'] 	= [];
$APP['route_handled'] 	= false;
$APP['hook_runtime'] = [
	'max_depth' => defined('HOOK_MAX_DEPTH') ? max(1, (int)HOOK_MAX_DEPTH) : 100,
	'history_limit' => defined('HOOK_HISTORY_LIMIT') ? max(1, (int)HOOK_HISTORY_LIMIT) : 250,
	'active_stack' => [],
	'execution_history' => [],
	'last_error' => null,
	'sequence' => 0,
	'history_dropped' => 0,
];
$USER_DATA 				= [];

/*
 *---------------------------------------------------------------
 * LOAD ALL ACTIVE PLUGINS IN PLUGINS FOLDER
 *---------------------------------------------------------------
 * Folders are first collected, then a config.json file is loaded 
 * to determine if a plugin is active or not before its hooks are loaded.
 */
$PLUGINS = \Core\get_plugin_folders();
if(empty($PLUGINS)){

	require 'assets/extras/plugins-not-found.php';
	die();
}

\Core\load_plugins($PLUGINS);

/*
 *---------------------------------------------------------------
 * ADD USER ROLES
 *---------------------------------------------------------------
 * This filter hook is used to add ALL user roles possible.
 * Functions inside plugins register all their possible roles they can give the user.
 */
$APP['roles'] = do_filter('roles',$APP['roles']);

/*
 *---------------------------------------------------------------
 * ADD USER PERMISSIONS
 *---------------------------------------------------------------
 * This filter hook is used to add ALL user permissions possible.
 * Functions inside plugins register all their possible permissions they can give the user.
 */
$APP['permissions'] = do_filter('permissions',$APP['permissions']);

/*
 *---------------------------------------------------------------
 * START THE MAIN APPLICATION
 *---------------------------------------------------------------
 This class contains the main hooks
 */
$app = new \ThunderPHP\App();
$app->index();
