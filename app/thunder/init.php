<?php

if(file_exists(FCPATH.'config.php')){
	require FCPATH.'config.php';
}else{
	require FCPATH.'app/core/config-backup.php';
}

define( 'DB_NAME', LOCAL_DB_NAME );
define( 'DB_USER', LOCAL_DB_USER );
define( 'DB_PASSWORD', LOCAL_DB_PASSWORD );
define( 'DB_HOST', LOCAL_DB_HOST );
define( 'DB_DRIVER', LOCAL_DB_DRIVER );
define( 'DB_PORT', LOCAL_DB_PORT );

require_once FCPATH . 'app/core/functions.php';
require_once FCPATH . 'app/thunder/Database.php';
require_once FCPATH . 'app/models/Migration.php';
require_once FCPATH . 'app/thunder/MigrationTracker.php';
require_once FCPATH . 'app/thunder/thunder.php';
