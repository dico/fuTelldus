<?php
	ob_start();
	session_start();

	unset($_SESSION['fuTelldus_user_loggedin']);

	setcookie("fuTelldus_user_loggedin", "", time()-3600);

	header("Location: index.php?msg=02");
	exit();
?>