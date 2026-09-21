<?php

/**
 * @package ThunderPHP
 * @author Your Name <youremail@email.com>
 * @version 1.0.0
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 * Plugin name: Your Plugin Name
 * Description: A Description of what your plugin does
 * 
 **/

namespace {NAMESPACE};

defined('ROOT') || exit('No direct script access allowed');

 /*
 * -------------------------------------------
 * PLUGIN CONSTANTS
 * -------------------------------------------
 * list values repeated througout the plugin
 * to make it easy to change them
 */
set_value([

	'plugin_front_route'	=>'my-plugin',
	'plugin_admin_route'	=>'my-plugin-admin',
	'admin_route'	=>'admin',
	'table'			=>'your_database_table_name',

]);


 /* -----------------------------------------
 * do page processing from here
 * -----------------------------------------*/
add_action('controller',function(){

	$vars = get_value();

	/* load the controller only in the admin section of this plugin */
	if(page() == $vars['admin_route'] && URL(1) == $vars['plugin_admin_route'])
		require plugin_path('controllers/controller.php');
});


/* -----------------------------------------
 * display view files from here
 * -----------------------------------------*/
add_action('view',function(){

	$vars = get_value();

	require plugin_path('looks/main/home.php');
});

