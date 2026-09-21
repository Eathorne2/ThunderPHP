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

/**
 * ------------------------------------------------------------------------------
 * Displays a message if any of the listed PHP extensions are not loaded.
 * ------------------------------------------------------------------------------
 */
function check_extensions():void
{
	$extensions = 
	[
		'gd',
		'pdo_mysql'
	];

	$not_loaded = [];
	foreach ($extensions as $ext) {
		if(!extension_loaded($ext))
			$not_loaded[] = $ext;
	}

	if(!empty($not_loaded))
		dd("please load the following extensions in your php.ini file: " . implode(",", $not_loaded));

}

/*
 *---------------------------------------------------------------
 * CHECK FOR REQUIRED EXTENSIONS
 *---------------------------------------------------------------
 */
check_extensions();
