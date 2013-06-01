<?php
	
	echo "<h4>".$lang['Virtual sensors']."</h4>";

	/* Get parameters
	--------------------------------------------------------------------------- */
	$action = "";
	$getID = "";
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);

	/* Messages
	--------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success'>{$lang['Virtual sensor added']}</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-success'>{$lang['Virtual sensor deleted']}</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-success'>{$lang['Data saved']}</div>";
		if ($_GET['msg'] == 04) echo "<div class='alert alert-success'>{$lang['Virtual sensor updated']}</div>";
	}

	$description = "";
	$sensor_type = "-1";
	$sensor_type_description = "";
	if ($action == "edit") {
		// load data
		$query = "SELECT * FROM ".$db_prefix."virtual_sensors vs, ".$db_prefix."virtual_sensors_types vst where vs.sensor_type = vst.type_int and vs.id='$getID' LIMIT 1";
	    $result = $mysqli->query($query);
	    $row = $result->fetch_array();
		
		$description = $row['description'];
		$sensor_type = $row['sensor_type'];
		$sensor_type_description = $row['type_description'];
		
		// load config data
	}


	/* Form
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Add virtual sensor']}</legend>";

		
			if ($action == "edit") {
				echo "<div class='alert'>";
				echo "<form action='?page=settings_exec&action=updateVirtualSensor&id=$getID' method='POST'>";
			} else {
				echo "<div class='well'>";
				echo "<form action='?page=settings_exec&action=addVirtualSensor' method='POST'>";	
			}		
		
			// add hidden field with actual virtual sensor id
			echo "<input type='hidden' name='virtual_sensor_id' id ='virtual_sensor_id' value='$getID' />";
		
			echo "<table width='100%' id='configValues'>";

				echo "<tr>";
					echo "<td width='40%'>".$lang['Description']."</td>";
					echo "<td>";
						echo "<input style='width:180px;' type='text' name='virtualsensor_description' id='virtualsensor_description' value='$description' />";
					echo "</td>";					
					echo "<td></td>";
				echo "</tr>";

				echo "<tr>";
					echo "<td>".$lang['Type']."</td>";
					echo "<td>";
					$disabledSelect = "";
					if ($action == "edit") {
						$disabledSelect = "disabled";
					}
					echo "	<select $disabledSelect name='virtualsensor_type' id ='virtualsensor_type' size='1' selectedIndex='-1'>";
					echo "	  <option value='$sensor_type'>$sensor_type_description</option>";
					// select all available vSensor-Types
					$query = "SELECT * FROM ".$db_prefix."virtual_sensors_types where hidden='0' ORDER BY type_int ASC";
					$result = $mysqli->query($query);		
					while($row = $result->fetch_array()) {
						echo "	  <option value='".$row['type_int']."'>".$row['type_description']."</option>";
					}
					echo "	</select>";
					echo "</td>";
					
					echo "<td></td>";	
				echo "</tr>";
				
			echo "</table>";
			echo "<br/><div style='text-align:right;'>";
			if ($action == "edit") {
				echo "<a class='btn' href='?page=settings&view=virtualsensors'>{$lang['Cancel']}</a> &nbsp; ";
				echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Update sensor']."'/>";		
			} else {
				echo "<input class='btn btn-primary' type='submit' name='submit' value='".$lang['Add sensor']."'/>";		
			}
			echo "</div>";
		echo "</form></div>";

	echo "</fieldset>";

	/* Virtual sensors
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Sensors']}</legend>";
		
		//$query = "SELECT * FROM ".$db_prefix."virtual_sensors WHERE user_id='{$user['user_id']}' ORDER BY description ASC";
		$query = "SELECT * FROM ".$db_prefix."virtual_sensors s, ".$db_prefix."virtual_sensors_types st WHERE user_id='{$user['user_id']}' and st.type_int = s.sensor_type ORDER BY description ASC";
	    $result = $mysqli->query($query);
	    $numRows = $result->num_rows;

	    if ($numRows > 0) {

	    	while($row = $result->fetch_array()) {
		    	echo "<div style='border-bottom:1px solid #eaeaea; margin-left:15px; padding:10px;'>";
				
		    		// Tools
		    		echo "<div style='float:right;'>";

						echo "<div class='btn-group'>";

							$toggleClass = "";
							if ($row['show_in_main'] == 1){
								$toggleClass = "btn-success";
							} else {
								$toggleClass = "btn-warning";
							}

							if ($row['online'] == 0){
								$toggleClass = "btn-danger";
							}

							echo "<a class='btn dropdown-toggle $toggleClass' data-toggle='dropdown' href='#''>";
								echo "{$lang['Action']}";
								echo "<span class='caret'></span>";
							echo "</a>";

							echo "<ul class='dropdown-menu'>";
								if ($row['show_in_main'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=putOnMainVirtualSensor&id={$row['id']}'>Remove from main</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=putOnMainVirtualSensor&id={$row['id']}'>Put on main</a></li>";
				    			

				    			if ($row['monitoring'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=setMonitoring&id={$row['id']}'>Disable monitoring</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=setMonitoring&id={$row['id']}'>Enable monitoring</a></li>";
									
								if ($row['online'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=setOnline&id={$row['id']}'>Set offline</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=setOnline&id={$row['id']}'>Set online</a></li>";	
				    			
								echo "<li><a href='?page=settings&view=virtualsensors&action=edit&id={$row['id']}'>Edit</a></li>";
								echo "<li class='divider'></li>";
				    			echo "<li><a href='?page=settings_exec&action=deleteVirtualSensor&id={$row['id']}'>Delete</a></li>";
							echo "</ul>";
						echo "</div>";

		    		echo "</div>";

		    		echo "<div style='font-size:20px;'>".$row['description']."</div>";

		    		echo "<div style='font-size:11px;'>";
		    			echo "<b>{$lang['Type']}:</b> ".$row['type_description']. "<br />";
						//echo "<b>{$lang['Value']}:</b> ".$row["config_value"]. "<br />";
		    			echo "<b>{$lang['Online']}:</b> ".$lang["boolean_".$row['online']]. "<br />";
						echo "<b>{$lang['Monitor']}:</b> ".$lang["boolean_".$row['monitoring']]. "<br />";
		    		echo "</div>";

		    		echo "<div style='font-size:10px'>";
		    			echo ago(getLastVirtualSensorCheck($row['id']));
		    		echo "</div>";

		    	echo "</div>";

		    }

		}

	echo "</fieldset>";

?>

<script type="text/javascript">
	$('#virtualsensor_type').change(function () {
	
		var type_int = $(this).val();
		
		if (type_int < 0) {
			$('#configValues').find('tr:gt(1)').remove();		
		}
		
		var type_id = $('#virtual_sensor_id').val();
		
		$.getJSON("inc/virtualHosts/getVirtualSensorTypeConfig.php?type_int="+type_int+"&sensor_id="+type_id, function(data) {
			// remove all from 1 on
			$('#configValues').find('tr:gt(1)').remove();
			
			// add all new
			jQuery.each(data , function(index, value){
				var value_description = value.description;
				var value_key = value.value_key;
				var value_type = value.value_type;
				var config_value = value.config_value;
				var id = value.id;
				if (value_type.toUpperCase() == "boolean".toUpperCase()) {
					var selectedTrue, selectedFalse = 0;
					if (config_value.toUpperCase() == "true".toUpperCase()) selectedTrue="selected='selected'";
					if (config_value.toUpperCase() == "false".toUpperCase()) selectedFalse="selected='selected'";
					$('#configValues tr:last').after("<tr><td>"+value_description+"</td><td><select name='virtualsensor_value_"+id+"'><option value='true' "+selectedTrue+">true</option><option value='false' "+selectedFalse+">false</option></select</td><td></td></tr>");
				} else {
					$('#configValues tr:last').after("<tr><td>"+value_description+"</td><td><input style='width:180px;' type='"+value_type+"' name='virtualsensor_value_"+id+"' id='virtualsensor_value_"+id+"' value='"+config_value+"' /></td><td></td></tr>");
				}
			});
		});
	//});
	}).trigger('change');
</script>