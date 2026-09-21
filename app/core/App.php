<?php
/**
 * This file is part of the ThunderPHP Framework.
 * 
 * @package ThunderPHP
 * @version 1.0.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace ThunderPHP;

/**
 * The App class runs all the main hooks for the application
 */
class App
{
	/**
	 * Runs the main application
	 * 
	 */
	public function index():void
	{
 		/*
		 *---------------------------------------------------------------
		 * RUN MAIN HOOKS
		 *---------------------------------------------------------------
		 * PLUGINS CAN INSERT THEIR CODE AT ANY POINT IN THE APP USING HOOKS
		 *---------------------------------------------------------------
		 */
 		ob_start();
 		// This hook is good for high priority actions like authentication 
 		// & stuff that should run before any page is processed
		do_action('before_controller');

		//Main controller hook. Used to process forms or load from database
		do_action('controller');

		//just incase something needs handling after controllers run
		do_action('after_controller');

		/* Use this hook for headers or menus*/
		do_action('before_view');

		/* Use this hook to load page views */
		do_action('view');

		/*
		 *---------------------------------------------------------------
		 * LOAD THE 404 PAGE IF NO ROUTE MATCHED
		 *---------------------------------------------------------------
		 */
		global $APP;
		if(!$APP['route_handled'])
		{
			if(page() == '404'){
				
				require '404.php';
			}else{
				do_action('before_404_redirect');
				redirect('404');
			}
		}

		/* Use this hook for stuff like footer or scripts */
		do_action('after_view');

	}
}