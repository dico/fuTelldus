<?php


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);





	/* Save usersettings
	--------------------------------------------------------------------------- */
	if ($action == "userAdd") {

		// Get POST data
		$inputEmail = clean($_POST['inputEmail']);
		$admin = clean($_POST['admin']);

		$newPassword = clean($_POST['newPassword']);
		$newCPassword = clean($_POST['newCPassword']);
		$cryptPW = hash('sha256', $newPassword);

		$selectChart = clean($_POST['selectChart']);

		$syncTelldusLists = clean($_POST['syncTelldusLists']);
		$public_key = clean($_POST['public_key']);
		$private_key = clean($_POST['private_key']);
		$token_key = clean($_POST['token_key']);
		$token_secret_key = clean($_POST['token_secret_key']);

	$pushover_key = clean($_POST['pushover_key']);
	
		// Insert user
		$query = "INSERT INTO ".$db_prefix."users SET 
					mail='".$inputEmail."', 
					password='".$cryptPW."', 
					admin='".$admin."', 
					pushover_key='".$pushover_key."' 
					chart_type='".$selectChart."'";
		$result = $mysqli->query($query);

		$userID =  $mysqli->insert_id;

		// Insert telldus config
		$query = "INSERT INTO ".$db_prefix."users_telldus_config SET 
					user_id='".$userID."', 
					sync_from_telldus='".$syncTelldusLists."', 
					public_key='".$public_key."', 
					private_key='".$private_key."', 
					token='".$token_key."',  
					token_secret='".$token_secret_key."'";
		$result = $mysqli->query($query);


		// Redirect
		header("Location: ?page=settings&view=users&msg=01");
		exit();
	}





	/* Save usersettings
	--------------------------------------------------------------------------- */
	if ($action == "userSave") {


		// Get POST data
		$inputEmail = clean($_POST['inputEmail']);
		$language = clean($_POST['language']);
		$admin = clean($_POST['admin']);

		$newPassword = clean($_POST['newPassword']);
		$newCPassword = clean($_POST['newCPassword']);

		$selectChart = clean($_POST['selectChart']);

		$syncTelldusLists = clean($_POST['syncTelldusLists']);
		$public_key = clean($_POST['public_key']);
		$private_key = clean($_POST['private_key']);
		$token_key = clean($_POST['token_key']);
		$token_secret_key = clean($_POST['token_secret_key']);
		
		$pushover_key = clean($_POST['pushover_key']);
		


		// Update userdata
		$query = "UPDATE ".$db_prefix."users SET 
					mail='".$inputEmail."', 
					language='".$language."', 
					chart_type='".$selectChart."',
					pushover_key='".$pushover_key."' 
					WHERE user_id='".$getID."'";
		$result = $mysqli->query($query);

		// Update telldus config
		$query = "REPLACE INTO ".$db_prefix."users_telldus_config SET 
					user_id='".$user['user_id']."', 
					sync_from_telldus='".$syncTelldusLists."', 
					public_key='".$public_key."', 
					private_key='".$private_key."', 
					token='".$token_key."',  
					token_secret='".$token_secret_key."'";
		$result = $mysqli->query($query);



		// Update password
		if (!empty($newPassword)) {

			// Check for password match
			if ($newPassword != $newCPassword || empty($newPassword)) {
				header("Location: ?page=settings&view=user&action=edit&id=$getID&msg=03");
				exit();
			}


			else {
				$newPassword = hash('sha256', $newPassword);

				$query = "UPDATE ".$db_prefix."users SET password='".$newPassword."' WHERE user_id='".$getID."'";
				$result = $mysqli->query($query);
			}

		} //end-if-password


		// Redirect
		header("Location: ?page=settings&view=user&action=edit&id=$getID&msg=01");
		exit();
	}



	/* Delete user
	--------------------------------------------------------------------------- */
	if ($action == "userDelete") {
		
		$query = "DELETE FROM ".$db_prefix."users WHERE user_id='".$getID."'";
		$result = $mysqli->query($query);

		$query = "DELETE FROM ".$db_prefix."users users_telldus_config user_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=settings&view=users&msg=03");
		exit();
	}



	/* Save telldus config
	--------------------------------------------------------------------------- */
	/*
	if ($action == "saveTelldusConfig") {

		// Get POST data
		$public_key = clean($_POST['public_key']);
		$private_key = clean($_POST['private_key']);
		$token_key = clean($_POST['token_key']);
		$token_secret_key = clean($_POST['token_secret_key']);

		if (!empty($public_key)) {
			$query = "UPDATE ".$db_prefix."config SET config_value='".$public_key."' WHERE config_name LIKE 'telldus_public_key'";
			$result = $mysqli->query($query);
		}

		if (!empty($private_key)) {
			$query = "UPDATE ".$db_prefix."config SET config_value='".$private_key."' WHERE config_name LIKE 'telldus_private_key'";
			$result = $mysqli->query($query);
		}

		if (!empty($token_key)) {
			$query = "UPDATE ".$db_prefix."config SET config_value='".$token_key."' WHERE config_name LIKE 'telldus_token'";
			$result = $mysqli->query($query);
		}

		if (!empty($token_secret_key)) {
			$query = "UPDATE ".$db_prefix."config SET config_value='".$token_secret_key."' WHERE config_name LIKE 'telldus_token_secret'";
			$result = $mysqli->query($query);
		}


		// Redirect
		header("Location: ?page=settings&view=telldus_config&msg=01");
		exit();

	}
	*/





	/* Save page config
	--------------------------------------------------------------------------- */
	if ($action == "saveGeneralSettings") {

		// Get POST data
		$pageTitle = clean($_POST['pageTitle']);
		$mail_from = clean($_POST['mail_from']);
		$chart_max_days = clean($_POST['chart_max_days']);
		$language = clean($_POST['language']);
		$pushover_api_token = clean($_POST['pushover_api_token']);


		$query = "UPDATE ".$db_prefix."config SET config_value='".$pageTitle."' WHERE config_name LIKE 'pagetitle'";
		$result = $mysqli->query($query);

		$query = "UPDATE ".$db_prefix."config SET config_value='".$mail_from."' WHERE config_name LIKE 'mail_from'";
		$result = $mysqli->query($query);

		$query = "UPDATE ".$db_prefix."config SET config_value='".$chart_max_days."' WHERE config_name LIKE 'chart_max_days'";
		$result = $mysqli->query($query);

		$query = "UPDATE ".$db_prefix."config SET config_value='".$language."' WHERE config_name LIKE 'public_page_language'";
		$result = $mysqli->query($query);
		
		$query = "UPDATE ".$db_prefix."config SET config_value='".$pushover_api_token."' WHERE config_name LIKE 'pushover_api_token'";
		$result = $mysqli->query($query);


		// Redirect
		header("Location: ?page=settings&view=general&msg=01");
		exit();

	}



	/* Add XML-shared sensor
	--------------------------------------------------------------------------- */
	if ($action == "addSensorFromXML") {
		// Get POST data
		$description = clean($_POST['description']);
		$xml_url = clean($_POST['xml_url']);


		// Insert telldus config
		$query = "INSERT INTO ".$db_prefix."sensors_shared SET 
					user_id='".$user['user_id']."', 
					description='".$description."', 
					url='".$xml_url."', 
					disable='". 0 ."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=settings&view=share&msg=01");
		exit();
	}


	/* Delete XML-shared sensor
	--------------------------------------------------------------------------- */
	if ($action == "deleteSensorFromXML") {
		$query = "DELETE FROM ".$db_prefix."sensors_shared WHERE user_id='".$user['user_id']."' AND share_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=settings&view=share&msg=02");
		exit();
	}


	/* Put on main
	--------------------------------------------------------------------------- */
	if ($action == "putOnMainSensorFromXML") {

		$getCurrentValue = getField("show_in_main", "".$db_prefix."sensors_shared", "WHERE share_id='".$getID."'");

		if ($getCurrentValue == 0) {
			$query = "UPDATE ".$db_prefix."sensors_shared SET show_in_main='1' WHERE user_id='".$user['user_id']."' AND share_id='".$getID."'";
			$result = $mysqli->query($query);
		} else {
			$query = "UPDATE ".$db_prefix."sensors_shared SET show_in_main='0' WHERE user_id='".$user['user_id']."' AND share_id='".$getID."'";
			$result = $mysqli->query($query);
		}

		// Redirect
		header("Location: ?page=settings&view=share&msg=03");
		exit();
	}


	/* Disable XML-shared sensor
	--------------------------------------------------------------------------- */
	if ($action == "disableSensorFromXML") {

		$getCurrentValue = getField("disable", "".$db_prefix."sensors_shared", "WHERE share_id='".$getID."'");

		if ($getCurrentValue == 0) {
			$query = "UPDATE ".$db_prefix."sensors_shared SET disable='1' WHERE user_id='".$user['user_id']."' AND share_id='".$getID."'";
			$result = $mysqli->query($query);
		} else {
			$query = "UPDATE ".$db_prefix."sensors_shared SET disable='0' WHERE user_id='".$user['user_id']."' AND share_id='".$getID."'";
			$result = $mysqli->query($query);
		}

		// Redirect
		header("Location: ?page=settings&view=share&msg=03");
		exit();
	}


	/* Add schedule
	--------------------------------------------------------------------------- */
	if ($action == "addSchedule") {
		
		// Get POST data
		$sensorID = clean($_POST['sensorID']);
		$direction = clean($_POST['direction']);
		$warningValue = clean($_POST['warningValue']);
		$type = clean($_POST['type']);
		$repeat = clean($_POST['repeat']);
		$sendTo_mail = clean($_POST['sendTo_mail']);
		$send_to_pushover = clean($_POST['sendTo_pushover']);
		$mail_primary = clean($_POST['mail_primary']);
		$mail_secondary = clean($_POST['mail_secondary']);

		$deviceID = clean($_POST['deviceID']);
		$device_action = clean($_POST['device_action']);


		// Insert telldus config
		$query = "INSERT INTO ".$db_prefix."schedule SET 
					user_id='".$user['user_id']."', 
					sensor_id='".$sensorID."', 
					direction='".$direction."', 
					warning_value='".$warningValue."', 
					type='".$type."', 
					repeat_alert='".$repeat."', 
					device='".$deviceID."', 
					device_set_state='".$device_action."', 
					send_to_mail='". $sendTo_mail ."',
					send_to_pushover='".$send_to_pushover."', 
					notification_mail_primary='". $mail_primary ."',
					notification_mail_secondary='". $mail_secondary ."'";
		$result = $mysqli->query($query);
		

		// Redirect
		header("Location: ?page=settings&view=schedule&msg=01");
		exit();
	}



	/* Delete schedule
	--------------------------------------------------------------------------- */
	if ($action == "updateSchedule") {

		// Get POST data
		$sensorID = clean($_POST['sensorID']);
		$direction = clean($_POST['direction']);
		$warningValue = clean($_POST['warningValue']);
		$type = clean($_POST['type']);
		$repeat = clean($_POST['repeat']);
		$sendTo_mail = clean($_POST['sendTo_mail']);
		$send_to_pushover = clean($_POST['sendTo_pushover']);
		$mail_primary = clean($_POST['mail_primary']);
		$mail_secondary = clean($_POST['mail_secondary']);

		$deviceID = clean($_POST['deviceID']);
		$device_action = clean($_POST['device_action']);


		// Update userdata
		$query = "UPDATE ".$db_prefix."schedule SET 
					sensor_id='".$sensorID."', 
					direction='".$direction."', 
					warning_value='".$warningValue."', 
					type='".$type."', 
					repeat_alert='".$repeat."', 
					device='".$deviceID."', 
					device_set_state='".$device_action."', 
					send_to_mail='".$sendTo_mail."', 
					send_to_pushover='".$send_to_pushover."', 
					notification_mail_primary='".$mail_primary."', 
					notification_mail_secondary='".$mail_secondary."' 
					WHERE notification_id='".$getID."'";
		$result = $mysqli->query($query);


		// Redirect
		header("Location: ?page=settings&view=schedule&msg=01");
		exit();
	}



	/* Delete schedule
	--------------------------------------------------------------------------- */
	if ($action == "deleteSchedule") {

		$query = "DELETE FROM ".$db_prefix."schedule WHERE user_id='".$user['user_id']."' AND notification_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=settings&view=schedule&msg=02");
		exit();
	}


		/* add virtual sensor
	--------------------------------------------------------------------------- */
	if ($action == "addVirtualSensor") {
			// Get POST data
		$virtualsensor_description = clean($_POST['virtualsensor_description']);
		$virtualsensor_type = clean($_POST['virtualsensor_type']);
			
		$query = "INSERT INTO ".$db_prefix."virtual_sensors SET
			user_id='".$user['user_id']."', 
			description='".$virtualsensor_description."', 
			sensor_type='".$virtualsensor_type."', 
			last_update = '".time()."',
			online = '1',
			monitoring = '0',
			show_in_main = '0'";
		$result = $mysqli->query($query);
		$vSensorId = $mysqli->insert_id;
		
		// for every para starts with virtualsensor_value_
		foreach (array_keys($_POST)  as $postkey) {
			$para_prefix = "virtualsensor_value_";
			if(strpos($postkey, $para_prefix) === 0){
				$configId = substr($postkey, strlen($para_prefix));
				$configValue = clean($_POST[$postkey]);;
				$queryInsert2 = "INSERT INTO ".$db_prefix."virtual_sensors_config SET
					sensor_id='".$vSensorId."', 
					value='".$configValue."', 
					config_id='".$configId."'";
				$resultInsert2 = $mysqli->query($queryInsert2); 
			}
		}

		// Redirect
		header("Location: ?page=settings&view=virtualsensors&msg=01");
		exit();
	}
	
		/* update virtual sensor
	--------------------------------------------------------------------------- */
	if ($action == "updateVirtualSensor") {
			// Get POST data
		$virtualsensor_description = clean($_POST['virtualsensor_description']);

		$query = "UPDATE ".$db_prefix."virtual_sensors SET description='".$virtualsensor_description."' where id = '".$getID."'";
		$result = $mysqli->query($query);
	
		// for every para starts with virtualsensor_value_
		foreach (array_keys($_POST)  as $postkey) {
			$para_prefix = "virtualsensor_value_";
			if(strpos($postkey, $para_prefix) === 0){
				$configId = substr($postkey, strlen($para_prefix));
				$configValue = clean($_POST[$postkey]);;
				$queryInsert2 = "UPDATE ".$db_prefix."virtual_sensors_config SET
					value='".$configValue."' 
					WHERE sensor_id='".$getID."' 
					AND config_id='".$configId."'";
				$resultInsert2 = $mysqli->query($queryInsert2); 
			}
		}
	
		// Redirect
		header("Location: ?page=settings&view=virtualsensors&msg=04");
		exit();
	}
	
		/* Delete schedule
	--------------------------------------------------------------------------- */
	if ($action == "deleteVirtualSensor") {
		$query = "DELETE FROM ".$db_prefix."virtual_sensors WHERE user_id='".$user['user_id']."' AND id='".$getID."'";
		$result = $mysqli->query($query);
		
		$query2 = "DELETE FROM ".$db_prefix."virtual_sensors_config WHERE sensor_id='".$getID."'";
		$result2 = $mysqli->query($query2);
		
		// Redirect
		header("Location: ?page=settings&view=virtualsensors&msg=02");
		exit();
	}
	
	/* add virtual sensor type
	--------------------------------------------------------------------------- */
