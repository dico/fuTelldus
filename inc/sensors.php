<?php
	
	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='?page=settings&view=user'>your userprofile</a>.";
		exit();
	}



	/* Request sensors list from Telldus Live
	--------------------------------------------------------------------------- */
	if ($userTelldusConf['sync_from_telldus'] == 1) {
		require_once 'HTTP/OAuth/Consumer.php';


		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		$params = array();
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensors/list', $params, 'GET');

		/*
		echo '<pre>';
			echo( htmlentities($response->getBody()));
		echo '</pre>';
		*/
		

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);




		/* Store sensors in DB
		--------------------------------------------------------------------------- */
		foreach($xmldata->sensor as $sensorData) {
			
			$sensorID = trim($sensorData['id']);
			$name = trim($sensorData['name']);
			$lastUpdate = trim($sensorData['lastUpdated']);
			$ignored = trim($sensorData['ignored']);
			$client = trim($sensorData['client']);
			$clientName = trim($sensorData['clientName']);
			$online = trim($sensorData['online']);
			$editable = trim($sensorData['editable']);

			$monitorSensor = 0;
			$monitorSensor = getField("monitoring", "".$db_prefix."sensors", "WHERE sensor_id='".$sensorData['id']."'");
			$publicValue = getField("public", "".$db_prefix."sensors", "WHERE sensor_id='".$sensorData['id']."'");

			// Use REPLACE INTO to overwrite with device_id as primary
			$query = "REPLACE INTO ".$db_prefix."sensors SET 
						user_id='".$user['user_id']."', 
						sensor_id='".$sensorID."', 
						name='".$name."', 
						last_update='".$lastUpdate."',  
						ignored='".$ignored."',  
						client='".$client."',  
						clientname='".$clientName."',  
						online='".$online."',  
						editable='".$editable."',
						monitoring='".$monitorSensor."',
						public='".$publicValue."'";
			$result = $mysqli->query($query);
		}

		echo "<div class='hidden-phone' style='float:right; margin-right:25px; margin-bottom:-50px; color:green; font-size:10px;'>{$lang['List synced']}</div>";
	}






	/* Headline
	--------------------------------------------------------------------------- */
	echo "<h3 class='hidden-phone'>".$lang['Sensors']."</h3>";

	echo "<div class='hidden-phone'>{$lang['Sensors description']}</div>";




	/* Messages
	--------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success'>".$lang['Sensor added to monitoring']."</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-info'>".$lang['Sensor removed from monitoring']."</div>";
	}





	/* Sensors
	--------------------------------------------------------------------------- */
	echo "<div class='well'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>".$lang['Name']."</th>";
					echo "<th>".$lang['Temperature']."</th>";
					echo "<th>".$lang['Humidity']."</th>";
					echo "<th>".$lang['Last update']."</th>";
					echo "<th>".$lang['Location']."</th>";
					echo "<th width='23%'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";

				$query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' ORDER BY name ASC LIMIT 100";
				$result = $mysqli->query($query);

				while ($row = $result->fetch_array()) {
					
					$sValueQuery = $mysqli->query("SELECT temp_value, humidity_value, time_updated FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row['sensor_id']}' ORDER BY time_updated DESC LIMIT 1");
					$sValues = $sValueQuery->fetch_array();

					echo "<tr>";
						echo "<td>";
							echo "<a href='?page=sensors_data&id={$row['sensor_id']}'><span class='hidden-phone'>#{$row['sensor_id']}: </span>{$row['name']}</a>";
							echo "<div class='visible-phone'>" . ago($sValues['time_updated']) . "</div>";
						echo "</td>";
						

						echo "<td>{$sValues['temp_value']}&deg;</td>";
						echo "<td>{$sValues['humidity_value']}%</td>";

						echo "<td class='hidden-phone'>" . ago($sValues['time_updated']) . "</td>";

						echo "<td class='hidden-phone'>{$row['clientname']}</td>";

						echo "<td class='hidden-phone' style='text-align:right;'>";
							//echo "<a class='btn' href='?page=sensors_data&action=removeSensor&id={$row['sensor_id']}'>".$lang['Data']."</a> &nbsp; ";

							$monitorSensor = getField("monitoring", "".$db_prefix."sensors", "WHERE sensor_id='{$row['sensor_id']}'");


							if ($row['public'] == 1) {
								echo "<a class='btn btn-success' href='?page=sensors_exec&action=setSensorNonPublic&id={$row['sensor_id']}'>{$lang['Public']}</a>";
							}

							else {
								echo "<a class='btn btn-inverse' href='?page=sensors_exec&action=setSensorPublic&id={$row['sensor_id']}'>{$lang['Non public']}</a>";
							}

							echo " &nbsp; ";


							if ($monitorSensor == 0) {
								echo "<a class='btn btn-success' href='?page=sensors_exec&action=addSensor&id={$row['sensor_id']}&name={$row['sensor_id']}&location={$row['clientname']}'>".$lang['Monitor']."</a>";
							} else {
								echo "<a class='btn btn-danger' href='?page=sensors_exec&action=removeSensor&id={$row['sensor_id']}'>".$lang['Stop']."</a>";
							}
						echo "</td>";

					echo "</tr>";
				}


			echo "</tbody>";
		echo "</table>";
	echo "</div>";
