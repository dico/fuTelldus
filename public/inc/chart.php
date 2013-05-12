    <script src="../lib/packages/rgraph_libraries/RGraph.common.core.js" ></script>
	<script src="../lib/packages/rgraph_libraries/RGraph.common.dynamic.js" ></script>
	<script src="../lib/packages/rgraph_libraries/RGraph.common.tooltips.js" ></script>
	<script src="../lib/packages/rgraph_libraries/RGraph.common.effects.js" ></script>
	<script src="../lib/packages/rgraph_libraries/RGraph.common.key.js" ></script>
	<script src="../lib/packages/rgraph_libraries/RGraph.line.js" ></script>
	<!--[if lt IE 9]><script src="../excanvas/excanvas.js"></script><![endif]-->

	<div class="container">

		<?php
			

			/* Get/set parameters
			--------------------------------------------------------------------------- */
			if (isset($_GET['id'])) {
				$getID = clean($_GET['id']);
			} else {
				echo "<p>Sensor ID is missing...</p>";
				exit();
			}

			$showFromDate = time() - 86400;






			/* Get sensor data
		    --------------------------------------------------------------------------- */
		    $query = "SELECT * FROM ".$db_prefix."sensors WHERE sensor_id='$getID'";
		    $result = $mysqli->query($query);
			$row = $result->fetch_array();


	        $sensorID = trim($row['sensor_id']);
	        echo "<h4>{$row['name']}</h4>";
	        echo "<h5 style='margin-left:10px;'>{$row['clientname']}</h5>";








			echo "<div class='' style='margin-bottom:60px;'>";


		        /* Get sensordata and generate graph
		        --------------------------------------------------------------------------- */
		        //echo "<h5 class='hidden-phone' style='margin-bottom:35px;'>".$lang['Latest readings']."</h5>";

		        $queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' AND time_updated > '$showFromDate' ORDER BY time_updated DESC LIMIT 1000";
		        $resultS = $mysqli->query($queryS);


		        // Set arrays
		        $labels             = array();
		        $tooltips_temp      = array();
		        $tooltips_humidity  = array();
		        $temp_value         = array();
		        $humidity_value     = array();


		        // Collect data
		        $count = 0; // Settings count for view data every hour
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
		            $count++;
		            if ($count == 4) $count = 0;
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
		            <canvas class='linechart hidden-phone' id='$getID' width='990' height='400'>[No canvas support]</canvas>
		            <script>
		                chart = new RGraph.Line('$getID', $temp_value_string, $humidity_value_string);
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
		            <canvas class='linechart hidden-phone' id='$getID' width='990' height='400'>[No canvas support]</canvas>
		            <script>
		                chart = new RGraph.Line('$getID', $temp_value_string);
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
		        echo "<h5>".$lang['Total']."</h5>";

		        $queryNow = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' AND time_updated > '$showFromDate' ORDER BY time_updated DESC LIMIT 1";
		        $resultNow = $mysqli->query($queryNow);
		        $sensorDataNow = $resultNow->fetch_array();

		        $queryS = "SELECT AVG(temp_value), MAX(temp_value), MIN(temp_value), AVG(humidity_value), MAX(humidity_value), MIN(humidity_value) FROM ".$db_prefix."sensors_log WHERE sensor_id='$getID' AND time_updated > '$showFromDate'";
		        $resultS = $mysqli->query($queryS);
		        $sensorData = $resultS->fetch_array();


		        echo "<table class='table table-striped table-hover'>";
		            echo "<tbody>";


		                // Temperature
		            	 echo "<tr>";
		                    echo "<td>".$lang['Temperature']." ".strtolower($lang['Now'])."</td>";
		                    echo "<td>";
		                    	echo round($sensorDataNow['temp_value'], 2)." &deg;";
		                    	echo "<abbr style='margin-left:20px;' class=\"timeago\" title='".date("c", $sensorDataNow['time_updated'])."'>".date("d-m-Y H:i", $sensorDataNow['time_updated'])."</abbr>";
		                    echo "</td>";
		                echo "</tr>";


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
		                if ($sensorDataNow['humidity_value'] > 0) {
		                    echo "<tr>";
		                        echo "<td>".$lang['Humidity']." ".strtolower($lang['Now'])."</td>";
		                        echo "<td>";
		                        	echo round($sensorDataNow['humidity_value'], 2)." %";
		                        	echo "<abbr style='margin-left:20px;' class=\"timeago\" title='".date("c", $sensorDataNow['time_updated'])."'>".date("d-m-Y H:i", $sensorDataNow['time_updated'])."</abbr>";
		                        echo "</td>";
		                    echo "</tr>";
		                }


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

		?>

	</div>