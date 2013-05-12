<script src="lib/jscripts/futelldus_lights.js"></script>


<?php
	

	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='?page=settings&view=user'>your userprofile</a>.";
		exit();
	}



	/* Request device list from Telldus Live
	--------------------------------------------------------------------------- */
	if ($userTelldusConf['sync_from_telldus'] == 1) {
		require_once 'HTTP/OAuth/Consumer.php';


		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		//$params = array();
		$params = array('supportedMethods'=> 1023);
		//$params = array('id'=> $getID);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/devices/list', $params, 'GET');

		/*
		echo '<pre>';
			echo( htmlentities($response->getBody()));
		echo '</pre>';
		*/
		

		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);




		/* Store devices in DB
		--------------------------------------------------------------------------- */
		foreach($xmldata->device as $deviceData) {
			
			$deviceID = trim($deviceData['id']);
			$name = trim($deviceData['name']);
			$state = trim($deviceData['state']);
			$statevalue = trim($deviceData['statevalue']);
			$methods = trim($deviceData['methods']);
			$type = trim($deviceData['type']);
			$client = trim($deviceData['client']);
			$clientName = trim($deviceData['clientName']);
			$online = trim($deviceData['online']);
			$editable = trim($deviceData['editable']);


			// Use REPLACE INTO to overwrite with device_id as primary
			$query = "REPLACE INTO ".$db_prefix."devices SET 
						device_id='".$deviceID."', 
						user_id='".$user['user_id']."', 
						name='".$name."', 
						state='".$state."', 
						statevalue='".$statevalue."', 
						methods='".$methods."', 
						type='".$type."', 
						client='".$client."',  
						clientname='".$clientName."',  
						online='".$online."',  
						editable='".$editable."'";
			$result = $mysqli->query($query);
		}

		echo "<div class='hidden-phone' style='float:right; margin-right:25px; margin-bottom:-50px; color:green; font-size:10px;'>{$lang['List synced']}</div>";
	}



	echo "<div style='float:right; height:20px; margin-right:20px;' id='ajaxFeedback'></div>";



	/* List groups
	--------------------------------------------------------------------------- */
	echo "<h3 class='hidden-phone'>{$lang['Groups']}</h3>";

	//echo "<div class='well'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>{$lang['Name']}</th>";
					echo "<th class='hidden-phone' width='40%'>{$lang['Location']}</th>";
					echo "<th width='20%'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
				$query = "SELECT * FROM ".$db_prefix."devices WHERE type='group' AND user_id='{$user['user_id']}' ORDER BY name ASC LIMIT 100";
				$result = $mysqli->query($query);

				while ($row = $result->fetch_array()) {
					echo "<tr>";
						echo "<td>{$row['name']}</td>";
						echo "<td class='hidden-phone'>{$row['clientname']}</td>";
						echo "<td style='text-align:right;'>";
							echo "<div id='ajax_loader_{$row['device_id']}'></div>";
							echo "<div class='btn-group'>";
								echo "<a id='btn_{$row['device_id']}_off' class='btn $activeStateOff' href=\"javascript:lightControl('off', '{$row['device_id']}');\">{$lang['Off']}</a>";
								echo "<a id='btn_{$row['device_id']}_on' class='btn $activeStateOn' href=\"javascript:lightControl('on', '{$row['device_id']}');\">{$lang['On']}</a>";
							echo "</div>";
						echo "</td>";
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
	//echo "</div>";



	/* List devices
	--------------------------------------------------------------------------- */
	echo "<h3 class='hidden-phone'>{$lang['Devices']}</h3>";

	//echo "<div class='well'>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>{$lang['Name']}</th>";
					echo "<th class='hidden-phone' width='40%'>{$lang['Location']}</th>";
					echo "<th width='20%'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
				$query = "SELECT * FROM ".$db_prefix."devices WHERE type='device' AND user_id='{$user['user_id']}' ORDER BY name ASC LIMIT 100";
				$result = $mysqli->query($query);

				while ($row = $result->fetch_array()) {
					echo "<tr valign='top'>";
						echo "<td>";
							echo "<div style='display:inline-block;' id='ajax_loader_{$row['device_id']}'></div>";
							echo "{$row['name']}";
						echo "</td>";

						echo "<td class='hidden-phone'>{$row['clientname']}</td>";
						echo "<td style='text-align:right;'>";


							if ($userTelldusConf['sync_from_telldus'] == 1) {
								if ($row['state'] == 1) {
									$activeStateOn = "btn-success active";
									$activeStateOff = "";
								}
								elseif ($row['state'] == 2) {
									$activeStateOn = "";
									$activeStateOff = "btn-success active";
								}
								else {
									$activeStateOff = "";
									$activeStateOn = "";
								}
							}

							

							echo "<div class='btn-group'>";
								echo "<a id='btn_{$row['device_id']}_off' class='btn $activeStateOff' href=\"javascript:lightControl('off', '{$row['device_id']}');\">{$lang['Off']}</a>";
								echo "<a id='btn_{$row['device_id']}_on' class='btn $activeStateOn' href=\"javascript:lightControl('on', '{$row['device_id']}');\">{$lang['On']}</a>";
							echo "</div>";
						echo "</td>";
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
	//echo "</div>";


?>