<?php
	
	require("../lib/base.inc.php");




	
	// Set error to false
	$error = false;
	
	
	// Get data from form
	if (isset($_POST['mail'])) 
		$mail = clean($_POST['mail']);
	
	if (isset($_POST['password'])) 
		$password = clean($_POST['password']);
		
	
	if (isset($_POST['remember'])) 
		$remember = clean($_POST['remember']);	
	
	if (isset($_POST['uniq'])) 
		$uniq = clean($_POST['uniq']);
	
	

	// Check if form is filled out
	if (empty($_POST['mail']) || empty($_POST['password'])) {
		$error = true;
	}
	
	
	// Hash the password
	$cryptPW = hash('sha256', $password);
	
	// Get secure form ID and check it
	$hashSecureFormLogin = hash('sha256', $_SESSION['secure_fuTelldus_loginForm']);
	
	if ($uniq != $hashSecureFormLogin) {
		$error = true;
	}
	
	echo "mail: $mail <br />";
	echo "password: $password <br />";
	echo "cryptPW: $cryptPW <br />";
	
	
	// Redirect if an error is found
	if ($error) {
		header("Location: index.php?msg=01&mail=".$_POST['mail']."&error=invalidSecureLogin");
		exit();
	}
	
	
	
	// Start logging in if all is good up to now!
	else {
		$query = "SELECT * FROM ".$db_prefix."users WHERE mail='$mail' AND password='".$cryptPW."' LIMIT 1";
		$result = $mysqli->query($query);
		$numRowsLogin = $result->num_rows;
		

		if($result && $numRowsLogin>0) {
			
			// Regenerate session ID to prevent session fixation attacks
			session_regenerate_id();
			
			// Login OK -> Storing sessions
			$userLogin = $result->fetch_array();
			$_SESSION['fuTelldus_user_loggedin'] = $userLogin['user_id'];
			

			// Set remember me
			if ($remember == 1) {
				$expire=time()+60*60*24*365;
				setcookie("fuTelldus_user_loggedin", $userLogin['user_id'], $expire);
			}


			session_write_close();
			
			header("Location: ../index.php");
			exit();
		}
		
		// Login error
		else {
			header("Location: index.php?msg=01&mail=".$_POST['mail']."&error=noUser");
			exit();	
		}
	}
	
	
	// If scripts failed along the way -> Redirect to login again
	header("Location: index.php?msg=01&mail=".$_POST['mail']."&error=noAction");
	exit();
	
	
	unset($_SESSION['secure_fuTelldus_loginForm']);	
?>