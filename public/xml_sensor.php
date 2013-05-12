<?php

	require("../lib/base.inc.php");


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['sensorID'])) $getSensorID = clean($_GET['sensorID']);



	$xml = new SimpleXMLElement('<xml/>');

		$query = "SELECT * 
				  FROM ".$db_prefix."sensors_log 
				  INNER JOIN ".$db_prefix."sensors ON ".$db_prefix."sensors_log.sensor_id = ".$db_prefix."sensors.sensor_id
				  WHERE ".$db_prefix."sensors_log.sensor_id='$getSensorID' 
				  ORDER BY time_updated DESC LIMIT 1";
		$result = $mysqli->query($query);

		while ($row = $result->fetch_array()) {
		    $track = $xml->addChild('sensor');
		    $track->addChild('sensorID', "{$row['sensor_id']}");
		    $track->addChild('name', "{$row['name']}");
		    $track->addChild('location', "{$row['clientname']}");
		    $track->addChild('lastUpdate', "{$row['time_updated']}");
		    $track->addChild('temp', "{$row['temp_value']}");
		    $track->addChild('humidity', "{$row['humidity_value']}");
		}

	Header('Content-type: text/xml');
	print($xml->asXML());

?>