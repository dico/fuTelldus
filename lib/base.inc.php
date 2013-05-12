<?php
	
	ob_start();
	session_start();



	/* Connect to DB and get config
	--------------------------------------------------------------------------- */
	require ("config.inc.php");




	/* Connect to database
	--------------------------------------------------------------------------- */
	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");






	
	
	
	
	/* Include functions
	--------------------------------------------------------------------------- */
	require ('php_functions/global.functions.inc.php');
	require ('php_functions/datetime.functions.inc.php');

	
	
	
	
	/* Get URL
	--------------------------------------------------------------------------- */
	$currentURL_01 = 'http'. ($_SERVER['HTTPS'] ? 's' : null) .'://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$currentURL = urlencode($currentURL_01);
	
	
	
	
	/* Get userdata
	--------------------------------------------------------------------------- */
	$result = $mysqli->query("SELECT * FROM ".$db_prefix."users WHERE user_id='".$_SESSION['fuTelldus_user_loggedin']."'");
	$user = $result->fetch_array();

	/* Get user telldus-config
	--------------------------------------------------------------------------- */
	$result = $mysqli->query("SELECT * FROM ".$db_prefix."users_telldus_config WHERE user_id='".$user['user_id']."'");
	$userTelldusConf = $result->fetch_array();

	$telldusKeysSetup = false;
	if (!empty($userTelldusConf['public_key']) && !empty($userTelldusConf['private_key']) && !empty($userTelldusConf['token']) && !empty($userTelldusConf['token_secret'])) $telldusKeysSetup = true;




	/* Get page config
	--------------------------------------------------------------------------- */
	$result = $mysqli->query("SELECT * FROM ".$db_prefix."config");
	while ($row = $result->fetch_array()) {
		$config[$row['config_name']] = $row['config_value'];
	}



	/* Set page language
	--------------------------------------------------------------------------- */
	if (empty($user['language'])) $defaultLang = $config['default_language'];
	else $defaultLang = $user['language'];

	include("languages/".$defaultLang.".php");



	/* Telldus
	--------------------------------------------------------------------------- */
	define('PUBLIC_KEY', $userTelldusConf['public_key']);
	define('PRIVATE_KEY', $userTelldusConf['private_key']);
	define('TOKEN', $userTelldusConf['token']);
	define('TOKEN_SECRET', $userTelldusConf['token_secret']);

	define('URL', 'http://api.telldus.com'); //https should be used in production!
	define('REQUEST_TOKEN', constant('URL').'/oauth/requestToken');
	define('AUTHORIZE_TOKEN', constant('URL').'/oauth/authorize');
	define('ACCESS_TOKEN', constant('URL').'/oauth/accessToken');
	define('REQUEST_URI', constant('URL').'/xml');

	define('BASE_URL', 'http://'.$_SERVER["SERVER_NAME"].dirname($_SERVER['REQUEST_URI']));

	define('TELLSTICK_TURNON', 1);
	define('TELLSTICK_TURNOFF', 2);

?>