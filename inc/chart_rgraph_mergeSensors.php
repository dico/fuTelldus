<script src="./lib/packages/rgraph_libraries/RGraph.common.core.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.common.dynamic.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.common.tooltips.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.common.effects.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.common.key.js" ></script>
<script src="./lib/packages/rgraph_libraries/RGraph.line.js" ></script>
<!--[if lt IE 9]><script src="../excanvas/excanvas.js"></script><![endif]-->


<?php
    
    $showFromDate = time() - 86400;


    

    /* Get sensors
    --------------------------------------------------------------------------- */
    $query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' AND monitoring='1'";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_array()) {


        $sensorID = trim($row['sensor_id']);
        //echo "<h4>{$row['name']}</h4>";
        $sensorNames[] = "'{$row['name']}'";



        /* Get sensordata and generate graph
        --------------------------------------------------------------------------- */
        $queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='$sensorID' AND time_updated > '$showFromDate' ORDER BY time_updated DESC LIMIT 1000";
        $resultS = $mysqli->query($queryS);


        // Set arrays
        //$sensorNames        = array();
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
        //$sensorNames        = array_reverse($sensorNames);
        $labels             = array_reverse($labels);
        $tooltips_temp      = array_reverse($tooltips_temp);
        $tooltips_humidity  = array_reverse($tooltips_humidity);
        $temp_value         = array_reverse($temp_value);
        $humidity_value     = array_reverse($humidity_value);


        // Aggregate all the data into one string
        $sensorNames_string         = "[" . join(", ", $sensorNames) . "]";
        $temp_value_string          = "[" . join(", ", $temp_value) . "]";
        $humidity_value_string      = "[" . join(", ", $humidity_value) . "]";
        $labels_string              = "['" . join("', '", $labels) . "']";
        $tooltips_temp_string       = "['" . join("', '", $tooltips_temp) . "']";
        $tooltips_humidity_string   = "['" . join("', '", $tooltips_humidity) . "']";

        $tempValueArr[] = $temp_value_string;
        $humidityValueArr[] = $humidity_value_string;
        $labelsArr[] = $labels_string;
        $toolTipsTempArr[] = $tooltips_temp_string;
        $toolTipsHumidityArr[] = $tooltips_humidity_string;


        unset($labels);
        unset($tooltips_temp);
        unset($tooltips_humidity);
        unset($temp_value);
        unset($humidity_value);



    }



    // Generate temperature string
    $tempString = "";
    foreach ($tempValueArr as $key => $value) {
    	$tempString .= "$value, ";
    }

    $tempString = substr($tempString, 0, -2);



     // Generate temperature string
    $tooltipString = "";
    foreach ($toolTipsTempArr as $key => $value) {
    	$tooltipString .= "$value, ";
    }

    $tooltipString = substr($tooltipString, 0, -2);



    
    echo "
        <canvas class='linechart hidden-phone' id='$sensorID' width='990' height='500'>[No canvas support]</canvas>
        <script>
            chart = new RGraph.Line('$sensorID', $tempString);
            chart.Set('chart.background.grid.autofit', true);
            chart.Set('chart.gutter.left', 35);
            chart.Set('chart.gutter.right', 5);
            chart.Set('chart.hmargin', 10);
            chart.Set('chart.tickmarks', 'circle');
            chart.Set('chart.labels', $labels_string);
            chart.Set('chart.tooltips', $tooltipString);
            chart.Set('chart.key', $sensorNames_string);
            chart.Set('chart.xaxispos', 'center');
            chart.Set('key.position', 'gutter');
            chart.Set('key.position.gutter.boxed', false);
            chart.Set('key.position.x', 50);
            chart.Set('key.position.y', 0);
            chart.Set('chart.text.angle', 45);
            chart.Set('chart.gutter.bottom', 50);
            chart.Draw();
        </script>
        ";
       


?>