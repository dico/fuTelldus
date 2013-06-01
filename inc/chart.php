<script src="../../lib/packages/Highstock-1.3.1/js/highstock.js"></script>
<script src="../../lib/packages/Highstock-1.3.1/js/modules/exporting.js"></script> 



<?php
    /* Headline
    --------------------------------------------------------------------------- */
    echo "<h3>{$lang['Charts']}</h3>";

    /* Scenes - todo
    --------------------------------------------------------------------------- */
/*	echo "<fieldset>";
		echo "<legend>{$lang['Scenes']}</legend>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th>{$lang['Name']}</th>";
					echo "<th>Included data</th>";
					echo "<th width='15%' >Actions</th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
				echo "<tr>";
					echo "<td>Schlafzimmer mit Fenster</td>";
					echo "<td>Schlafzimmer; Fenster</td>";
					echo "<td>";
						echo "<button class='btn btn-warning btn-mini' title='edit the scene'><i class='icon-white icon-pencil'></i></button>&nbsp;";
						echo "<button class='btn btn-danger btn-mini' title='delete the scene'><i class='icon-white icon-trash'></i></button>&nbsp;";
						echo "<button class='btn btn-info btn-mini' style='float:right' title='show the chart'><i class='icon-white icon-signal'></i></button>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>Jens@Home mit Pathfinder online</td>";
					echo "<td>pathfinder online; Jens@Home</td>";
					echo "<td>";
						echo "<button class='btn btn-warning btn-mini' title='edit the scene'><i class='icon-white icon-pencil'></i></button>&nbsp;";
						echo "<button class='btn btn-danger btn-mini' title='delete the scene'><i class='icon-white icon-trash'></i></button>&nbsp;";
						echo "<button class='btn btn-info btn-mini' style='float:right' title='show the chart'><i class='icon-white icon-signal'></i></button>";
					echo "</td>";
				echo "</tr>";
			echo "</tbody>";
		
		echo "</table>";
		
	echo "<div style='text-align:right;'>";
	echo "<a class='btn btn-primary' href='#'>Create scene</a>";		
	echo "</div>";
	echo "</fieldset>"; */
	
    /* Sensors
    --------------------------------------------------------------------------- */	
	echo "<fieldset>";
		echo "<legend>{$lang['Sensors']}</legend>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th width='50%'>{$lang['Name']}</th>";
					echo "<th width='35%'></th>";
					echo "<th width='15%' style='float:right'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
			
			
			$query = "select s.name, s.sensor_id as id, 'sensor' as type, count(sl.time_updated)-1 as rows from ".$db_prefix."sensors s, ".$db_prefix."sensors_log sl 
				where s.monitoring=1 and s.sensor_id=sl.sensor_id group by name, id, type
				union
				select vs.description as name, vs.id as id, 'virtual' as type, count(vsl.time_updated)-1 as rows from ".$db_prefix."virtual_sensors vs, ".$db_prefix."virtual_sensors_log vsl 
					where vs.monitoring=1 and vs.id=vsl.sensor_id group by name, id, type";
			$result = $mysqli->query($query);
			
			while($row = $result->fetch_array()) {
				echo "<tr>";
					echo "<td>".$row['name']."</td>";
					$activateChartButton = "";
					if ($row['rows']>0) {
						echo "<td><small><i>about ".$row['rows']." logs</i></small></td>";
					} else {
						echo "<td><small><i>no data</i></small></td>";
						$activateChartButton = "disabled";
					}
					echo "<td>";
					
						$activateChartButton = "";
						if ($row['type']=='virtual' and !isPluginProvidingCharts($row['id'])) {
							$activateChartButton = "disabled";
						}					
						echo "<button class='btn btn-info btn-mini showChart $activateChartButton' $activateChartButton style='float:right' title='show the chart' href='#showChart' data-toggle='modal'\" data-name='".$row['name']."' data-id='".$row['id']."' data-type='".$row['type']."'>";
						echo "<i class='icon-white icon-signal'></i></button>";
					echo "</td>";
				echo "</tr>";
			}
			
			echo "</tbody>";
		echo "</table>";
	echo "</fieldset>";
	
    /* Devices
    --------------------------------------------------------------------------- */		
	echo "<fieldset>";
		echo "<legend>{$lang['Devices']}</legend>";
		echo "<table class='table table-striped table-hover'>";
			echo "<thead class='hidden-phone'>";
				echo "<tr>";
					echo "<th width='50%'>{$lang['Name']}</th>";
					echo "<th width='35%'></th>";
					echo "<th width='15%' style='float:right'></th>";
				echo "</tr>";
			echo "</thead>";
			
			echo "<tbody>";
			
			$query = "SELECT d.name, d.type as type, d.device_id as id, count(dl.time_updated)-1 as rows FROM ".$db_prefix."devices d, ".$db_prefix."devices_log dl where d.device_id = dl.device_id group by name, id, type";
			$result = $mysqli->query($query);
			
			while($row = $result->fetch_array()) {
				echo "<tr>";
					echo "<td>".$row['name']."</td>";
					$activateChartButton = "";
					if ($row['rows']>0) {
						echo "<td><small><i>about ".$row['rows']." logs</i></small></td>";
					} else {
						echo "<td><small><i>no data</i></small></td>";
						$activateChartButton = "disabled";
					}
					
					echo "<td>";
						echo "<button class='btn btn-info btn-mini showChart $activateChartButton' $activateChartButton style='float:right' title='show the chart' href='#showChart' data-toggle='modal'\" data-name='".$row['name']."' data-id='".$row['id']."' data-type='".$row['type']."'>";
						echo "<i class='icon-white icon-signal'></i></button>";
					echo "</td>";
				echo "</tr>";
			}
			echo "</tbody>";
		
		echo "</table>";		
	echo "</fieldset>";
?>

	<!-- The modal test dialog for notifications -->
	<div class="modal fade" id="showChart">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<div class="header">
			</div>
		</div>
		<div class="modal-body" style="text-align:center">
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal"><?php echo $lang['Close'] ?></a>
		</div>
	</div>
	
<script type="text/javascript">	
	$(document).on("click", ".showChart", function () {
		$('#showChart').css({ //		.modal({backdrop: true,keyboard: true}).css({
		   'width': function () { 
			   return ($(document).width() * .7) + 'px';  
		   },
		   'margin-left': function () { 
			   return -($(this).width() / 2); 
		   }
		});
		
		var sensorID = $(this).data('id');
		var sensortype = $(this).data('type');
		var sensorName = $(this).data('name');
		
		$(".header").html("<h3>"+sensorName+"</h3>");
		$(".modal-body").css({'min-height': '500px'});
		$(".modal-body").html("<img style='height:100px; margin-top:200px;' src='images/ajax-loader3.gif' alt='ajax-loader' />");

		/*$.get("inc/charts/getChart.php?id="+sensorID+"&type="+sensortype+"&name="+sensorName+"", function(data) {
		  $(".modal-body").html(data );
		}).error(function(xhr, status, err) {
		    $(".modal-body").html("<p>Error: Status = " + status + ", err = " + err + "</p>");
		  });*/
		
		$.ajax({
		  url: "inc/charts/getChart.php?id="+sensorID+"&type="+sensortype+"&name="+sensorName+"",
		  dataType: "text",
		  method: "get",
		  cache: false,
		  success: function(data) {
			$(".modal-body").html(data );
		  },
		  error: function(xhr, status, err) {
		    $(".modal-body").html("<p>Error: Status = " + status + ", err = " + err + "</p>");
		  }
		});
		
		
	});
</script>