<?php
	
	if (!isset($_SESSION['fuTelldus_user_loggedin'])) {
		header("Location: ./login/");
		exit();
	}

?>