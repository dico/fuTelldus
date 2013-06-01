<?php
	
	function getAvailablePlugins() {
		$dirs = getPluginDirs();

		$plugins = array();
		foreach (array_keys($dirs) as $key) {
			$ini_array = parse_ini_file($dirs[$key]."/plugin_info.txt");
			//array_push($ini_array, array("directory" => $dirs[$key]));
			$ini_array["directory"] = $dirs[$key];
			array_push($plugins, $ini_array);
		}
		
		return $plugins;
	}
	
	function getPluginDirs() {
		$plugins = array();

		$path = "fuPlugins/virtutal_devices/";
		foreach(glob($path.'*', GLOB_ONLYDIR) as $dir) {
			array_push($plugins, $dir);
		}

		return $plugins;
	}
	
	function isPluginActivated($pluginPath) {
		global $mysqli;
		global $db_prefix;
		
		$query = "SELECT * FROM ".$db_prefix."virtual_sensors_types where plugin_path='".$pluginPath."'";
	    $result = $mysqli->query($query);
		if (isset($result)) {
			$numRows = $result->num_rows;	
			if ($numRows > 1) {
				$numRows=1;
			}
		} else {
			$numRows=0;
		}
	    
		return $numRows;
	}
	
	function activatePlugin($pluginName, $pluginPath, $version) {
		global $mysqli;
		global $db_prefix;
		
		$pluginIndex = $pluginPath."/index.php";
		if (file_exists($pluginIndex)) {
		
			$nameSpace = includePlugin($pluginIndex);
			$func = $nameSpace."\\activateHook";		
			//include_once $pluginIndex;
			$configFields = @$func();

			$query = "INSERT INTO ".$db_prefix."virtual_sensors_types SET
				type_description='".$pluginName."', 
				activated_version='".$version."', 				
				plugin_path='".$pluginPath."'";
			$result = $mysqli->query($query);
			$vSensorPluginID = $mysqli->insert_id;
			
			// add every config type
			foreach (array_keys($configFields) as $value_key) {
				foreach (array_keys($configFields[$value_key]) as $description) {
					$queryInsert2 = "INSERT INTO ".$db_prefix."virtual_sensors_types_config SET
						type_int='".$vSensorPluginID."', 
						value_key='".$value_key."', 
						value_type='".$configFields[$value_key][$description]."', 
						description='".$description."'";
					$resultInsert2 = $mysqli->query($queryInsert2);
				}
			}
		}		
	}
	
	function getPluginPathToPluginID($pluginID) {
		global $mysqli;
		global $db_prefix;
		
		$queryPath = "select plugin_path from ".$db_prefix."virtual_sensors_types where type_int='".$pluginID."'";
		$result = $mysqli->query($queryPath);
		$pluginPath = $result->fetch_assoc()['plugin_path'];
		return $pluginPath;
	}
	
	function getPluginPathToVSensorId($sensorID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select vst.plugin_path from ".$db_prefix."virtual_sensors vs, ".$db_prefix."virtual_sensors_types vst where vs.id=$sensorID and vs.sensor_type=vst.type_int";
		$result = $mysqli->query($query);
		$phpscript = $result->fetch_assoc();
		return $phpscript['plugin_path'];
	}
	
	function getVirtualSensorDescriptionToSensorId($sensorID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select description from ".$db_prefix."virtual_sensors where id='".$sensorID."'";
		$result = $mysqli->query($query);
		$description = "";
		if (mysqli_num_rows($result) == 1) {
			$description = $result->fetch_assoc()['description'];
		}
		return $description;
	}
	
	function getVirtualSensorTypeDescriptionToSensorId($sensorID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select vst.type_description from ".$db_prefix."virtual_sensors vs, ".$db_prefix."virtual_sensors_types vst where vs.id='".$sensorID."' and vst.type_int = vs.sensor_type";
		$result = $mysqli->query($query);
		$typeDescription = "";
		if (mysqli_num_rows($result) == 1) {
			$typeDescription = $result->fetch_assoc()['type_description'];
		}
		return $typeDescription;
	}
	
	function getVirtualSensorConfigToKey($virtualSensorID, $config_key) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select vsc.value from ".$db_prefix."virtual_sensors_config vsc, ".$db_prefix."virtual_sensors_types_config vstc where vstc.id = vsc.config_id and vstc.value_key='$config_key' and vsc.sensor_id=$virtualSensorID";
		$result = $mysqli->query($query);
		$configValue = "";
		if (mysqli_num_rows($result) == 1) {
			$configValue = $result->fetch_assoc()['value'];
		}
		return $configValue;
	}
	
	function updatePlugin($pluginID, $version) {
		global $mysqli;
		global $db_prefix;
		
		$path = getPluginPathToPluginID($pluginID);
		
		$query = "update ".$db_prefix."virtual_sensors_types SET
				activated_version='".$version."' where type_int='".$pluginID."'"; 				
		$mysqli->query($query);

		$nameSpace = includePlugin($path."/index.php");
		$func = $nameSpace."\\updateHook";	
		//include_once $path."/index.php";
		$configFields = @$func();
		foreach (array_keys($configFields) as $value_key) {
			foreach (array_keys($configFields[$value_key]) as $description) {
				// replace/insert all
				$queryInsert = "REPLACE INTO ".$db_prefix."virtual_sensors_types_config SET 
						type_int='".$pluginID."', 
						value_key='".$value_key."', 
						value_type='".$configFields[$value_key][$description]."', 
						description='".$description."'";
				$resultInsert = $mysqli->query($queryInsert);
			}
		}
		
		// get all which where deleted
		$querySelectAll = "select value_key, id from ".$db_prefix."virtual_sensors_types_config where type_int='".$pluginID."'";
		$resultSelectAll = $mysqli->query($querySelectAll);
		
		while ($row = $resultSelectAll->fetch_array()) {
			$value_key_db = $row['value_key'];
			$rowId = $row['id'];
			
			if (!array_key_exists($value_key_db, $configFields)){
				$queryDelete = "delete from ".$db_prefix."virtual_sensors_types_config where id='".$rowId."' and type_int='".$pluginID."' and value_key='".$value_key_db."'";
				$mysqli->query($queryDelete);
				
				$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."virtual_sensors_config where config_id='".$rowId."'";
				$mysqli->query($queryDeleteVirtualSensorConfig);
			}
		}
	}
	
	function disablePlugin($pluginID) {
		global $mysqli;
		global $db_prefix;
		
		$querySelectVirtualSensor = "select * from ".$db_prefix."virtual_sensors where sensor_type='".$pluginID."'";
		$resultAllSensors = $mysqli->query($querySelectVirtualSensor);
		
		while ($row = $resultAllSensors->fetch_array()) {
			$sensorID = $row['id'];
			
			$querySelectVirtualSensorLogs = "select * from ".$db_prefix."virtual_sensors_log where sensor_id='".$sensorID."'";
			$resultAllSensorsLogs = $mysqli->query($querySelectVirtualSensorLogs);
			
			while ($row1 = $resultAllSensorsLogs->fetch_array()) {
				$logID = $row1['id'];
				
				$queryDeleteVirtualSensorLogValue = "delete from ".$db_prefix."virtual_sensors_log_values where log_id='".$logID."'";
				$mysqli->query($queryDeleteVirtualSensorLogValue);
			}
			
			$queryDeleteVirtualSensorLogs = "delete from ".$db_prefix."virtual_sensors_log where sensor_id='".$sensorID."'";
			$mysqli->query($queryDeleteVirtualSensorLogs);
			
			$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."virtual_sensors_config where sensor_id='".$sensorID."'";
			$mysqli->query($queryDeleteVirtualSensorConfig);
			
			$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."virtual_sensors where id='".$sensorID."'";
			$mysqli->query($queryDeleteVirtualSensorConfig);
			
			$queryDeleteVirtualSensorTmp = "delete from ".$db_prefix."virtual_sensors_tmpvals where sensor_id='".$sensorID."'";
			$mysqli->query($queryDeleteVirtualSensorTmp);
		}
		
		$queryDeleteVirtualSensor = "delete from ".$db_prefix."virtual_sensors_types where type_int='".$pluginID."'";
		$mysqli->query($queryDeleteVirtualSensor);
		
		$queryDeleteVirtualSensorConfig = "delete from ".$db_prefix."virtual_sensors_types_config where type_int='".$pluginID."'";
		$mysqli->query($queryDeleteVirtualSensorConfig);
	}
	
	
	function getCurrentVirtualSensorState($virtualSensorID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select vstc.value_key, vsc.value from ".$db_prefix."virtual_sensors_config vsc, ".$db_prefix."virtual_sensors_types_config vstc where vsc.sensor_id=$virtualSensorID and vstc.id = vsc.config_id and vstc.value_type!='return'";
		$result = $mysqli->query($query);

		$parameter = array();
		while ($row = $result->fetch_array()) {
			$parameter[$row['value_key']] = $row['value'];
		}

		// find the script
		$phpscript = getPluginPathToVSensorId($virtualSensorID);
		
		$nameSpace = includePlugin($phpscript."/index.php");
		$func = $nameSpace."\\getVirtualSensorVal";	
		//include_once $phpscript."/index.php";		
		$returnFromScript = @$func($parameter, $virtualSensorID); 	
		
		// update last check
		$lastUpdated = time();
		$updateCheck = "update ".$db_prefix."virtual_sensors set last_check='".$lastUpdated."' WHERE id='".$virtualSensorID."'";
		$mysqli->query($updateCheck);
		
		return $returnFromScript;
	}
	
	// returns an array with arrays, containing the return-type and as sub-arrays
	// the data found in the DB for this time range and maybe transformed by
	// the plugin itself
	function getVirtualSensorChartData($virtualSensorID, $start, $end) {
		// find the script
		$phpscript = getPluginPathToVSensorId($virtualSensorID);
		
		// get the data from the DB
		$chartData = getChartData($virtualSensorID, $start, $end);
		
		// if the plugin would like to change the data, the according function is called
		$nameSpace = includePlugin($phpscript."/index.php");
		$func = $nameSpace."\\editChartData";
		$returnFromScript = @$func($virtualSensorID, $chartData);
		
		return $returnFromScript;
	}
	
	// returns an array of the axis definition for the virtual sensor
	function getVirtualSensorChartAxis($virtualSensorID) {
		global $mysqli;
		global $db_prefix;
		
		$phpscript = getPluginPathToVSensorId($virtualSensorID);
		$nameSpace = includePlugin($phpscript."/index.php");
		$func = $nameSpace."\\overwriteChartAxisDefinition";
		
		$selectReturnTypes = "select vstc.value_key, vstc.description from futelldus_virtual_sensors vs, futelldus_virtual_sensors_types_config vstc
				where vs.id = $virtualSensorID
				and vs.sensor_type = vstc.type_int
				and vstc.value_type like 'return%'";
		
		$result = $mysqli->query($selectReturnTypes);

		$axisArray = array();
		$rowCounter = 0;
		while ($row = $result->fetch_array()) {
			$axisInfo = array();
			array_push($axisInfo, $rowCounter);
			array_push($axisInfo, $row['description']);
			array_push($axisInfo, "");
			$axisArray[$row['value_key']] = $axisInfo;
			
			$rowCounter++;
		}
		
		if (function_exists($func)) {
			$axisArray = @$func($axisArray);
		}
		
		return $axisArray;
	}
	
	// return true, if the plugin defines the function editChartData
	// that means, that charts would be showable on the UI
	function isPluginProvidingCharts($virtualSensorID) {
		$phpscript = getPluginPathToVSensorId($virtualSensorID);
		$nameSpace = includePlugin($phpscript."/index.php");
		$func = $nameSpace."\\editChartData";
		return function_exists($func);
	}
	
	// return an array with all returntypes of the virtual sensor and the requested data
	function getChartData($virtualSensorID, $start, $end) {
		global $db_prefix;
		global $mysqli;
		
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
		
		
		// get all return types
		$queryVirtualSensorReturnTypes ="select vstc.value_key from futelldus_virtual_sensors vs, futelldus_virtual_sensors_types_config vstc
			where vs.id = $virtualSensorID
			and vs.sensor_type = vstc.type_int
			and vstc.value_type like 'return%'";
		$queryResultReturnTypes = $mysqli->query($queryVirtualSensorReturnTypes);
		
		// for every returntype get the data and store to the array
		$chartDataArray = array();
		while ($returnTypeRow = $queryResultReturnTypes->fetch_array()) {
			$returnType = $returnTypeRow['value_key'];
			
			$queryVirtualSensorChartData = "select vsl.time_updated, vslv.value from ".$db_prefix."virtual_sensors_log vsl, ".$db_prefix."virtual_sensors_log_values vslv
				where vsl.id = vslv.log_id
				and vsl.sensor_id=$virtualSensorID
				and vsl.time_updated between $start and ($end+86400)
				and vslv.value_key='".$returnType."' 
				GROUP BY FLOOR(time_updated / $rangeValue)
				order by vsl.time_updated";
		
			$queryResult = $mysqli->query($queryVirtualSensorChartData);
			
			$dataArray = array();
			while ($row = $queryResult->fetch_array()) {
				$dataArray[$row['time_updated']] = $row['value'];
			}
			$chartDataArray[$returnType] = $dataArray;
		}
		
		
		return $chartDataArray;
	}

	function getLastVirtualSensorLog($virtualSensorID) {
		global $db_prefix;
		global $mysqli;
		
		$queryCount = "select count(*) as count_limit from ".$db_prefix."virtual_sensors s, ".$db_prefix."virtual_sensors_types_config stc
			where s.id = $virtualSensorID
			and s.sensor_type = stc.type_int
			and stc.value_type='return'";
		$resultCount = $mysqli->query($queryCount);
		$countLimit = $resultCount->fetch_assoc()['count_limit'];
	
		$query = "select slv.value_key, slv.value from ".$db_prefix."virtual_sensors_types_config stc, ".$db_prefix."virtual_sensors s, ".$db_prefix."virtual_sensors_log sl, ".$db_prefix."virtual_sensors_log_values slv
					where stc.value_type='return'
					and s.sensor_type = stc.type_int
					and s.id = $virtualSensorID
					and sl.sensor_id=s.id
					and sl.id = slv.log_id and slv.value_key=stc.value_key
					order by sl.time_updated desc limit $countLimit";
		$result = $mysqli->query($query);
		$actualValues = array();
		while($row = $result->fetch_array()) {
			$actualValues[$row['value_key']] = $row['value'];
		}
		
		return $actualValues;
	}
	
	function storeVirtualSensorTmpVal($sensorID, $value_key, $value) {
		global $db_prefix;
		global $mysqli;
		
		$queryInsert = "REPLACE INTO ".$db_prefix."virtual_sensors_tmpvals SET 
						sensor_id='".$sensorID."', 
						value_key='".$value_key."', 
						value='".$value."'";
		$resultInsert = $mysqli->query($queryInsert);
	}
	
	function getVirtualSensorTmpVal($sensorID, $value_key) {
		global $db_prefix;
		global $mysqli;
		
		$queryTmpVal = "select value from ".$db_prefix."virtual_sensors_tmpvals 
						where sensor_id='".$sensorID."' and value_key='".$value_key."'";
		$result = $mysqli->query($queryTmpVal);
		$return=null;
		if (mysqli_num_rows($result) == 1) {
			$return = $result->fetch_assoc()['value'];
		}
		return $return;
	}
	
	function deleteVirtualSensorTmpVal($sensorID, $value_key) {
		global $db_prefix;
		global $mysqli;
		
		$deleteTmpVal = "delete from ".$db_prefix."virtual_sensors_tmpvals 
						where sensor_id='".$sensorID."' and value_key='".$value_key."'";
		$mysqli->query($deleteTmpVal);
	}	
	
	// gets the timestamp from the log values (real update)
	function getLastVirtualSensorLogTimestamp($virtualSensorID){
		global $db_prefix;
		global $mysqli;

		$queryTimestamp = "select time_updated from futelldus_virtual_sensors_log where sensor_id='".$virtualSensorID."' order by time_updated desc limit 1";
		$result = $mysqli->query($queryTimestamp);
		$timeStamp=0;
		if (mysqli_num_rows($result) == 1) {
			$timeStamp = $result->fetch_assoc()['time_updated'];
		}
		return $timeStamp;
	}
	
	// gets the timestamp from the sensor itself, should return the same like getLastVirtualSensorLogTimestamp();
	function getLastVirtualSensorTimestamp($virtualSensorID){
		global $db_prefix;
		global $mysqli;

		$queryTimestamp = "select last_update from futelldus_virtual_sensors where id='".$virtualSensorID."' order by last_update desc limit 1";
		$result = $mysqli->query($queryTimestamp);
		$timeStamp=0;
		if (mysqli_num_rows($result) == 1) {
			$timeStamp = $result->fetch_assoc()['last_update'];
		}
		return $timeStamp;
	}	
	
	function getLastVirtualSensorCheck($virtualSensorID){
		global $db_prefix;
		global $mysqli;

		$queryTimestamp = "select last_check from futelldus_virtual_sensors where id='".$virtualSensorID."' order by last_check desc limit 1";
		$result = $mysqli->query($queryTimestamp);
		$timeStamp=0;
		if (mysqli_num_rows($result) == 1) {
			$timeStamp = $result->fetch_assoc()['last_check'];
		}
		return $timeStamp;
	}		
	
	
	function getCurrentVirtualSensorStateWidet($virtualSensorID) {
		$actualValues = getLastVirtualSensorLog($virtualSensorID);
		
		$phpscript = getPluginPathToVSensorId($virtualSensorID);
		$nameSpace = includePlugin($phpscript."/index.php");
		$func = $nameSpace."\\getDashBoardWidget";
		
		$dashboardwidget = @$func($actualValues, $virtualSensorID);
		
		return $dashboardwidget; //$lastValue['value'];
	}
?>