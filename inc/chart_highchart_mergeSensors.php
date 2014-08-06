<script src="lib/packages/Highstock-1.3.1/js/highstock.js"></script>
<script src="lib/packages/Highstock-1.3.1/js/modules/exporting.js"></script>

<script type="text/javascript">

$(function() {
	$('#container').highcharts('StockChart', {

		chart: {
		},

		rangeSelector: {
			enabled: true,
			buttons: [{
				type: 'hour',
				count: 1,
				text: '1h'
			},{
				type: 'hour',
				count: 12,
				text: '12h'
			},{
				type: 'day',
				count: 1,
				text: '1d'
			}, {
				type: 'week',
				count: 1,
				text: '1w'
			}, {
				type: 'month',
				count: 1,
				text: '1m'
			}, {
				type: 'month',
				count: 6,
				text: '6m'
			}, {
				type: 'year',
				count: 1,
				text: '1y'
			}, {
				type: 'all',
				text: 'All'
			}],
			selected: 4
		},

	        legend: {
			align: "center",
			layout: "horizontal",
			enabled: true,
			verticalAlign: "bottom"
		},

		tooltip: {
		        valueSuffix: '°C'
		    },

		xAxis: {
			type: 'datetime',
		}, 

		yAxis: {
			title: {
				text: 'Temperature (°C)',
			},
			labels: {
			    formatter: function() {
					return this.value +'\u00B0C';
			    },
			    style: {
					color: '#777'
			    }
			},
		}, 

		navigator : {
			enabled : false
		},

		scrollbar : {
			enabled : false
		},

		series: [

<?php

	// Set how long back you want to pull data
    $showFromDate = time() - 86400 * $config['chart_max_days']; // 86400 => 24 hours * 10 days

    /* TEMP SENSOR 01: Get sensors
    --------------------------------------------------------------------------- */
    $query = "SELECT * FROM ".$db_prefix."sensors WHERE user_id='{$user['user_id']}' AND monitoring='1'";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_array()) {

        unset($temp_values);
        $joinValues = "";

        /* Get sensordata and generate graph
        --------------------------------------------------------------------------- */
        $queryS = "SELECT * FROM ".$db_prefix."sensors_log WHERE sensor_id='{$row["sensor_id"]}' AND time_updated > '$showFromDate' ORDER BY time_updated ASC";
        $resultS = $mysqli->query($queryS);

        while ($sensorData = $resultS->fetch_array()) {
            $db_tempValue = trim($sensorData["temp_value"]);

            $timeJS = $sensorData["time_updated"] * 1000;
            $temp_values[]        = "[" . $timeJS . "," . round($db_tempValue, 2) . "]";
        }

        $joinValues = join($temp_values, ',');

echo <<<end
		{
			name: '{$row["name"]}',
			data: [$joinValues],
			type: 'spline',
			tooltip: {
				valueDecimals: 1
			}
		},
end;

}
?>
		],

	});
});


</script>
<div id="container" style="height: 500px; min-width: 500px"></div>
