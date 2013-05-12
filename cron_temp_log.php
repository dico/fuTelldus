<?php
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



		/* Get sensors for user that should me monitored
		--------------------------------------------------------------------------- */
    	$query2 = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$row['user_id']}' AND monitoring='1'";
  		$result2 = $mysqli->query($query2);
  		while ($sensor = $result2->fetch_array()) {

	    	/* Get sensordata
			--------------------------------------------------------------------------- */
			$consumer = new HTTP_OAuth_Consumer($telldusConf['public_key'], $telldusConf['private_key'], $telldusConf['token'], $telldusConf['token_secret']);
			$params = array('id'=> $sensor['sensor_id']);
			$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensor/info', $params, 'GET');


			// Get XML and create array with SimpleXMLElement
			$xmlData = $response->getBody();
			$xml = new SimpleXMLElement($xmlData);


			// Trim values
			$lastUpdated 	= trim($xml->lastUpdated);
			$tempValue 		= trim($xml->data[0]['value']);
			$humidityValue 	= trim($xml->data[1]['value']);

			// Add values to DB
			$queryInsert = "REPLACE INTO ".$db_prefix."sensors_log SET 
							sensor_id='". $sensor['sensor_id'] ."', 
							time_updated='". $lastUpdated ."', 
							temp_value='". $tempValue ."', 
							humidity_value='". $humidityValue ."'";
			$resultInsert = $mysqli->query($queryInsert);
	    




	    } //end-sensorlist
    } //end-while-users

?>