<?php
	//error_reporting(E_ALL);
	ini_set('display_errors', '1');	
	
	require("lib/config.inc.php");
	require("lib/base.inc.php");
	

	/* Connect to database
	--------------------------------------------------------------------------- */
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


	/* Find users
	--------------------------------------------------------------------------- */
	$query = "SELECT * FROM ".$db_prefix."users";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_array()) {
    	$query2 = "SELECT * FROM ".$db_prefix."virtual_sensors WHERE user_id='".$row['user_id']."' and monitoring=1";
  		$result2 = $mysqli->query($query2);

	    while ($row2 = $result2->fetch_array()) {
			$lastUpdated = time();
		
			$returnValues = getCurrentVirtualSensorState($row2['id']);
			
			// get all return types
			$queryReturnType = "select vstc.value_key from futelldus_virtual_sensors_types_config vstc, futelldus_virtual_sensors vs where vs.id=".$row2['id']." and vstc.type_int = vs.sensor_type and vstc.value_type='return'";
			$resultReturnType = $mysqli->query($queryReturnType);
			
			//print_r(array_keys($returnValues));
			
			$valueChanged = 0;
			$valueChangedTo = "";
			$logArray = array();
			
			// insert every log value
			foreach (array_keys($returnValues) as $key) {
				$value = $returnValues[$key];
				
				// check if the status changed
				$queryOldVal = "select slv.value from ".$db_prefix."virtual_sensors_log sl, ".$db_prefix."virtual_sensors_log_values slv 
						where sl.sensor_id='".$row2['id']."' 
						and sl.id = slv.log_id 
						and slv.value_key='".$key."' 
						order by sl.time_updated desc LIMIT 1";
				$resultOldVal = $mysqli->query($queryOldVal);
				$oldVal = $resultOldVal->fetch_assoc();
				if (!isset($oldVal['value']) or $oldVal['value'] != $value) {
					$valueChanged = 1;
					$valueChangedTo=$valueChangedTo."".$key."=".$value."; ";
					//echo "value changed for ".$key." from ".$oldVal['value']." to ".$value."\n";
				}

				// generate the sql and store to array
				$queryInsertValue = "REPLACE INTO ".$db_prefix."virtual_sensors_log_values SET 
								log_id='%%LOGID%%', 
								value_key='". $key ."', 
								value='".$value."'";
				array_push($logArray, $queryInsertValue); 				
			}

			if ($valueChanged == 1) {
				// update timestamp
				$update1 = "update ".$db_prefix."virtual_sensors set last_update='".$lastUpdated."'WHERE id='".$row2['id']."'";
				$resultUpdate1 = $mysqli->query($update1);
			
				// execute the sqls
				// insert the log
				$queryInsert = "REPLACE INTO ".$db_prefix."virtual_sensors_log SET 
								sensor_id='". $row2['id'] ."', 
								time_updated='".$lastUpdated."'";
				$mysqli->query($queryInsert);
				$log_id = $mysqli->insert_id;
			
				foreach($logArray as $sqlInsert) {
					$sqlInsert = str_replace("%%LOGID%%", $log_id, $sqlInsert);
					$mysqli->query($sqlInsert);
				}
			
			
				$pushover_key = $row['pushover_key'];
				$subject = "Status changed";
				$message =  "The status of jens@home changed to ".$valueChangedTo;

			//	sendNotification($pushover_key, $subject, $message);
			} else {
				//echo "no values changed\n";
			}	
		}
    } //end-while-users
?>
