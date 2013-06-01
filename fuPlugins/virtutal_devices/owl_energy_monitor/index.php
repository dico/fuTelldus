<?php
namespace virtual_devices\owl_energy_monitor;
/*
 Needs:
	 owl device installed correctly with drivers and server electricowl_2.3.tgz
	 --> server have to be started with parameters: /usr/local/bin/cm160server -t /cm160_data.db.txt -q > /var/log/owl160 (init-script)
	 this plugin will read the new entries in the generated TXT file and not directly to the sensor!!! Take care that the TXT file is written
	 periodically
	 after a complete read of the file, the file will be deleted
*/

/*	$paras = array(
		'datapath' => '/cm160_data.db.txt', // path to SQLlite db
		'energy_consumption' => '', //return
		'voltage' => '', // text
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
			'datafilepath' => array('Path to OWL-export file' => 'text'), 
			'voltage' => array('Voltage' => 'text'),
			'energy_consumption' => array('Energy consumption' => 'return') 
		);
	}
	
	// marks that the plugin is supporting charts
	// maybe it will transform the data before it will be shown in
	// UI
	function editChartData($virtualSensorID, $chartDataArray) {
		// round each value and convert it into Watt
		$newChartDataArray = array();
		while (list($returnKey, $returnValues) = each($chartDataArray)) { 
			
			//if returnKey == 'energy_consumption' --> convert and round
			if ($returnKey=='energy_consumption') {
				while (list($timestamp, $value) = each($returnValues)) { 
					$consumptionAmpere = round($value,2);
					$voltageConfig = getVirtualSensorConfigToKey($virtualSensorID, "voltage");
					$consumptionWatt = $consumptionAmpere * $voltageConfig;
					$newChartDataArray[$returnKey][$timestamp] = $consumptionWatt;
				}
			} else {
				$newChartDataArray[$returnKey][$timestamp] = $value;
			}
		}
		
		return $newChartDataArray;
	}
	
	// chance to redefine the description and the suffix for the axis
	// like they will shown on the UI
	// --> iterate over the array, included is another array, keys:
	// 0 --> position, don't change
	// 1 --> description
	// 2 --> suffix
	function overwriteChartAxisDefinition($axisDefinition) {
		$newAxisDefinition = array();
		while (list($value_key, $configArray) = each($axisDefinition)) { 
			if ($value_key == 'energy_consumption') {
				$configArray[2] = " W";
			}
			$newAxisDefinition[$value_key] = $configArray;
		}
		return $newAxisDefinition;
	}
	
	function getDashBoardWidget($lastLogValues, $virtualSensorID) {
		$myPath = getPluginPathToVSensorId($virtualSensorID);
	
		$consumptionAmpere = round($lastLogValues['energy_consumption'],2);
		$voltageConfig = getVirtualSensorConfigToKey($virtualSensorID, "voltage");
		$consumptionWatt = $consumptionAmpere * $voltageConfig;

		$widget = "";
		$widget.="<div class='sensor-blocks well'>";

			$widget.= "<div class='sensor-name'>";
				$widget.= getVirtualSensorDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-location'>";
				$widget.= getVirtualSensorTypeDescriptionToSensorId($virtualSensorID);
			$widget.= "</div>";

			$widget.= "<div class='sensor-temperature'>";
					$widget.= "<img src='". $myPath."/icon.png' alt='icon' />";
					$widget.= "&nbsp;".$consumptionWatt."&nbsp;W&nbsp;<span style='font-size:small'>".$consumptionAmpere."&nbsp;A</span>";	
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

		$datafilepath=$parameter['datafilepath']; //"/cm160_data.db.txt";

		$row = 1;
		$avgConsumption = 0;
		if (($handle = fopen($datafilepath, "r")) !== FALSE) {
		    $count=0;

		    while (($data = fgetcsv($handle, 1000, " ")) !== FALSE) {
		        $num = count($data);

		        $time = $data[0];
		        $date = $data[1];
		        $consumption = $data[2];
		        $footprint = $data[3];

		        $avgConsumption = $avgConsumption + $consumption;
		        $count++;
		    }

		    if ($count>0) {
		        $avgConsumption = $avgConsumption / $count;
		    } else {
		        $avgConsumption = -1;
		    }
		    fclose($handle);
		    unlink($datafilepath);
		    touch($datafilepath);
		}


		$returnValArr = array();
	 	if ($avgConsumption >= 0) {
			$returnValArr = array('energy_consumption'=>$avgConsumption);
		} else {
			$returnValArr = getLastVirtualSensorLog($virtualSensorID);
		}

		return $returnValArr;
	}

	
?>
