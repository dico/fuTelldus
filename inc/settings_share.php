<?php
	
	echo "<h4>".$lang['Shared sensors']."</h4>";



	/* Messages
	--------------------------------------------------------------------------- */
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success'>{$lang['Sensor added to monitoring']}</div>";
		if ($_GET['msg'] == 02) echo "<div class='alert alert-success'>{$lang['Sensor removed from monitoring']}</div>";
		if ($_GET['msg'] == 03) echo "<div class='alert alert-success'>{$lang['Data saved']}</div>";
	}



	/* Form
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Add shared sensor']}</legend>";

		echo "<form action='?page=settings_exec&action=addSensorFromXML' method='POST'>";
			echo "<table width='100%'>";

				echo "<tr>";
					echo "<td>".$lang['Description']."</td>";
					echo "<td>".$lang['XML URL']."</td>";
					echo "<td></td>";
				echo "</tr>";

				echo "<tr>";

					echo "<td>";
						echo "<input style='width:180px;' type='text' name='description' id='description' value='' />";
					echo "</td>";

					echo "<td>";
						echo "<input style='width:360px;' type='text' name='xml_url' id='xml_url' value='' />";
					echo "</td>";

					echo "<td><input class='btn btn-primary' type='submit' name='submit' value='".$lang['Save data']."' /></td>";
				echo "</tr>";

			echo "</table>";
		echo "</form>";

	echo "</fieldset>";



	/* Shared sensors
	--------------------------------------------------------------------------- */
	echo "<fieldset>";
		echo "<legend>{$lang['Sensors']}</legend>";

		$query = "SELECT * FROM ".$db_prefix."sensors_shared WHERE user_id='{$user['user_id']}' ORDER BY description ASC";
	    $result = $mysqli->query($query);
	    $numRows = $result->num_rows;

	    if ($numRows > 0) {


	    	while($row = $result->fetch_array()) {

		    	$xmlData = simplexml_load_file($row['url']);

		    	echo "<div style='border-bottom:1px solid #eaeaea; margin-left:15px; padding:10px;'>";


		    		// Tools
		    		echo "<div style='float:right;'>";

						echo "<div class='btn-group'>";

							if ($row['show_in_main'] == 1) $toggleClass = "btn-success";
							else $toggleClass = "btn-warning";

							if ($row['disable'] == 1) $toggleClass = "btn-danger";

							echo "<a class='btn dropdown-toggle $toggleClass' data-toggle='dropdown' href='#''>";
								echo "{$lang['Action']}";
								echo "<span class='caret'></span>";
							echo "</a>";

							echo "<ul class='dropdown-menu'>";
								if ($row['show_in_main'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=putOnMainSensorFromXML&id={$row['share_id']}'>Remove from main</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=putOnMainSensorFromXML&id={$row['share_id']}'>Put on main</a></li>";
				    			

				    			if ($row['disable'] == 1)
				    				echo "<li><a href='?page=settings_exec&action=disableSensorFromXML&id={$row['share_id']}'>Enable</a></li>";
				    			else
				    				echo "<li><a href='?page=settings_exec&action=disableSensorFromXML&id={$row['share_id']}'>Disable</a></li>";
				    			

				    			echo "<li><a href='?page=settings_exec&action=deleteSensorFromXML&id={$row['share_id']}'>Delete</a></li>";
							echo "</ul>";
						echo "</div>";

		    		echo "</div>";



		    		echo "<div style='font-size:20px;'>".$row['description']."</div>";

		    		echo "<div style='font-size:11px;'>";
		    			echo "<b>{$lang['Sensorname']}:</b> ".$xmlData->sensor->name . "<br />";
		    			echo "<b>{$lang['Location']}:</b> ".$xmlData->sensor->location . "<br />";
		    			echo "<b>{$lang['XML URL']}:</b> <a href='{$row['url']}' target='_blank'>".$row['url']."</a>";
		    		echo "</div>";
		    		
		    		echo "<div style='display:inline-block; width:100px; margin:10px; font-size:20px;'>";
		    			echo "<img style='margin-right:10px;' src='images/thermometer02.png' alt='icon' />";
		    			echo $xmlData->sensor->temp . "&deg;";
		    		echo "</div>";

		    		if ($xmlData->sensor->humidity > 0) {
			    		echo "<div style='display:inline-block; width:100px; margin:10px; font-size:20px;'>";
			    			echo "<img style='margin-right:10px;' src='images/water.png' alt='icon' />";
			    			echo $xmlData->sensor->humidity . "%";
			    		echo "</div>";
			    	}

		    		echo "<div style='font-size:10px'>";
		    			echo ago($xmlData->sensor->lastUpdate);
		    		echo "</div>";

		    	echo "</div>";

		    }

		} else echo "<div class='alert'>{$lang['Nothing to display']}</div>";

	echo "</fieldset>";

?>