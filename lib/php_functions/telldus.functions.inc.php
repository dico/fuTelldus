<?php
	
	function getDeviceIDList($userid) {
		require_once 'HTTP/OAuth/Consumer.php';

		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		//$params = array();
		$params = array('supportedMethods'=> 1023);
		//$params = array('id'=> $getID);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/devices/list', $params, 'GET');

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);

		$deviceIDs = array();
		foreach($xmldata->device as $deviceData) {		
			$deviceID = trim($deviceData['id']);
			array_push($deviceIDs, $deviceID);
		}
		
		return $deviceIDs;
	}
	
	function getUserIDToDeviceID($devideID) {
		global $mysqli;
		global $db_prefix;
		
		/* Connect to telldus
		--------------------------------------------------------------------------- */
    	$query = "select user_id from ".$db_prefix."devices where device_id = '$devideID'";
  		$result = $mysqli->query($query);
		$userID = "";
		if (mysqli_num_rows($result) == 1) {
			$userID = $result->fetch_assoc()['user_id'];
		}
		return $userID;
	}
	
	function getDeviceState($deviceID) {
		require_once 'HTTP/OAuth/Consumer.php';

		$telldusConf = connectToTelldus(getUserIDToDeviceID($deviceID));
		
		$consumer = new HTTP_OAuth_Consumer($telldusConf['public_key'], $telldusConf['private_key'], $telldusConf['token'], $telldusConf['token_secret']);
		$params = array('supportedMethods'=> 1023, 'id'=> $deviceID);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/info', $params, 'GET');
		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);

		$deviceState = $xmldata->state;
		
		return $deviceState;
	}
	
	function connectToTelldus($userID) {
		global $mysqli;
		global $db_prefix;
		
		/* Connect to telldus
		--------------------------------------------------------------------------- */
    	$query2 = "SELECT * FROM ".$db_prefix."users_telldus_config WHERE user_id='".$userID."'";
  		$result2 = $mysqli->query($query2);
  		$telldusConf = $result2->fetch_array();

  		// Define variables for oAuth
		define('URL', 'http://api.telldus.com'); //https should be used in production!
		define('REQUEST_TOKEN', constant('URL').'/oauth/requestToken');
		define('AUTHORIZE_TOKEN', constant('URL').'/oauth/authorize');
		define('ACCESS_TOKEN', constant('URL').'/oauth/accessToken');
		define('REQUEST_URI', constant('URL').'/xml');

		define('BASE_URL', 'http://'.$_SERVER["SERVER_NAME"].dirname($_SERVER['REQUEST_URI']));	
	
		return $telldusConf;
	}

?>