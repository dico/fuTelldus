<?php

	$paras = array(
		'device' => '58:1F:AA:B0:31:2A', 
		'host' => '192.168.1.1' 
	);
	$data = getVirtualSensorVal($paras);
	echo $data['available']."\n";

	function getVirtualSensorVal($parameter) {
		$device = $parameter['device']; //"58:1F:AA:B0:31:2A";
		$host = $parameter['host']; //"192.168.1.1";
		$community = "public";
		$snmpObj = "1.3.6.1.2.1.3.1.1.2";
		$snmpReturnVals = snmp2_walk($host, $community, $snmpObj);

		$contains = containsDevice($device, $snmpReturnVals);
		return array('available' => $contains);
	}
	
	function containsDevice($device, $snmpReturnVals) {
		foreach ($snmpReturnVals as $returnVal) {
			$hex = getHexCode($returnVal);
//			echo $hex."\n";
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