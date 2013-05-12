<?php

	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);





	/* add sensor
	--------------------------------------------------------------------------- */
	if ($action == "addSensor") {

		$query = "UPDATE ".$db_prefix."sensors SET monitoring='". 1 ."' WHERE sensor_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=sensors&msg=01");
		exit();
	}


	/* remove sensor
	--------------------------------------------------------------------------- */
	if ($action == "removeSensor") {

		$query = "UPDATE ".$db_prefix."sensors SET monitoring='". 0 ."' WHERE sensor_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ?page=sensors&msg=02");
		exit();
	}



	/* Set public
	--------------------------------------------------------------------------- */
	if ($action == "setSensorPublic") {

		$query = "UPDATE ".$db_prefix."sensors SET public='". 1 ."' WHERE sensor_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ".$_SERVER['HTTP_REFERER']."");
		exit();
	}

	/* Set non public
	--------------------------------------------------------------------------- */
	if ($action == "setSensorNonPublic") {

		$query = "UPDATE ".$db_prefix."sensors SET public='". 0 ."' WHERE sensor_id='".$getID."'";
		$result = $mysqli->query($query);

		// Redirect
		header("Location: ".$_SERVER['HTTP_REFERER']."");
		exit();
	}

?>