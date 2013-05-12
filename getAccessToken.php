<?php

ob_start();
session_start();

require_once 'lib/base.inc.php';
require_once 'HTTP/OAuth/Consumer.php';

$consumer = new HTTP_OAuth_Consumer($userTelldusConf['public_key'], $userTelldusConf['private_key'], $_SESSION['token'], $_SESSION['tokenSecret']);

try {
	$consumer->getAccessToken(constant('ACCESS_TOKEN'));
	
	$_SESSION['accessToken'] = $consumer->getToken();
	$_SESSION['accessTokenSecret'] = $consumer->getTokenSecret();

	header('Location:index.php');
} catch (Exception $e) {
	?>
		<p>Authorization failed!</p>
		<p><a href="index.php">Go back</a></p>
	<?php
}

?>
