<?php
namespace virtual_devices\device_as_sensor;
/*
 Needs:
*/

/*	$paras = array(
		'deviceID' => '123456', // telldus device ID
		'onDesc' => '', //return
		'offDesc' => '', //return
	);
	status=1 --> device on, status=2 --> device off
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
			'device_id' => array('Telldus device id' => 'text'), 
			'on_desc' => array('Description when on' => 'text'), 
			'off_desc' => array('Description when off' => 'text'), 
			'status' => array('Status' => 'return')
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
				if (isset($lastLogValues['status']) and $lastLogValues['status']==1) {
					$widget.= "<img src='". $myPath."/light_on.png' alt='icon' />";
					$onText = getVirtualSensorConfigToKey($virtualSensorID, "on_desc");
					$widget.= "&nbsp;".$onText."&nbsp;";	
				} else {
					$widget.= "<img src='". $myPath."/light_off.png' alt='icon' />";
					$offText = getVirtualSensorConfigToKey($virtualSensorID, "off_desc");
					$widget.= "&nbsp;".$offText."&nbsp;";	
				}
			$widget.= "</div>";

			$widget.= "<div class='sensor-timeago'>";
				$timeUpdatedByInsertedLog = getLastVirtualSensorLogTimestamp($virtualSensorID);
				if ($timeUpdatedByInsertedLog==0){
					$timeUpdatedByInsertedLog = time();
				}
				
				$widget.= "<abbr class=\"timeago\" title='".date("c", $timeUpdatedByInsertedLog)."'>".date("d-m-Y H:i", $timeUpdatedByInsertedLog)."</abbr>";
			$widget.= "</div>";
			$widget.= "</div>";	
		
		return $widget;
	}
	
	function getVirtualSensorVal($parameter, $virtualSensorID) {
		$device = $parameter['device_id'];
		
		$actState = trim(getDeviceState($device));
		$returnValArr = array(
			'status'=>$actState
		);
		return $returnValArr;
	}

?>
