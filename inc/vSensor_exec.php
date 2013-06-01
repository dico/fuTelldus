<?php


	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);


	/*
	--------------------------------------------------------------------------- */
	if ($action == "activatePlugin") {
		$plugin_name = clean($_GET['plugin_name']);
		$version = clean($_GET['version']);
		activatePlugin($plugin_name, $getID, $version);
		
		header("Location: ?page=settings&view=virtualsensors_types&msg=01");
		exit();
	}
	
	
	if ($action == "disablePlugin") {
		disablePlugin($getID);
		
		header("Location: ?page=settings&view=virtualsensors_types&msg=02");
		exit();
	}
	
	if ($action == "updatePlugin") {
		$version = clean($_GET['version']);
		updatePlugin($getID, $version);
		
		header("Location: ?page=settings&view=virtualsensors_types&msg=04");
		exit();
	}

	if ($action == "hidePlugin") {
		$toHide = clean($_GET['toHide']);
	
		$query = "UPDATE ".$db_prefix."virtual_sensors_types  SET hidden='".$toHide."' WHERE type_int='".$getID."'";
		$result = $mysqli->query($query);
		
		header("Location: ?page=settings&view=virtualsensors_types&msg=03");
		exit();
	}
	
?>