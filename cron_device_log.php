<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	
	
	ob_start();
	session_start();

	/* Connect to database
	--------------------------------------------------------------------------- */
	require("lib/config.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");

	/* Get oAuth class
	--------------------------------------------------------------------------- */
	require_once 'HTTP/OAuth/Consumer.php';



	/* ##################################################################################################################### */
	/* ######################################## SCRIPT RUNS BELOW THIS LINE ################################################ */
	/* ##################################################################################################################### */


	/* Find users
	--------------------------------------------------------------------------- */
	$query = "SELECT * FROM ".$db_prefix."users";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_array()) {


    	/* Connect to telldus
		--------------------------------------------------------------------------- */
    	$query2 = "SELECT * FROM ".$db_prefix."users_telldus_config WHERE user_id='{$row['user_id']}'";
  		$result2 = $mysqli->query($query2);
  		$telldusConf = $result2->fetch_array();

  		// Define variables for oAuth
		define('URL', 'http://api.telldus.com'); //https should be used in production!
		define('REQUEST_TOKEN', constant('URL').'/oauth/requestToken');
		define('AUTHORIZE_TOKEN', constant('URL').'/oauth/authorize');
		define('ACCESS_TOKEN', constant('URL').'/oauth/accessToken');
		define('REQUEST_URI', constant('URL').'/xml');

		define('BASE_URL', 'http://'.$_SERVER["SERVER_NAME"].dirname($_SERVER['REQUEST_URI']));

		
		/* Get devices for user
		--------------------------------------------------------------------------- */
		$consumer = new HTTP_OAuth_Consumer($telldusConf['public_key'], $telldusConf['private_key'], $telldusConf['token'], $telldusConf['token_secret']);
		$params = array('supportedMethods'=> 1023);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/devices/list', $params, 'GET');
		
		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);
		
		$lastUpdated = time();
		
		foreach($xmldata->device as $deviceData) {
			$deviceID = trim($deviceData['id']);
			$name = trim($deviceData['name']);
			$state = trim($deviceData['state']);
			$statevalue = trim($deviceData['statevalue']);
			$methods = trim($deviceData['methods']);
			$type = trim($deviceData['type']);
			$client = trim($deviceData['client']);
			$clientName = trim($deviceData['clientName']);
			$online = trim($deviceData['online']);
			$editable = trim($deviceData['editable']);
			
			$queryDevice = "select status from ".$db_prefix."devices_log where device_id = '".$deviceID."' order by time_updated desc limit 1";
			$result3 = $mysqli->query($queryDevice);
			$row = mysqli_fetch_row($result3);
			$oldstatus = $row[0];
			
			// only update id value changed
			if ($result3->num_rows<=0 or $oldstatus != $state) {
				// Add values to DB
				$queryInsert = "REPLACE INTO ".$db_prefix."devices_log SET 
								device_id='". $deviceID ."', 
								time_updated='". $lastUpdated ."', 
								status='". $state ."'";
				$resultInsert = $mysqli->query($queryInsert);
			}		
		}

    } //end-while-users

?>