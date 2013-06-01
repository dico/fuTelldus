<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once("../../lib/config.inc.php");
	require_once("../../lib/base.inc.php");

	// Create DB-instance
	$mysqli = new Mysqli($host, $username, $password, $db_name); 

	
	// get parameter
	$id = clean($_GET['id']);
	$type = clean($_GET['type']);
	$name = clean($_GET['name']);
	
	$tempColor = "#6698FF";
	$humiColor = "#99C68E";
	
	/* Generate the chart
	-------------------------------------------------------*/
	if ($type == "sensor") {
		echo generateSensorChart($id, $name);
	}
	
	if ($type == "virtual") {
		echo generateVirtualSensorChart($id, $name);
	}
	
	if ($type == "device") {
		echo generateDeviceChart($id, $name);
	}
	
	echo "<div id='container' style='height: 500px; min-width: 500px'></div>";

	
	function generateVirtualSensorChart($id, $name) {
		
		// determine, how the hichart object should be configured:
		// ask the plugin abaout returnData and how it should be displayed
		// the array-position now have to be the same like later --> the plugin 
		// is responsible for that
		
		$axisArray = getVirtualSensorChartAxis($id);
		$yaxis = "";
		$series = "";
		$callback = "";
		$axisInfo = array();
		foreach ($axisArray as $axisConfig) {
			$position = $axisConfig[0];
			$description = $axisConfig[1];
			$suffix = $axisConfig[2];
			$yaxis .= "{
					title: {
						text: '$description',
					},
					labels: {
						formatter: function() {
							return this.value +'$suffix';
						},
					},
				}, ";
			$series .= "{
					name: '$description',
					data : data[$position],
					dataGrouping: {
						enabled: false
					},
					tooltip: {
						valueSuffix: '$suffix'
					},		
				}";
			$callback .= "
				chart.series[$position].setData(data[$position]);";
			
			$axisInfo['yaxis'] = $yaxis;
			$axisInfo['series'] = $series;
			$axisInfo['callback'] = $callback;
		}
								
		//print_r($axisInfo);
		
		return getVirtualSensorJavaScript($id, $axisInfo);
	}
	
	function getVirtualSensorJavaScript($id, $axisInfo) {
		$script = "";
		$script .= "
				<script type='text/javascript'>
				$(function() {
					Highcharts.setOptions({
						global: {
							useUTC: false
						}
					});
					// See source code from the JSONP handler at https://github.com/highslide-software/highcharts.com/blob/master/samples/data/from-sql.php
					var startTime=1;
					$.getJSON('http://telldus.hca-erfurt.de/inc/charts/getChartDataJSON.php?id={$id}&type=virtual&start='+startTime+'&callback=?', function(data) {
						
								
						// create the chart
						$('#container').highcharts('StockChart', {
							chart : {
								type: 'spline',
								zoomType: 'x'
							},
							
							navigator : {
								adaptToUpdatedData: false,
								series : {
									data : data[1]
								}
							},

							scrollbar: {
								liveRedraw: false
							},
							
							rangeSelector : {
								buttons: [{
									type: 'hour',
									count: 1,
									text: '1h'
								}, {
									type: 'day',
									count: 1,
									text: '1d'
								}, {
									type: 'week',
									count: 1,
									text: '1w'
								},{
									type: 'month',
									count: 1,
									text: '1m'
								}, {
									type: 'year',
									count: 1,
									text: '1y'
								}, {
									type: 'all',
									text: 'All'
								}],
								inputEnabled: false, // it supports only days
								selected : 5 // all
							},
							
							legend: {
								enabled: false,
							},
							
							series: [";
		
		// add series from array;
		$script .= $axisInfo['series'];
								
		$script .="					],
							yAxis: [";
							
		// add yaxis from array
		$script .= $axisInfo['yaxis'];
								
		$script .= "					],
							
							xAxis : {
								type: 'datetime',
								events : {
									afterSetExtremes : afterSetExtremes
								},
								minRange: 3600 // one hour
							},
						});
					});
				});

				/**
				 * Load new data depending on the selected min and max
				 */
				function afterSetExtremes(e) {

					var url,
						currentExtremes = this.getExtremes(),
						range = e.max - e.min;
					var chart = $('#container').highcharts();
					chart.showLoading('Loading data from server...');
					
					$.getJSON('http://telldus.hca-erfurt.de/inc/charts/getChartDataJSON.php?id={$id}&type=virtual&start='+ Math.round(e.min) +
							'&end='+ Math.round(e.max) +'&callback=?', function(data) {";
		
		// add callback from array
		$script .= $axisInfo['callback'];
					
		$script .= "		chart.hideLoading();
					});
				}
				</script> ";
		return $script;
	}
	
	function generateDeviceChart($id, $name) {
		global $tempColor;
		global $humiColor;
		
		return "
			<script type='text/javascript'>
				$(function() {
					Highcharts.setOptions({
						global: {
							useUTC: false
						}
					});
					// See source code from the JSONP handler at https://github.com/highslide-software/highcharts.com/blob/master/samples/data/from-sql.php
					//var startTime=new Date().getTime()-86400000; //one day
					var startTime=1;
					$.getJSON('http://telldus.hca-erfurt.de/inc/charts/getChartDataJSON.php?id={$id}&type=device&start='+startTime+'&callback=?', function(data) {
						
								
						// create the chart
						$('#container').highcharts('StockChart', {
							chart : {
								type: 'area',
								renderTo: 'weight-chart',
								zoomType: 'x'
							},
							
							navigator : {
								adaptToUpdatedData: false,
								series : {
									data : data
								}
							},

							scrollbar: {
								liveRedraw: false
							},
							
							rangeSelector : {
								buttons: [{
									type: 'hour',
									count: 1,
									text: '1h'
								}, {
									type: 'day',
									count: 1,
									text: '1d'
								}, {
									type: 'week',
									count: 1,
									text: '1w'
								},{
									type: 'month',
									count: 1,
									text: '1m'
								}, {
									type: 'year',
									count: 1,
									text: '1y'
								}, {
									type: 'all',
									text: 'All'
								}],
								inputEnabled: false, // it supports only days
								selected : 5 // all
							},
							
							legend: {
								enabled: false,
							},
							
							series: [{
									name: 'State',
									data : data,
									dataGrouping: {
										enabled: false
									},
									tooltip: {
										formatter: function(){
											if (data.label==1){
												return 'on';
											} else {
												return 'off';
											}
										},
									},		
									color: '{$tempColor}'
								}
							],
							
							yAxis: [{
									title: {
										text: 'State',
										style: {
											color: '{$tempColor}'
										}
									},
									minorTickInterval: 1,
									gridLineWidth: 0,
									labels: {
										formatter: function() {
											if (this.value==1){
												return 'on';
											} else if (this.value==0){
												return 'off';
											}
										},
										align: 'right',
										x:-10,
										y:5,
										style: {
											color: '{$tempColor}'
										}
									},
								}
							],
							
							xAxis : {
								type: 'datetime',
								ordinal: false,
								events : {
									afterSetExtremes : afterSetExtremes
								},
								//minRange: 3600, // one hour
								/*plotBands: [{ // Light air
										from: Date.UTC(2009, 9, 5),
										to: Date.UTC(2013, 5, 25),
										color: 'rgba(68, 170, 213, 0.1)'
									}],*/
							},
						});
					});
				});

				/**
				 * Load new data depending on the selected min and max
				 */
				function afterSetExtremes(e) {

					var url,
						currentExtremes = this.getExtremes(),
						range = e.max - e.min;
					var chart = $('#container').highcharts();
					chart.showLoading('Loading data from server...');
					
					$.getJSON('http://telldus.hca-erfurt.de/inc/charts/getChartDataJSON.php?id={$id}&type=device&start='+ Math.round(e.min) +
							'&end='+ Math.round(e.max) +'&callback=?', function(data) {
						
						chart.series[0].setData(data);
						chart.hideLoading();
					});
					
				}

				</script> 
				
				";
	}
		
		
	
	function generateSensorChart($id, $name) {
		global $tempColor;
		global $humiColor;
		
		return "
				<script type='text/javascript'>
				$(function() {
					Highcharts.setOptions({
						global: {
							useUTC: false
						}
					});
					// See source code from the JSONP handler at https://github.com/highslide-software/highcharts.com/blob/master/samples/data/from-sql.php
					//var startTime=new Date().getTime()-86400000; //one day
					var startTime=1;
					$.getJSON('http://telldus.hca-erfurt.de/inc/charts/getChartDataJSON.php?id={$id}&type=sensor&start='+startTime+'&callback=?', function(data) {
						
								
						// create the chart
						$('#container').highcharts('StockChart', {
							chart : {
								type: 'spline',
								zoomType: 'x'
							},
							
							navigator : {
								adaptToUpdatedData: false,
								series : {
									data : data[1]
								}
							},

							scrollbar: {
								liveRedraw: false
							},
							
							rangeSelector : {
								buttons: [{
									type: 'hour',
									count: 1,
									text: '1h'
								}, {
									type: 'day',
									count: 1,
									text: '1d'
								}, {
									type: 'week',
									count: 1,
									text: '1w'
								},{
									type: 'month',
									count: 1,
									text: '1m'
								}, {
									type: 'year',
									count: 1,
									text: '1y'
								}, {
									type: 'all',
									text: 'All'
								}],
								inputEnabled: false, // it supports only days
								selected : 5 // all
							},
							
							legend: {
								enabled: false,
							},
							
							series: [{
									name: 'Temperature',
									data : data[0],
									dataGrouping: {
										enabled: false
									},
									tooltip: {
										valueSuffix: '\u00B0C'
									},		
									yAxis: 1,									
									color: '{$tempColor}'
								}, {
									name: 'Humidity',
									data : data[1],
									dataGrouping: {
										enabled: false
									},
									tooltip: {
										valueSuffix: '%'
									},	
									color: '{$humiColor}'
								}
							],
							
							yAxis: [{
									title: {
										text: 'Temperature',
										style: {
											color: '{$tempColor}'
										}
									},
									labels: {
										formatter: function() {
											return this.value +'\u00B0C';
										},
										style: {
											color: '{$tempColor}'
										}
									},
									
								}, {
									title: {
										text: 'Humidity',
										style: {
											color: '{$humiColor}'
										}
									},
									labels: {
										formatter: function() {
											return this.value +'%';
										},
										style: {
											color: '{$humiColor}'
										}
									},
									opposite: true
								}
							],
							
							xAxis : {
								type: 'datetime',
								events : {
									afterSetExtremes : afterSetExtremes
								},
								minRange: 3600 // one hour
							},
						});
					});
				});

				/**
				 * Load new data depending on the selected min and max
				 */
				function afterSetExtremes(e) {
					var chart = $('#container').highcharts();
					
					var url,
						currentExtremes = this.getExtremes(),
						range = e.max - e.min;
					
					chart.showLoading('Loading data from server...');
					
					$.getJSON('http://telldus.hca-erfurt.de/inc/charts/getChartDataJSON.php?id={$id}&type=sensor&start='+ Math.round(e.min) +
							'&end='+ Math.round(e.max) +'&callback=?', function(data) {
						
						chart.series[0].setData(data[0]);
						chart.series[1].setData(data[1]);
						chart.hideLoading();
					});
					
				}

				</script> 
				
				";
	}	
	

?>
