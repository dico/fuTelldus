<?php


	

	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);



	/* Max, min avrage
    --------------------------------------------------------------------------- */
    //$querySensorData = "SELECT * FROM ".$db_prefix."sensors WHERE sensor_id='$getID'";
    $sensorResults = $mysqli->query("SELECT * FROM ".$db_prefix."sensors WHERE sensor_id='$getID' LIMIT 1");
    $db_sensor = $sensorResults->fetch_array();



    /* Headline
	--------------------------------------------------------------------------- */
	echo "<h3>{$lang['Sensor']}: {$db_sensor['name']}</h3>";



	/* Public
	--------------------------------------------------------------------------- */
	echo "<div style='float:right; margin-top:-45px; margin-right:20px;'>";

		if ($db_sensor['public'] == 1) {
			echo "<a class='btn btn-success' href='?page=sensors_exec&action=setSensorNonPublic&id=$getID'>{$lang['Public']}</a>";
		}

		else {
			echo "<a class='btn btn-inverse' href='?page=sensors_exec&action=setSensorPublic&id=$getID'>{$lang['Non public']}</a>";
		}


	echo "</div>";






	/* Max, min avrage
    --------------------------------------------------------------------------- */
    $queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID'";
    $resultS = $mysqli->query($queryS);
    $sensorData = $resultS->fetch_array();

    echo "<div class='well'>";
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






    echo "<div class='well'>";

		require_once 'HTTP/OAuth/Consumer.php';
		$consumer = new HTTP_OAuth_Consumer(constant('PUBLIC_KEY'), constant('PRIVATE_KEY'), constant('TOKEN'), constant('TOKEN_SECRET'));

		$params = array('id'=> $getID);
		$response = $consumer->sendRequest(constant('REQUEST_URI').'/sensor/info', $params, 'GET');



		/* Get and extract the XML data
		--------------------------------------------------------------------------- */
		$xmlString = $response->getBody();
		$xmldata = new SimpleXMLElement($xmlString);



		echo "<table class='table table-striped table-hover'>";
			echo "<tbody>";

				echo "<tr>";
					echo "<td>".$lang['ID']."</td>";
					echo "<td>".$xmldata->id."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Client name']."</td>";
					echo "<td>".$xmldata->clientName."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Name']."</td>";
					echo "<td>".$xmldata->name."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Last update']."</td>";
					echo "<td>";
						$lastUpdate = trim($xmldata->lastUpdated);
						echo date("H:i:s d-m-y", $lastUpdate) . " &nbsp; (" . ago($lastUpdate) . ")";
					echo "</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Ignored']."</td>";
					echo "<td>".$xmldata->ignored."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Editable']."</td>";
					echo "<td>".$xmldata->editable."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Temperature']."</td>";
					echo "<td>".$xmldata->data[0]['value']."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Humidity']."</td>";
					echo "<td>".$xmldata->data[1]['value']."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Protocol']."</td>";
					echo "<td>".$xmldata->protocol."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Sensor ID']."</td>";
					echo "<td>".$xmldata->sensorId."</td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Timezone offset']."</td>";
					echo "<td>".$xmldata->timezoneoffset."</td>";
				echo "</tr>";

			echo "</tbody>";
		echo "</table>";
	echo "</div>";


	
	echo "<div style='text-align:right;'>";
		echo "<a style='margin-right:15px;' target='_blank' href='./public/chart.php?id=$getID'>Public chart</a>";
		echo "<a style='margin-right:15px;' target='_blank' href='./public/xml_sensor.php?sensorID=$getID'>XML Latest values</a>";
		echo "<a style='margin-right:15px;' target='_blank' href='./public/xml_sensor_log.php?sensorID=$getID'>XML Values last day</a>";
	echo "</div>";

?>






<h3><?php echo $lang['Latest readings']; ?></h3>

<div class="well">
	<?php

		$query = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' ORDER BY time_updated DESC LIMIT 100";
	    $result = $mysqli->query($query);


	    echo "<table class='table table-striped table-hover'>";
			
	    	echo "<thead>";
	    		echo "<tr>";
	    			//echo "<th>Sensor ID</th>";
	    			//echo "<th>Name</th>";
	    			echo "<th>".$lang['Time']."</th>";
	    			echo "<th>".$lang['Temperature']."</th>";
	    			echo "<th>".$lang['Humidity']."</th>";
	    		echo "</tr>";
	    	echo "</thead>";

			echo "<tbody>";
			    while ($row = $result->fetch_array()) {
			    	echo "<tr>";
			    		//echo "<td>{$row['sensor_id']}</td>";
			    		//echo "<td></td>";
			    		echo "<td>".date("H:i:s d-m-y", $row['time_updated'])." &nbsp; (".ago($row['time_updated']).")</td>";
			    		echo "<td>{$row['temp_value']} &deg;</td>";
			    		echo "<td>{$row['humidity_value']} %</td>";
			    	echo "</tr>";
			    }
			echo "<tbody>";

	   	echo "</table>";


	?>
</div>