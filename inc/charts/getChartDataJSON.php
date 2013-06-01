<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once("../../lib/config.inc.php");
	require_once("../../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 
	 

	// Check for connection errors
	if ($mysqli->connect_errno) {
		die('Connect Error: ' . $mysqli->connect_errno);
	}
	
	// Set DB charset
	mysqli_set_charset($mysqli, "utf8");
	
	// get parameter
	$id = clean($_GET['id']);
	$type = clean($_GET['type']);

	$callback = $_GET['callback'];
	if (!preg_match('/^[a-zA-Z0-9_]+$/', $callback)) {
		die('Invalid callback name');
	}
	
	$start = $_GET['start'];
	if ($start && !preg_match('/^[0-9]+$/', $start)) {
		die("Invalid start parameter: $start");
	}

	$end = $_GET['end'];
	if ($end && !preg_match('/^[0-9]+$/', $end)) {
		die("Invalid end parameter: $end");
	}
	
	
	echo "/*para_start=$start, para_end=$end*/";
	
	if (!$end){
		$end = time();
	} else {
		$end = $end/1000;
	}
	if (!$start) {
		$start = 1;
	} else {
		$start = $start/1000;
	}
	
	
	// Set how long back you want to pull data
    //$showFromDate = time() - 86400 * $config['chart_max_days']; // 86400 => 24 hours * 10 days
	
	
	/* Generate the chart
	-------------------------------------------------------*/
	if ($type == "sensor") {
		$rows = getSensorChartJSON($id, $name, $start, $end);
		header('Content-Type: text/javascript');
		echo $callback ."([\n" . $rows ."\n]);";
	}
	
	if ($type == "virtual") {
		$rows = generateVirtualSensorChart($id, $name, $start, $end);
		header('Content-Type: text/javascript');
		echo $callback ."([\n" . $rows ."\n]);";
	}
	
	if ($type == "device") {
		$rows =  generateDeviceChart($id, $name, $start, $end);
		header('Content-Type: text/javascript');
		echo $callback ."([\n" . $rows ."\n]);";
	}

	// returns an array in array with the return-type definied by the plugin
	// and the data found in the DB belonging to this return-type
	function generateVirtualSensorChart($id, $name, $start, $end) {
		global $mysqli;
		global $db_prefix;	
		
		$offset = date('Z');
		echo "/* timezone offset: ".$offset."\n*/";
		// calculate the correct offset to get ever 0am for the actual day
		$end = $end - $offset + 86400;
		
		$chartDataArray = getVirtualSensorChartData($id, $start, $end);
		// convert the chartDataArray into a highchart compatible array
		// make sure, that all the values are at the correct position (given by parameter)

		// every axis
		$returnArray = "";
		$first = true;
		while (list($returnKey, $returnValues) = each($chartDataArray)) { 
			//echo $returnKey;
			
			//all timestamps with values
			$axis = array();
			while (list($timestamp, $value) = each($returnValues)) { 
				$axis[] = "[" . $timestamp*1000 . "," . $value . "]";
			}
			if ($first) {
				$returnArray .= "[\n" . join(",\n", $axis) ."\n]";	
				$first = false;
			} else {
				$returnArray .= ",[\n" . join(",\n", $axis) ."\n]";	
			}
		}
		return $returnArray;
	}
	
	function generateDeviceChart($id, $name, $start, $end) {
		global $mysqli;
		global $db_prefix;	
	
		$offset = date('Z');
		echo "/* timezone offset: ".$offset."\n*/";
		// calculate the correct offset to get ever 0am for the actual day
		$end = $end - $offset + 86400;
	
		$queryS = "
			SELECT * FROM futelldus_devices_log
					WHERE device_id={$id}
					and time_updated between $start and $end
					ORDER BY time_updated ASC
		";
		echo "/* query: $queryS */";
        $resultS = $mysqli->query($queryS);
        
		$temp_values = array();
		$first=true;
        while ($sensorData = $resultS->fetch_array()) {
		
			// add extra values to get the on and off-range correctly (last -1 millisecond with old state)
			$timeJS = $sensorData["time_updated"] * 1000;
			if (!$first) {
				$timeEndLast = $timeJS - 1;
				$temp_values[] = "[" . $timeEndLast . "," . $status . "]";
			} else {
				$first=false;
			}
			
			// convert status to 0 and 1 status
            $status = convertStatusToOnOff(trim($sensorData["status"]));
			
            $temp_values[] = "[" . $timeJS . "," . $status . "]";
        }
		
		// add the current timestamp with the last state
		$temp_values[] = "[" . time()*1000 . "," . $status . "]";
		
		$return = join(",\n", $temp_values);
		
		return $return;
	}	
	
	function convertStatusToOnOff($status) {
		$newstatus = 0;
		if ($status > 1) {
			$newstatus = $status - floor($status);
		} else {
			$newstatus = 1;
		}
		return $newstatus;
	}
	
	
	function getSensorChartJSON($id, $name, $start, $end) {
		global $mysqli;
		global $db_prefix;	
	
		$range = $end - $start;

		$rangeValue=86400; // bigger than half a year, dayily
		if ($range <= 15778458) { // half year
			$rangeValue=43200; // halfdays
		}
		
		if ($range <= 2629744) { // one month
			$rangeValue=3600; // hour
		}
		
		if ($range <= 604800) { // one week
			$rangeValue=60;	//minute
		}
		
		if ($range <= 86400) { // one day
			$rangeValue=1;	// all
		}		
		
		if ($range <= 604800) { // one week
			$rangeValue=1;	//all
		}
		
		echo "/* range: $range, so grouping: $group_by \n*/";
		
		$offset = date('Z');
		echo "/* timezone offset: ".$offset."\n*/";
		// calculate the correct offset to get ever 0am for the actual day
		$end = $end - $offset + 86400;

		$queryS = "
			SELECT UNIX_TIMESTAMP(FROM_UNIXTIME(time_updated)) as time_updated, ROUND(avg(temp_value),1) as temp_value, ROUND(avg(humidity_value),1) as humidity_value FROM futelldus_sensors_log 
					WHERE sensor_id='{$id}'
					and time_updated between $start and $end
					GROUP BY FLOOR(time_updated / $rangeValue)
					ORDER BY time_updated ASC
		";
		echo "/* query: $queryS */";
        $resultS = $mysqli->query($queryS);
        
		$temp_values = array();
        while ($sensorData = $resultS->fetch_array()) {
            $db_tempValue = trim($sensorData["temp_value"]);
			$db_humidity_value = trim($sensorData["humidity_value"]);
			
            $timeJS = $sensorData["time_updated"] * 1000;
            $temp_values[]        = "[" . $timeJS . "," . $db_tempValue . "]";
			$humi_values[]        = "[" . $timeJS . "," . $db_humidity_value . "]";
			
        }
		$return = "[\n" . join(",\n", $temp_values) ."\n]";
		$return .= ",[\n" . join(",\n", $humi_values) ."\n]";
		
        //$joinValues = join(",\n", $temp_values);
		//print_r ($temp_values);
		return $return;
	}	
	
	

?>
