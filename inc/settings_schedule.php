<?php


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);



	/* Set parameters
	--------------------------------------------------------------------------- */
	if ($action == "edit") {
		$query = "SELECT * FROM ".$db_prefix."schedule WHERE notification_id='$getID' LIMIT 1";
	    $result = $mysqli->query($query);
	    $row = $result->fetch_array();

	    $sensorID = $row['sensor_id'];
	    $direction = $row['direction'];
	    $warning_value = $row['warning_value'];
	    $type = $row['type'];
	    $repeat_alert = $row['repeat_alert'];
	    $device = $row['device'];
	    $device_set_state = $row['device_set_state'];
	    $send_to_mail = $row['send_to_mail'];
	    $mail_primary = $row['notification_mail_primary'];
	    $mail_secondary = $row['notification_mail_secondary'];

	} else {
		$warning_value = 30;
		$repeat_alert = 60;
		$send_to_mail = 1;
		$mail_primary = $user['mail'];
		$mail_secondary = "";
	}



	
	echo "<h4>".$lang['Schedule']."</h4>";


	echo "<div style='float:right; margin-top:-35px; margin-right:15px;'>";
		echo "<a class='btn btn-success' href='?page=settings&view=schedule&action=add'>{$lang['Create new']}</a>";
	echo "</div>";


	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-info'>".$lang['Data saved']."</div>";
		elseif ($_GET['msg'] == 02) echo "<div class='alert alert-error'>".$lang['Deleted']."</div>";
	}







	/* Form
	--------------------------------------------------------------------------- */
	if ($action == "add" || $action == "edit") {

		if ($action == "edit") {
			echo "<div class='alert'>";
			echo "<form action='?page=settings_exec&action=updateSchedule&id=$getID' method='POST'>";
		} else {
			echo "<div class='well'>";
			echo "<form action='?page=settings_exec&action=addSchedule' method='POST'>";
		}


			echo "<table width='100%'>";

				// Sensor
				echo "<tr>";
					echo "<td>{$lang['Sensor']}</td>";
					echo "<td>";
						$query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='".$user['user_id']."' AND monitoring='1' ORDER BY name ASC LIMIT 100";
		   				$result = $mysqli->query($query);

		   				echo "<select style='width:180px;' name='sensorID'>";
		   				while ($row = $result->fetch_array()) {
		   					if ($sensorID == $row['sensor_id'])
		   						echo "<option value='{$row['sensor_id']}' selected='selected'>{$row['sensor_id']}: {$row['name']}</option>";

		   					else
		   						echo "<option value='{$row['sensor_id']}'>{$row['sensor_id']}: {$row['name']}</option>";
		   				}
		   				echo "</select>";
					echo "</td>";
				echo "</tr>";


				// Higher / lower
				echo "<tr>";
					echo "<td>{$lang['Type']}</td>";
					echo "<td>";

						echo "<select style='width:120px; margin-right:5px;' name='direction'>";
							if ($direction == "less") $directionSelectedLess = "selected='selected'";
							if ($direction == "more") $directionSelectedMore = "selected='selected'";

							echo "<option value='more' $directionSelectedMore>{$lang['Higher than']}</option>";
							echo "<option value='less' $directionSelectedLess>{$lang['Lower than']}</option>";
						echo "</select>";

						echo "<input style='width:30px; margin-right:5px;' type='text' name='warningValue' id='warningValue' value='$warning_value' />";

						echo "<select style='width:120px;' name='type'>";
							if ($type == "celsius") $typeSelectedCelsius = "selected='selected'";
							if ($type == "humidity") $typeSelectedHumidity = "selected='selected'";

							echo "<option value='celsius' $typeSelectedCelsius>&deg; ({$lang['Celsius']})</option>";
							echo "<option value='humidity' $typeSelectedHumidity>% ({$lang['Humidity']})</option>";
						echo "</select>";

					echo "</td>";
				echo "</tr>";




				


				echo "<tr><td colspan='2'><br /></td></tr>"; // Space






				echo "<tr><td colspan='2'><h5>{$lang['Device action']}</h5></td></tr>"; // Headline


				// Device
				echo "<tr>";
					echo "<td>{$lang['Devices']}</td>";
					echo "<td>";
						$query = "SELECT * FROM ".$db_prefix."devices WHERE user_id='".$user['user_id']."' ORDER BY name ASC LIMIT 100";
		   				$result = $mysqli->query($query);

		   				echo "<select style='width:250px;' name='deviceID'>";
		   					echo "<option value=''>-- {$lang['No device action']}</option>";

			   				while ($row = $result->fetch_array()) {
			   					if ($device == $row['device_id'])
			   						echo "<option value='{$row['device_id']}' selected='selected'>{$row['device_id']}: {$row['name']}</option>";

			   					else
			   						echo "<option value='{$row['device_id']}'>{$row['device_id']}: {$row['name']}</option>";
			   				}
		   				echo "</select>";


		   				echo "<select style='width:70px; margin-left:10px;' name='device_action'>";
		   					echo "<option value='1'>{$lang['On']}</option>";
		   					echo "<option value='0'>{$lang['Off']}</option>";
		   				echo "</select>";

					echo "</td>";
				echo "</tr>";



				echo "<tr><td colspan='2'><br /></td></tr>"; // Space







				echo "<tr><td colspan='2'><h5>{$lang['Notifications']}</h5></td></tr>"; // Headline


				// Value
				echo "<tr>";
					echo "<td>{$lang['Repeat every']}</td>";
					echo "<td>";
						echo "<input style='width:35px;' type='text' name='repeat' id='repeat' value='$repeat_alert' /> {$lang['minutes']}";
					echo "</td>";
				echo "</tr>";

				// Send to
				echo "<tr>";
					echo "<td>{$lang['Send to']}</td>";
					echo "<td>";
						echo "<label class='checkbox'>";
								if ($send_to_mail == 1) $sendToMailChecked = "checked='checked'";
					          echo "<input type='checkbox' name='sendTo_mail' value='1' $sendToMailChecked> {$lang['Email']}";
					        echo "</label>";
					echo "</td>";
				echo "</tr>";


				// Primary mail
				echo "<tr>";
					echo "<td>{$lang['Primary']} {$lang['Email']}</td>";
					echo "<td>";
						echo "<input style='width:350px;' type='text' name='mail_primary' id='repeat' value='$mail_primary' />";
					echo "</td>";
				echo "</tr>";

				// Secondary mail
				echo "<tr>";
					echo "<td>{$lang['Secondary']} {$lang['Email']}</td>";
					echo "<td>";
						echo "<input style='width:350px;' type='text' name='mail_secondary' id='repeat' value='$mail_secondary' />";
					echo "</td>";
				echo "</tr>";



				// Submit
				echo "<tr>";
					echo "<td colspan='2'>";
						echo "<div style='text-align:right;'>";
							if ($action == "edit") echo "<a class='btn' href='?page=settings&view=schedule'>{$lang['Cancel']}</a> &nbsp; ";
							echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Save data']."' />";
						echo "</div>";
					echo "</td>";
				echo "</tr>";

			echo "</table>";
		echo "</form>";
		echo "</div>";
	}








	/* Show notifications
	--------------------------------------------------------------------------- */
	$query = "SELECT * 
			  FROM ".$db_prefix."schedule 
			  INNER JOIN ".$db_prefix."sensors ON ".$db_prefix."schedule.sensor_id = ".$db_prefix."sensors.sensor_id
			  WHERE ".$db_prefix."schedule.user_id='{$user['user_id']}' 
			  ORDER BY ".$db_prefix."schedule.sensor_id ASC";
    $result = $mysqli->query($query);
    $numRows = $result->num_rows;

    if ($numRows > 0) {

    	echo "<table class='table table-striped table-hover'>";
			echo "<thead>";
				echo "<tr>";
					//echo "<th>".$lang['Name']."</th>";
					echo "<th>".$lang['Rule']."</th>";
					echo "<th>".$lang['Email']."</th>";
					echo "<th>".$lang['Repeat every']."</th>";
					echo "<th>".$lang['Last sent']."</th>";
					echo "<th></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";

		    	while($row = $result->fetch_array()) {

		    		echo "<tr>";


		    			echo "<td>";

		    				// Sensorname
		    				echo "#{$row['sensor_id']}: {$row['name']}<br />";


		    				// Rule description
		    				if ($row['direction'] == "less") $directionDesc = $lang['Lower than'];
		    				elseif ($row['direction'] == "more") $directionDesc = $lang['Higher than'];

		    				if ($row['type'] == "celsius") {
		    					$typeDesc = $lang['Temperature'];
		    					$unit = "&deg;";
		    				}
		    				elseif ($row['type'] == "humidity") {
		    					$typeDesc = $lang['Humidity'];
		    					$unit = "%";
		    				}

		    				echo "{$lang['If']} <b>$typeDesc</b> ".strtolower($lang['Is'])." <b>$directionDesc</b> ".strtolower($lang['Than'])." <b>{$row['warning_value']}" . $unit . "</b>";

		    				if (!empty($row['device'])) {
		    					$getDeviceName = getField("name", "".$db_prefix."devices", "WHERE device_id='{$row['device']}'");
			    				
		    					echo "<br />";
			    				echo "$getDeviceName";
			    				if ($row['device_set_state'] == 1) echo " &nbsp; ({$lang['On']})";
			    				elseif ($row['device_set_state'] == 0) echo " &nbsp; ({$lang['Off']})";
			    			}
		    			echo "</td>";


		    			// Send to mail
		    			echo "<td style='text-align:center;'>";
		    				if ($row['send_to_mail'] == 1) echo "<img style='height:16px;' src='images/metro_black/check.png' alt='yes' />";
		    				else echo "<img style='height:16px;' src='images/metro_black/cancel.png' alt='no' />";
		    			echo "</td>";


		    			// Repeat every
		    			echo "<td>";
		    				echo "{$row['repeat_alert']} {$lang['minutes']}";
		    			echo "</td>";


		    			// Time since last warning
		    			echo "<td>";
		    				if ($row['last_warning'] > 0) echo ago($row['last_warning']);
		    			echo "</td>";


		    			// Toggle tools
		    			echo "<td>";
							echo "<div class='btn-group'>";
								echo "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#''>";
									echo "<span class='caret'></span>";
								echo "</a>";

								echo "<ul class='dropdown-menu'>";
					    			echo "<li><a href='?page=settings&view=schedule&action=edit&id={$row['notification_id']}'>Edit</a></li>";
					    			echo "<li><a href='?page=settings_exec&action=deleteNotification&id={$row['notification_id']}'>Delete</a></li>";
								echo "</ul>";
							echo "</div>";
		    			echo "</td>";

		    		echo "</tr>";

		    	}

    		echo "</tbody>";
    	echo "</table>";
    }

    else echo "<div class='alert'>{$lang['Nothing to display']}</div>";


?>