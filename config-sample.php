<?php

/*
 *---------------------------------------------------------------
 * ALLOW SESSIONS FOR USER LOGIN
 *---------------------------------------------------------------
 */
define('USE_SESSIONS', true);

/*
 *---------------------------------------------------------------
 * ACTIVATE OR DISABLE ERROR REPORTING
 *---------------------------------------------------------------
 */
define('DEBUG', true);

/*
 *---------------------------------------------------------------
 * WEBSITE NAME AND DESCRIPTION
 *---------------------------------------------------------------
 */
define('APP_NAME', 'Thunder PHP');

define('APP_DESCRIPTION', 'A plugin based PHP Framework');

define('APP_LOGO', '/assets/images/logo.jpg');

/*
 *---------------------------------------------------------------
 * SET ROOT PATH FOR WHEN RUNNING LOCALLY OR ONLINE SERVER
 * e.g http://localhost/myfolder or https://mywebsite.com
 * auto detected if left empty
 *---------------------------------------------------------------
 */
define('LOCAL_ROOT', '');

define('REMOTE_ROOT', '');

/*
 *---------------------------------------------------------------
 * LOCAL DATABASE DETAILS
 *---------------------------------------------------------------
 */
/** The name of your database */
define( 'LOCAL_DB_NAME', 'pluginphp_db' );

/** Database username */
define( 'LOCAL_DB_USER', 'root' );

/** Database password */
define( 'LOCAL_DB_PASSWORD', '' );

/** Database hostname */
define( 'LOCAL_DB_HOST', 'localhost' );

/** Database driver */
define( 'LOCAL_DB_DRIVER', 'mysql' );

/** Database port */
define( 'LOCAL_DB_PORT', '3306' );

/*
 *---------------------------------------------------------------
 * REMOTE DATABASE DETAILS
 *---------------------------------------------------------------
 */
define( 'REMOTE_DB_NAME', 'pluginphp_db' );

define( 'REMOTE_DB_USER', 'root' );

define( 'REMOTE_DB_PASSWORD', '' );

define( 'REMOTE_DB_HOST', 'localhost' );

define( 'REMOTE_DB_DRIVER', 'mysql' );

define( 'REMOTE_DB_PORT', '3306' );

