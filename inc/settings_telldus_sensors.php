<?php
	
	require_once 'HTTP/OAuth/Consumer.php';


	$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

	$params = array();
	$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensors/list', $params, 'GET');


	echo '<pre>';
	echo( htmlentities($response->getBody()));




?>