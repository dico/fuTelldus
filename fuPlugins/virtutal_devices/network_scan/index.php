<?php
namespace virtual_devices\network_scan;

/*
 Needs:
	nmap tool on console
*/

/*	$paras = array(
		'device' => '58:1F:AA:B0:31:2A', 
		'host' => '192.168.1.1',
		'timeout' => '10'
	);
*/

	function activateHook() {
		// return an array with the description, and all fields needed for determining to correct sensor state
		return getConfigArray();
	}
	
	function disableHook() {
		// nothing todo for this plugin
	}
	
	function updateHook() {
		return getConfigArray();
	}
	
	function getConfigArray() {
		return $configs = array(
			'mac_client' => array('Mac Address' => 'text'), 
			'snmp_host' => array('Router IP' => 'text'), 
			'timeout' => array('Timeout in minutes' => 'text'), 
			'addSNMPCheck' => array('Perform SNMP check' => 'boolean'), 
			'wakeupWithLastIP' => array('Send wakeup signal' => 'boolean'), 
			'available' => array('Available' => 'return'), 
			'ip' => array('IP address' => 'return')
		);
	}
	
	function getDashBoardWidget($lastLogValues, $virtualSensorID) {
		$myPath = getPluginPathToVSensorId($virtualSensorID);
	
		$widget = "";
		$widget.="<div class='sensor-blocks well'>";

			$widget.= "<div class='sensor-name'>";
				$widget.= getVirtualSensorDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-location'>";
				$widget.= getVirtualSensorTypeDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-temperature'>";
				$widget.= "<img src='". $myPath."/host.png' alt='icon' />";
				if (isset($lastLogValues['available']) and $lastLogValues['available']==1) {
					$widget.= "&nbsp;yes&nbsp;<span style='font-size:small'>".$lastLogValues['ip']."</span>";	
				} else {
					$widget.= "&nbsp;no&nbsp;";	
				}
			$widget.= "</div>";

			$widget.= "<div class='sensor-timeago'>";
				$timeUpdatedByInsertedLog = getLastVirtualSensorLogTimestamp($virtualSensorID);
				if ($timeUpdatedByInsertedLog==0){
					$timeUpdatedByInsertedLog = time();
				}
				
				$lastLogsTimestamp = getVirtualSensorTmpVal($virtualSensorID, "lastOfflineState");
				$timeStyle = "";
				if ($lastLogsTimestamp>0){
					$timeStyle = "style='color:#580000;'";
				}
				
				$widget.= "<abbr class=\"timeago\" title='".date("c", $timeUpdatedByInsertedLog)."' ".$timeStyle.">".date("d-m-Y H:i", $timeUpdatedByInsertedLog)."</abbr>";
			$widget.= "</div>";
			$widget.= "</div>";	
		
		return $widget;
	}
	
	function getVirtualSensorVal($parameter, $virtualSensorID) {
		$device = $parameter['mac_client']; //"58:1F:AA:B0:31:2A";
		$host = $parameter['snmp_host']; //"192.168.1.1";
		$timeout = $parameter['timeout']; //"timeout in minutes";
		$addSNMPCheck = $parameter['addSNMPCheck']; //"check via SNMP?";
		$wakeupWithLastIP = $parameter['wakeupWithLastIP']; //"send NMAP package on port 62078 before testing? --> wakeup iPhone's wifi";

		if ($wakeupWithLastIP=='true') {
			tryWakeUpWithOldIP($virtualSensorID);
		}
		
		$snmpResult = 0;
		if ($addSNMPCheck=='true') {
			$snmpResult = trySNMPCheck($parameter, $virtualSensorID);
		}
		
		$ip = getDeviceIP($host, $device);
		$available=0;
		if (isset($ip) and strlen(trim($ip))>0) {
			$available=1;
			$returnValArr = array(
				'available'=>$available,
				'ip'=>$ip
			);
		} else {
			// if snmp is enabled, we might get true back, but the IP is blank. But this 
			// don't effect the availability ... maybe we have no IP for minute or so.
			$available=$snmpResult;
			if($available==1) {
				$returnValArr = getLastVirtualSensorLog($virtualSensorID);
			} else {
				$returnValArr = array(
					'available'=>$available,
					'ip'=>$ip
				);
			}
		}		
		
		$tmpValKey="lastOfflineState";	
		if ($available == 0) {
			// handle timeout only when go offline
			$returnValArr = checkTimeOut($virtualSensorID, $returnValArr, $timeout, $tmpValKey);
		} else {
			deleteVirtualSensorTmpVal($virtualSensorID, $tmpValKey);
		}
		return $returnValArr;
	}
	
	function checkTimeOut($virtualSensorID, $currentState, $timeout, $tmpValKey) {
		$lastLogsTimestamp = getVirtualSensorTmpVal($virtualSensorID, $tmpValKey);
		$actualTimeStamp = time();
		if (!isset($lastLogsTimestamp)) {
			$lastLogsTimestamp = $actualTimeStamp;
			storeVirtualSensorTmpVal($virtualSensorID, $tmpValKey, $actualTimeStamp);
		}
		
		$timeoutMilliseconds = 60*$timeout;
		$differenceMillis = time() - $lastLogsTimestamp;
		
		//echo "diff = ".$differenceMillis."; act: ".$actualTimeStamp." last: ".$lastLogsTimestamp."\n";
		//echo "timeout: ".$timeoutMilliseconds.", currentDiff: ".$differenceMillis.".\n";
		
		if ($differenceMillis > $timeoutMilliseconds){
			//echo "returning new state\n";
			deleteVirtualSensorTmpVal($virtualSensorID, $tmpValKey);
			return $currentState;
		}
		//echo "returning old state\n";
		
		return $lastLogs = getLastVirtualSensorLog($virtualSensorID);
	}
	
	
	function getDeviceIP($host, $device) {
		$shellCommand = "nmap -sP ".$host."/24 | grep -B2 -i ".$device." 2>&1";
		$output = shell_exec($shellCommand);
		
		if (strlen(stristr($output,$device))>0) {
			preg_match("/\(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\)/", $output, $matches); 
			$deviceIP = $matches[0];
			
			// remove first and last character because we added them to our regExp
			$deviceIP = substr($deviceIP, 1 , strlen($deviceIP)-2); 

			return $deviceIP;
		} else {
			return null;
		}
	}
	
	function wakeup($ip) {
		// contact the iphone on port 62078 to keep wifi on
		$command = "nmap -P0 -sT -p62078 ".$ip." 2>&1";
		shell_exec($command);
	}

	function tryWakeUpWithOldIP($virtualSensorID) {
		global $mysqli;
		global $db_prefix;
		
		$query = "select lv.value from ".$db_prefix."virtual_sensors_log l, ".$db_prefix."virtual_sensors_log_values lv where l.id = lv.log_id and lv.value_key='ip' and l.sensor_id=".$virtualSensorID." and lv.value is not null and LENGTH(lv.value)>0 order by l.time_updated desc limit 1";
	    $result = $mysqli->query($query);
		
		$ip = $result->fetch_assoc();
		wakeup($ip['value']);
		
		//wait a second
		sleep(3);
	}
	
	function trySNMPCheck($parameter, $virtualSensorID) {
		$device = $parameter['mac_client']; //"58:1F:AA:B0:31:2A";
		$host = $parameter['snmp_host']; //"192.168.1.1";
		$community = "public";
		$snmpObj = "1.3.6.1.2.1.3.1.1.2";

		$snmpReturnVals = snmp2_walk($host, $community, $snmpObj);

		$contains = containsDevice($device, $snmpReturnVals);
		
		return $contains;
	}	
	
	function containsDevice($device, $snmpReturnVals) {
		foreach ($snmpReturnVals as $returnVal) {
			$hex = getHexCode($returnVal);
			if (strcasecmp($device, $hex)==0) {
				return 1;
			}
		}
		return 0;
	}
	
	function getHexCode($snmpReutnVal) {
		if (startsWith($snmpReutnVal, 'Hex-STRING: ') == TRUE){
			$hex = str_ireplace('Hex-STRING: ', '', $snmpReutnVal);
			$hex = trim($hex);
			$hex = str_ireplace(' ', ':', $hex);
			return $hex;
		} else {
			return null;
		}
	}
	
	function startsWith($haystack, $needle) {
		return !strncmp($haystack, $needle, strlen($needle));
	}
	
?>
