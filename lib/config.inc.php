<?php
	
	
	/* Connect to database
	--------------------------------------------------------------------------- */
	$host 		= "localhost"; 					// Host name
	$username 	= "USERNAME"; 					// Mysql username
	$password 	= "PASSWORD"; 					// Mysql password
	$db_name 	= "DATABASE_NAME"; 				// Mysql DB

	
	
	// DB Prefix
	$db_prefix = "futelldus_";
	

	
	
	
	/* Timezone
	--------------------------------------------------------------------------- */
	date_default_timezone_set('Europe/Oslo');
	
	
	
	
	
	/* PHP error reporting
	--------------------------------------------------------------------------- */
	error_reporting(E_ALL ^ E_NOTICE);
	
?>