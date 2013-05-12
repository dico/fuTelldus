<script src="./lib/packages/rgraph_libraries/RGraph.common.core.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.common.dynamic.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.common.tooltips.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.common.effects.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.common.key.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.line.js" ></script>
<!--[if lt IE 9]><script src="../excanvas/excanvas.js"></script><![endif]-->

<script src="./lib/packages/jonthornton-jquery-timepicker-1.0.11-0-gc3f8ede/jquery.timepicker.min.js" ></script>
<link href="./lib/packages/jonthornton-jquery-timepicker-1.0.11-0-gc3f8ede/jquery.timepicker.css" rel="stylesheet">

<script>
	
	$(document).ready(function() {
		$('#dateFrom').datepicker({
			constrainInput: true,   // prevent letters in the input field
			dateFormat: 'yy-mm-dd',  // Date Format used
			firstDay: 1,  // Start with Monday
		});
		
		$('#dateTo').datepicker({
			constrainInput: true,   // prevent letters in the input field
			dateFormat: 'yy-mm-dd',  // Date Format used
			firstDay: 1,  // Start with Monday
		});


		$('#timeFrom').timepicker({ 'timeFormat': 'H:i' });
		$('#timeTo').timepicker({ 'timeFormat': 'H:i' });

		$('#tooltip').tooltip();
	});

</script>

