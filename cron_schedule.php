<?php

	/* Connect to database
	--------------------------------------------------------------------------- */
	require("./lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");










	/* ##################################################################################################################### */
	/* ######################################## SCRIPT RUNS BELOW THIS LINE ################################################ */
	/* ##################################################################################################################### */



	$query = "SELECT * 
			  FROM ".$db_prefix."schedule 
			  INNER JOIN ".$db_prefix."sensors ON ".$db_prefix."schedule.sensor_id = ".$db_prefix."sensors.sensor_id
			  INNER JOIN ".$db_prefix."users ON ".$db_prefix."schedule.user_id = ".$db_prefix."users.user_id
			  ";
    $result = $mysqli->query($query);
    $numRows = $result->num_rows;

    if ($numRows > 0) {
    	while($row = $result->fetch_array()) {

    		$scheduleRun = false;	// Set default as false


    		/* PRINT DATA FOR DEBUG
    		echo "Notification ID: {$row['notification_id']} <br />";
    		echo "Sensorname: {$row['name']} <br />";
    		echo "Sensor ID: {$row['sensor_id']} <br />";
    		echo "Device ID: {$row['device']} <br />";
    		*/


    		/* Get last sensor values logged
			--------------------------------------------------------------------------- */

    		// Check only for temp values newer than
    		$newerThan = (time() - 3600); // 3600 = 1 hour

    		$queryTemp = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row['sensor_id']}' AND time_updated < '$newerThan' ORDER BY time_updated DESC LIMIT 1";
    		$resultTemp = $mysqli->query($queryTemp);
    		$tempData = $resultTemp->fetch_array();





	    	/* Check for warning last sent
			--------------------------------------------------------------------------- */
			$timeSinceLastWarning = (time() - $row['last_warning']);


			// Check if notification-repeat-state
			if (($row['repeat_alert'] * 60) < $timeSinceLastWarning) $repeatNotification = true;
			else $repeatNotification = false;





			/* Check for device action
			--------------------------------------------------------------------------- */

			/* Don't run if last action was less than SET-secounds
			 *   This is to prevent sending data every time
			 * 15 minutes => 900 sec
			 * 30 minutes => 1800 sec
			 * 60 minutes => 3600 sec
			*/
			
			if (900 < $timeSinceLastWarning) $repeatDeviceState = true;
			else $repeatDeviceState = false;

			if (empty($row['device'])) $repeatDeviceState = false; // Check for device
			
			// Connect to Telldus Live
			else {
				require_once 'HTTP/OAuth/Consumer.php';
				$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

				if ($row['device_set_state'] == 1) $deviceStateApiParameter = "turnOn";
				else $deviceStateApiParameter = "turnOff";
			}
			









	    	/* Check for LESS THAN
			--------------------------------------------------------------------------- */
	    	if ($row['direction'] == "less") {

	    		// CELSIUS
	    		if ($row['type'] == "celsius") {
			    	if ($tempData['temp_value'] < $row['warning_value']) {
			    		//echo "<b>WARNING: Temperature is less</b><br />{$lang['Notification mail low temperature']}<br /><br />";

			    		// Send notification
			    		if ($repeatNotification) {
			    			$scheduleRun = true;

				    		// Get and replace variables in mail message
				    		$mailSubject = "{$config['pagetitle']}: {$lang['Warning']}: {$lang['Low']} {$lang['Temperature']}";
				    		$mailMessage = "{$lang['Notification mail low temperature']}";

				    		$mailMessage = str_replace("%%sensor%%", $row['name'], $mailMessage);
							$mailMessage = str_replace("%%value%%", $tempData['temp_value'], $mailMessage);
						}


						// Send state to device
						if ($repeatDeviceState) {
							$scheduleRun = true;

							$params = array('id'=> $row['device']);
							$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/'. $deviceStateApiParameter, $params, 'GET');
						}

			    	}
			    }

			    // HUMIDITY
			    elseif ($row['type'] == "humidity") {
			    	if ($tempData['humidity_value'] < $row['warning_value']) {
			    		//echo "<b>WARNING: Humidity is less</b><br />";

			    		if ($repeatNotification) {
			    			$scheduleRun = true;

				    		// Get and replace variables in mail message
				    		$mailSubject = "{$config['pagetitle']}: {$lang['Warning']}: {$lang['Low']} {$lang['Humidity']}";
				    		$mailMessage = "{$lang['Notification mail low humidity']}";

				    		$mailMessage = str_replace("%%sensor%%", $row['name'], $mailMessage);
							$mailMessage = str_replace("%%value%%", $tempData['humidity_value'], $mailMessage);
						}

						// Send state to device
						if ($repeatDeviceState) {
							$scheduleRun = true;

							$params = array('id'=> $row['device']);
							$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/'. $deviceStateApiParameter, $params, 'GET');
						}

			    	}
			    }
		    }


		    /* Check for MORE THAN
			--------------------------------------------------------------------------- */
	    	if ($row['direction'] == "more") {

	    		// CELSIUS
	    		if ($row['type'] == "celsius") {
			    	if ($tempData['temp_value'] > $row['warning_value']) {
			    		//echo "<b>WARNING: Temperature is more</b><br />{$lang['Notification mail high temperature']}<br /><br />";
			    		
			    		if ($repeatNotification) {
			    			$scheduleRun = true;

				    		// Get and replace variables in mail message
				    		$mailSubject = "{$config['pagetitle']}: {$lang['Warning']}: {$lang['High']} {$lang['Temperature']}";
				    		$mailMessage = "{$lang['Notification mail high temperature']}";

				    		$mailMessage = str_replace("%%sensor%%", $row['name'], $mailMessage);
							$mailMessage = str_replace("%%value%%", $tempData['temp_value'], $mailMessage);
						}

						// Send state to device
						if ($repeatDeviceState) {
							$scheduleRun = true;

							$params = array('id'=> $row['device']);
							$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/'. $deviceStateApiParameter, $params, 'GET');
						}

			    	}
			    }

			    // HUMIDITY
			    elseif ($row['type'] == "humidity") {
			    	if ($tempData['humidity_value'] > $row['warning_value']) {
			    		//echo "<b>WARNING: Humidity is more</b><br />";

			    		if ($repeatNotification) {
			    			$scheduleRun = true;

				    		// Get and replace variables in mail message
				    		$mailSubject = "{$config['pagetitle']}: {$lang['Warning']}: {$lang['High']} {$lang['Humidity']}";
				    		$mailMessage = "{$lang['Notification mail high humidity']}";

				    		$mailMessage = str_replace("%%sensor%%", $row['name'], $mailMessage);
							$mailMessage = str_replace("%%value%%", $tempData['humidity_value'], $mailMessage);
						}

						// Send state to device
						if ($repeatDeviceState) {
							$scheduleRun = true;

							$params = array('id'=> $row['device']);
							$response = $consumer->sendRequest(constant('REQUEST_URI').'/device/'. $deviceStateApiParameter, $params, 'GET');
						}

			    	}
			    }
		    }



		    /* IF warning = true
			--------------------------------------------------------------------------- */
		    if ($scheduleRun) {
			    
		    	// Update sent timestamp
			    $queryTimestamp = "UPDATE ".$db_prefix."schedule SET last_warning='".time()."' WHERE notification_id='".$row['notification_id']."'";
				$resultTimestamp = $mysqli->query($queryTimestamp);
				

				// Send mail
				if ($row['send_to_mail'] == 1 && $repeatNotification == true) {

					// Use mail-function in /lib/php_functions/global.functions.inc.php
					if (!empty($row['notification_mail_primary'])) sendMail($row['notification_mail_primary'], $mailSubject, $mailMessage);
					if (!empty($row['notification_mail_secondary'])) sendMail($row['notification_mail_secondary'], $mailSubject, $mailMessage);
				}
			}



				


    	} //end-while
    } //end-numRows


?>