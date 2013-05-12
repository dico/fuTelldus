<?php
	ob_start();
	session_start();

	require_once 'lib/base.inc.php';
	require_once 'HTTP/OAuth/Consumer.php';

	$consumer = new HTTP_OAuth_Consumer($userTelldusConf['public_key'], $userTelldusConf['private_key']);

	$consumer->getRequestToken(constant('REQUEST_TOKEN'), constant('BASE_URL').'/getAccessToken.php');

	$_SESSION['token'] = $consumer->getToken();
	$_SESSION['tokenSecret'] = $consumer->getTokenSecret();

	$url = $consumer->getAuthorizeUrl(constant('AUTHORIZE_TOKEN'));
	header('Location:'.$url);

?>