<?php
	
	
	if (!$telldusKeysSetup) {
		echo "No keys for Telldus has been added... Keys can be added under <a href='?page=settings&view=user'>your userprofile</a>.";
		exit();
	}



	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);




	/* Get values
	--------------------------------------------------------------------------- */
	if (isset($_POST['submit'])) {
		$sensorID = clean($_POST['sensorID']);

		$dateFrom = clean($_POST['dateFrom']);
		$timeFrom = clean($_POST['timeFrom']);

		$dateTo = clean($_POST['dateTo']);
		$timeTo = clean($_POST['timeTo']);

		$jump = clean($_POST['jump']);

		header("Location: ?page=report&sensorID=$sensorID&dateFrom=$dateFrom&timeFrom=$timeFrom&dateTo=$dateTo&timeTo=$timeTo&jump=$jump");
		exit();
	}


	if (isset($_GET['sensorID'])) {
		$sensorID = clean($_GET['sensorID']);

		$dateFrom = clean($_GET['dateFrom']);
		$timeFrom = clean($_GET['timeFrom']);

		$dateTo = clean($_GET['dateTo']);
		$timeTo = clean($_GET['timeTo']);

		$jump = clean($_GET['jump']);
		if ($jump == 0) $jump = 1;


	} else {
		$dateFrom = date("Y-m-d", strtotime(' -1 day'));
		$timeFrom = "00:00";

		$dateTo = date("Y-m-d");
		$timeTo = "23:59";

		$jump = 4;
	}



	// Create unix timestamps
	list($yearFrom, $monthFrom, $dayFrom) = explode("-", $dateFrom);
	list($hourFrom, $minFrom) = explode(":", $timeFrom);

	list($yearTo, $monthTo, $dayTo) = explode("-", $dateTo);
	list($hourTo, $minTo) = explode(":", $timeTo);

	$dateFrom = mktime($hourFrom, $minFrom, 00, $monthFrom, $dayFrom, $yearFrom);
	$dateTo = mktime($hourTo, $minTo, 00, $monthTo, $dayTo, $yearTo);




	/* Check for errors
	--------------------------------------------------------------------------- */
	if (isset($_GET['sensorID'])) {
		$error = false;

		if ($dateFrom > $dateTo) $error = true;
		if (date("d", $dateFrom) < 1 || date("d", $dateFrom) > 31) $error = true;
		if (date("d", $dateTo) < 1 || date("d", $dateTo) > 31) $error = true;
	}








	echo "<h4>".$lang['Report']."</h4>";



	/* Form
	--------------------------------------------------------------------------- */
	echo "<form action='?page=report' method='POST'>";
		echo "<table width='100%'>";

			echo "<tr>";
				echo "<td>".$lang['Sensor']."</td>";
				echo "<td>".$lang['Date from']."</td>";
				echo "<td>".$lang['Date to']."</td>";
				echo "<td>".$lang['Jump']."</td>";
				echo "<td></td>";
			echo "</tr>";

			echo "<tr>";

				echo "<td>";
					$query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='".$user['user_id']."' AND monitoring='1' ORDER BY name ASC LIMIT 100";
	   				$result = $mysqli->query($query);

	   				echo "<select name='sensorID'>";
	   				while ($row = $result->fetch_array()) {
	   					if ($sensorID == $row['sensor_id'])
	   						echo "<option value='{$row['sensor_id']}' selected='selected'>{$row['sensor_id']}: {$row['name']}</option>";

	   					else
	   						echo "<option value='{$row['sensor_id']}'>{$row['sensor_id']}: {$row['name']}</option>";
	   				}
	   				echo "</select>";
				echo "</td>";

				echo "<td>";
					echo "<input style='width:100px;' type='text' name='dateFrom' id='dateFrom' value='".date("Y-m-d", $dateFrom)."' />";
					echo "<input style='width:50px; margin-left:5px;' type='text' name='timeFrom' id='timeFrom' value='".date("H:i", $dateFrom)."' />";
				echo "</td>";

				echo "<td>";
					echo "<input style='width:100px;' type='text' name='dateTo' id='dateTo' value='".date("Y-m-d", $dateTo)."' />";
					echo "<input style='width:50px; margin-left:5px;' type='text' name='timeTo' id='timeTo' value='".date("H:i", $dateTo)."' />";
				echo "</td>";


				echo "<td>";
					echo "<input style='width:20px; margin-left:5px;' type='text' name='jump' id='jump' value='$jump' /> ";
					echo "<a href='#' id='tooltip' data-toggle='tooltip' data-placement='bottom' title='".$lang['Jump description']."'>?</a>";
				echo "</td>";


				echo "<td><input class='btn btn-primary' type='submit' name='submit' value='".$lang['Show data']."' /></td>";
			echo "</tr>";

		echo "</table>";
	echo "</form>";














	if (isset($_GET['sensorID']) && !$error) {

		echo "<div class='well'>";

	        
	        /* Get sensordata and generate graph
	        --------------------------------------------------------------------------- */
	        $queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$sensorID' AND (time_updated > '$dateFrom' AND time_updated < '$dateTo') ORDER BY time_updated DESC LIMIT 1000";
	        $resultS = $mysqli->query($queryS);


	        // Set arrays
	        $labels             = array();
	        $tooltips_temp      = array();
	        $tooltips_humidity  = array();
	        $temp_value         = array();
	        $humidity_value     = array();


	        // Collect data
	        $count = 1; // Settings count for view data every hour
	        $negativeValue = false;
	        $maxValue = 0;
	        $minValue = 0;
	        
	        while ($sensorData = $resultS->fetch_array()) {

	            $db_tempValue = trim($sensorData["temp_value"]);
	            $db_humidityValue = trim($sensorData["humidity_value"]);

	            $showHumidity = false;
	            if ($sensorData["humidity_value"] > 0) $showHumidity = true;




	            if ($count == 1) {
	                $labels[]               = date("H:i", $sensorData["time_updated"]);
	                $tooltips_temp[]        = "<b>".$db_tempValue."&deg;</b> &nbsp; (" . date("H:i", $sensorData["time_updated"]) . ")";
	                $tooltips_humidity[]    = "$db_humidityValue %";
	                $temp_value[]           = $db_tempValue;
	                $humidity_value[]       = $db_humidityValue;

	                // Check if chart has negative values
	                if ($db_tempValue < 0) $negativeValue = true;

	                // Get max/min values in chart
	                if ($db_tempValue > $maxValue) $maxValue = $db_tempValue;
	                if ($db_tempValue < $minValue) $minValue = $db_tempValue;

	            }


	            // Add to count or reset
	            
	            if ($count == $jump) $count = 1;
	            else $count++;
	        }


	        // Round the max/min values for use as yaxis max/min on chart
	        $minValue = round($minValue) - 2;
	        $maxValue = round($maxValue) + 2;



	        // Reverse array to preview newest update at right in chart
	        $labels             = array_reverse($labels);
	        $tooltips_temp      = array_reverse($tooltips_temp);
	        $tooltips_humidity  = array_reverse($tooltips_humidity);
	        $temp_value         = array_reverse($temp_value);
	        $humidity_value     = array_reverse($humidity_value);


	        // Aggregate all the data into one string
	        $temp_value_string          = "[" . join(", ", $temp_value) . "]";
	        $humidity_value_string      = "[" . join(", ", $humidity_value) . "]";
	        $labels_string              = "['" . join("', '", $labels) . "']";
	        $tooltips_temp_string       = "['" . join("', '", $tooltips_temp) . "']";
	        $tooltips_humidity_string   = "['" . join("', '", $tooltips_humidity) . "']";


	        if ($showHumidity) {
	            echo "
	            <canvas class='linechart hidden-phone' id='$sensorID' width='900' height='400'>[No canvas support]</canvas>
	            <script>
	                chart = new RGraph.Line('$sensorID', $temp_value_string, $humidity_value_string);
	                chart.Set('chart.background.grid.autofit', true);
	                chart.Set('chart.gutter.left', 35);
	                chart.Set('chart.gutter.right', 5);
	                chart.Set('chart.hmargin', 10);
	                chart.Set('chart.tickmarks', 'circle');
	                chart.Set('chart.labels', $labels_string);
	                chart.Set('chart.tooltips', $tooltips_temp_string, $tooltips_humidity_string);
	                chart.Set('chart.key', ['".$lang['Temperature']."', '".$lang['Humidity']."']);
	            ";

	            if ($negativeValue == true) echo "chart.Set('chart.xaxispos', 'center');";

	            echo "
	                chart.Set('key.position', 'gutter');
	                chart.Set('key.position.gutter.boxed', false);
	                chart.Set('key.position.x', 700);
	                chart.Set('key.position.y', 0);
	                chart.Set('chart.text.angle', 45);
	                chart.Set('chart.gutter.bottom', 50);
	                chart.Draw();
	            </script>
	            ";
	        } else {
	            echo "
	            <canvas class='linechart hidden-phone' id='$sensorID' width='900' height='400'>[No canvas support]</canvas>
	            <script>
	                chart = new RGraph.Line('$sensorID', $temp_value_string);
	                chart.Set('chart.background.grid.autofit', true);
	                chart.Set('chart.gutter.left', 35);
	                chart.Set('chart.gutter.right', 5);
	                chart.Set('chart.hmargin', 10);
	                chart.Set('chart.tickmarks', 'circle');
	                chart.Set('chart.labels', $labels_string);
	                chart.Set('chart.tooltips', $tooltips_temp_string);
	                chart.Set('chart.key', ['".$lang['Temperature']."']);
	            ";

	            if ($negativeValue == true) echo "chart.Set('chart.xaxispos', 'center');";

	            //echo "chart.Set('chart.ymax', $maxValue);";
	            //echo "chart.Set('chart.ymin', $minValue);";

	            echo "
	                chart.Set('key.position', 'gutter');
	                chart.Set('key.position.gutter.boxed', false);
	                chart.Set('key.position.x', 780);
	                chart.Set('key.position.y', 0);
	                chart.Set('chart.text.angle', 45);
	                chart.Set('chart.gutter.bottom', 50);
	                chart.Draw();
	            </script>
	            ";
	        }

	        unset($labels);
	        unset($tooltips_temp);
	        unset($tooltips_humidity);
	        unset($temp_value);
	        unset($humidity_value);





	        /* Max, min avrage
	        --------------------------------------------------------------------------- */
	        $queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='$sensorID' AND (time_updated > '$dateFrom' AND time_updated < '$dateTo') ORDER BY time_updated DESC LIMIT 1000";
	        $resultS = $mysqli->query($queryS);
	        $sensorData = $resultS->fetch_array();

	        echo "<div style='margin-top:40px;'>";
		        echo "<table class='table table-striped table-hover'>";
		            echo "<tbody>";


		                // Temperature
		                echo "<tr>";
		                    echo "<td>".$lang['Avrage']." ".strtolower($lang['Temperature'])."</td>";
		                    echo "<td>".round($sensorData['AVG(temp_value)'], 2)." &deg;</td>";
		                echo "</tr>";

		                echo "<tr>";
		                    echo "<td>".$lang['Max']." ".strtolower($lang['Temperature'])."</td>";
		                    echo "<td>".round($sensorData['MAX(temp_value)'], 2)." &deg; </td>";
		                echo "</tr>";

		                echo "<tr>";
		                    echo "<td>".$lang['Min']." ".strtolower($lang['Temperature'])."</td>";
		                    echo "<td>".round($sensorData['MIN(temp_value)'], 2)." &deg; </td>";
		                echo "</tr>";




		                // Humidity
		                if ($sensorData['AVG(humidity_value)'] > 0) {
		                    echo "<tr>";
		                        echo "<td>".$lang['Avrage']." ".strtolower($lang['Humidity'])."</td>";
		                        echo "<td>".round($sensorData['AVG(humidity_value)'], 2)." %</td>";
		                    echo "</tr>";
		                }

		                if ($sensorData['MAX(humidity_value)'] > 0) {
		                    echo "<tr>";
		                        echo "<td>".$lang['Max']." ".strtolower($lang['Humidity'])."</td>";
		                        echo "<td>".round($sensorData['MAX(humidity_value)'], 2)." %</td>";
		                    echo "</tr>";
		                }

		                if ($sensorData['MIN(humidity_value)'] > 0) {
		                    echo "<tr>";
		                        echo "<td>".$lang['Min']." ".strtolower($lang['Humidity'])."</td>";
		                        echo "<td>".round($sensorData['MIN(humidity_value)'], 2)." %</td>";
		                    echo "</tr>";
		                }



		            echo "</tbody>";
		        echo "</table>";
			echo "</div>";
		echo "</div>";
	}







	/* Show errormessage if error
	--------------------------------------------------------------------------- */
	if ($error) {
		echo "<div class='alert alert-error'>".$lang['Wrong timeformat']."</div>";	
	}


?>