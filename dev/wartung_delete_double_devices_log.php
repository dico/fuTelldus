<?php
	ob_start();
	session_start();

	/* Connect to database
	--------------------------------------------------------------------------- */
	require("lib/config.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");

	/* Get oAuth class
	--------------------------------------------------------------------------- */
	require_once 'HTTP/OAuth/Consumer.php';



	/* ##################################################################################################################### */
	/* ######################################## SCRIPT RUNS BELOW THIS LINE ################################################ */
	/* ##################################################################################################################### */


	$query = "SELECT * FROM ".$db_prefix."devices";
    $result = $mysqli->query($query);
	$countDelete=0;
	
    while ($row = $result->fetch_array()) {
		$deviceID = $row['device_id'];

		$query2 = "SELECT * FROM ".$db_prefix."devices_log where device_id = '".$deviceID."' order by time_updated asc";
		$result2 = $mysqli->query($query2);

		$first = 1;
		$oldState = -1;
		
		while ($row2 = $result2->fetch_array()) {
			if ($first==0) {
				if ($oldState == $row2['status']){
					$countDelete = $countDelete + 1;
					$query3 = "DELETE FROM ".$db_prefix."devices_log where device_id = '".$deviceID."' and time_updated='".$row2['time_updated']."'";
					$result3 = $mysqli->query($query3);
				}
				$oldState = $row2['status'];
			} else {
				$oldState = $row2['status'];
				$first=0;
			}
		}
		
		
	}	
	echo "Would delete ".$countDelete. "                ";
?>