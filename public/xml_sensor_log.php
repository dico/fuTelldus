<?php

	require("../lib/base.inc.php");


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['sensorID'])) $getSensorID = clean($_GET['sensorID']);



	$xml = new SimpleXMLElement('<xml/>');

		$query = "SELECT * 
				  FROM ".$db_prefix."sensors_log 
				  WHERE sensor_id='$getSensorID' 
				  ORDER BY time_updated DESC LIMIT 96";
		$result = $mysqli->query($query);

		$getSensorName = getField("name", "".$db_prefix."sensors", "WHERE sensor_id='$getSensorID'");

		

		while ($row = $result->fetch_array()) {
		    $track = $xml->addChild('sensorValues');
			$track->addAttribute('lastUpdate', "{$row['time_updated']}");
			$track->addAttribute('temp', "{$row['temp_value']}");
			$track->addAttribute('humidity', "{$row['humidity_value']}");
		}

	Header('Content-type: text/xml');
	print($xml->asXML());

?>