/*	if ($action == "addVirtualSensorType") {
			// Get POST data
		$type_description = clean($_POST['type_description']);
		$type_image = clean($_POST['type_image']);
		$type_php = clean($_POST['type_php']);		
			
		$query = "INSERT INTO ".$db_prefix."virtual_sensors_types SET
			type_description='".$type_description."',
			phpfile='".$type_php."',
			icon='".$type_image."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=settings&view=virtualsensors_types&msg=01");
		exit();
	}	*/
	
	/* delete virtual sensor type
	--------------------------------------------------------------------------- */
/*	if ($action == "deleteVirtualSensorType") {
		$query = "DELETE FROM ".$db_prefix."virtual_sensors_types WHERE type_int='".$getID."'";
		$result = $mysqli->query($query);
		
		//echo $query;
		
		$query2 = "DELETE FROM ".$db_prefix."virtual_sensors_types_config WHERE type_int='".$getID."'";
		$result2 = $mysqli->query($query2);

		// Redirect
		header("Location: ?page=settings&view=virtualsensors_types&msg=01");
		exit();
	}	*/
	
	/* Put on main
	--------------------------------------------------------------------------- */
	if ($action == "putOnMainVirtualSensor") {

		$getCurrentValue = getField("show_in_main", "".$db_prefix."virtual_sensors", "WHERE id='".$getID."'");

		if ($getCurrentValue == 0) {
			$query = "UPDATE ".$db_prefix."virtual_sensors SET show_in_main='1' WHERE user_id='".$user['user_id']."' AND id='".$getID."'";
			$result = $mysqli->query($query);
		} else {
			$query = "UPDATE ".$db_prefix."virtual_sensors SET show_in_main='0' WHERE user_id='".$user['user_id']."' AND id='".$getID."'";
			$result = $mysqli->query($query);
		}

		// Redirect
		header("Location: ?page=settings&view=virtualsensors&msg=03");
		exit();
	}

	/*
	--------------------------------------------------------------------------- */
	if ($action == "setMonitoring") {
		$getCurrentValue = getField("monitoring", "".$db_prefix."virtual_sensors", "WHERE id='".$getID."'");

		if ($getCurrentValue == 0) {
			$query = "UPDATE ".$db_prefix."virtual_sensors SET monitoring='1' WHERE user_id='".$user['user_id']."' AND id='".$getID."'";
			$result = $mysqli->query($query);
		} else {
			$query = "UPDATE ".$db_prefix."virtual_sensors SET monitoring='0' WHERE user_id='".$user['user_id']."' AND id='".$getID."'";
			$result = $mysqli->query($query);
		}

		// Redirect
		header("Location: ?page=settings&view=virtualsensors&msg=03");
		exit();
	}
	
	/*
	--------------------------------------------------------------------------- */
	if ($action == "setOnline") {
		$getCurrentValue = getField("online", "".$db_prefix."virtual_sensors", "WHERE id='".$getID."'");

		if ($getCurrentValue == 0) {
			$query = "UPDATE ".$db_prefix."virtual_sensors SET online='1' WHERE user_id='".$user['user_id']."' AND id='".$getID."'";
			$result = $mysqli->query($query);
		} else {
			$query = "UPDATE ".$db_prefix."virtual_sensors SET online='0' WHERE user_id='".$user['user_id']."' AND id='".$getID."'";
			$result = $mysqli->query($query);
		}

		// Redirect
		header("Location: ?page=settings&view=virtualsensors&msg=03");
		exit();
	}

	
	if ($action == "sendTestNotification") {
		$pushover_key = clean($_GET['pushover_key']);
		$subject = clean($_GET['subject']);
		$message = clean($_GET['message']);

		sendNotification($pushover_key, $subject, $message);
		
		// Redirect		
		header("Location: ?page=settings&view=user&action=edit&id=$getID&msg=04");
		exit();
	}

?>