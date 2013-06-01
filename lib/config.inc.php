<?php
	
	
	/* Connect to database
	--------------------------------------------------------------------------- */
	$host 		= "localhost"; 					// Host name
	$username 	= "telldus"; 					// Mysql username
	$password 	= "lb77rghf"; 					// Mysql password
	$db_name 	= "telldus_log"; 				// Mysql DB

	
	
	// DB Prefix
	$db_prefix = "futelldus_";
	

	
	
	
	/* Timezone
	--------------------------------------------------------------------------- */
	date_default_timezone_set('Europe/Berlin');
	
	
	
	
	
	/* PHP error reporting
	--------------------------------------------------------------------------- */
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('include_path', '/www/');
	
?>
