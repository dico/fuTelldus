<?php
	
	require("lib/base.inc.php");
	require("lib/auth.php");


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);
	if (isset($_GET['state'])) $state = clean($_GET['state']);
	if (isset($_GET['btnID'])) $btnID = clean($_GET['btnID']);


	/* exists an device extension file?
	--------------------------------------------------------------------------- */
	$extensionPath = "fuPlugins/device_extensions/".$getID."_action.php";
	$callExtension = 0;
	if (file_exists($extensionPath)) {
	   $callExtension=1;
	   include "".$extensionPath;
	} 

	/* Connect and send to telldus live
	--------------------------------------------------------------------------- */
	require_once 'HTTP/OAuth/Consumer.php';

	$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

	if ($state == "on") {
		$params = array('id'=> $getID);
		
		// call preOnAction
		if ($callExtension == 1) {
			if (preOnAction($getID) == 1) {
				exit();
			}
		} 
		
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/turnOn', $params, 'GET');
		
		// call postOnAction
		if ($callExtension == 1) {
			if (postOnAction($getID) == 1) {
				exit();
			}			
		} 
		//echo "<img style='height:11px' src='images/metro_black/check.png' alt='check' />";
	}

	if ($state == "off") {
		$params = array('id'=> $getID);
		
		// call preOffAction
		if ($callExtension == 1) {
			if (preOffAction($getID) == 1) {
				exit();
			}			
		} 
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/turnOff', $params, 'GET');
		
		// call postOffAction
		if ($callExtension == 1) {
			if (postOffAction($getID) == 1) {
				exit();
			}			
		} 
		
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