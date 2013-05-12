<?php
	
	require("lib/base.inc.php");
	require("lib/auth.php");


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);
	if (isset($_GET['state'])) $state = clean($_GET['state']);
	if (isset($_GET['btnID'])) $btnID = clean($_GET['btnID']);




	/* Connect and send to telldus live
	--------------------------------------------------------------------------- */
	require_once 'HTTP/OAuth/Consumer.php';


	$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

	if ($state == "on") {
		$params = array('id'=> $getID);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/turnOn', $params, 'GET');
		//echo "<img style='height:11px' src='images/metro_black/check.png' alt='check' />";
	}

	if ($state == "off") {
		$params = array('id'=> $getID);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/turnOff', $params, 'GET');
		//echo "<img style='height:11px' src='images/metro_black/check.png' alt='check' />";
	}

	
	/*
	echo '<pre>';
		echo( htmlentities($response->getBody()));
	echo '</pre>';

	$xmlString = $response->getBody();
	$xmldata = new SimpleXMLElement($xmlString);
	*/

?>