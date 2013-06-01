<?php

	require("../../lib/config.inc.php");
	require("../../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 
	 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");
	
	$type_int = clean($_GET['type_int']);
	$sensor_id = clean($_GET['sensor_id']);
	
	// decide if the current config value should be attached or not
	if (strlen($sensor_id) > 0) {
		$query = "SELECT vstc.id, vstc.value_key, vstc.value_type, vstc.description, vsc.value as config_value
			FROM ".$db_prefix."virtual_sensors_types_config vstc, ".$db_prefix."virtual_sensors_config vsc 
			where vsc.config_id = vstc.id 
			and vsc.sensor_id=$sensor_id 
			and vstc.type_int=$type_int 
			and vstc.value_type not like 'return%'";
	} else {
		$query = "SELECT id, value_key, value_type, description, '' as config_value 
			FROM ".$db_prefix."virtual_sensors_types_config 
			WHERE type_int=$type_int 
			and value_type not like 'return%'";
	}
	
	$result = $mysqli->query($query);
	
	$rows = array();
	while($r = $result->fetch_assoc()) {
		$rows[] = $r;
	}
	echo json_encode($rows);


?>