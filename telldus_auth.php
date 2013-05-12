<?php
	ob_start();
	session_start();
	
	require_once "./lib/packages/openid/openid.php";


	$lightOpenID = new LightOpenID();
	$lightOpenID->identity = 'http://login.telldus.com';
	$lightOpenID->required = array('contact/email', 'namePerson');

	if ($_GET['openid_mode'] == 'id_res') {

		if ($lightOpenID->validate()) {
			$data = $lightOpenID->getAttributes();
			$_SESSION['telldus_id'] = $_GET['openid_identity'];
			$_SESSION['email'] = $data['contact/email'];
			$_SESSION['fullname'] = $data['namePerson'];
			$_SESSION['loggedin'] = true;

			echo $_SESSION['email'];
			header('Location: index.php?page=settings&view=telldus');
		} else {
			echo "Validation failed! <a href='index.php'>Go back</a>";
		}
	} else if ($_GET['openid_mode'] == 'cancel') {
		echo "Login canceled. <a href='index.php'>Go back</a>";
		
	} else {
		header('Location: '. $lightOpenID->authUrl(true) );
	}

?>