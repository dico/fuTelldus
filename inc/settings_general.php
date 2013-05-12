<?php

	//echo "<h4>".$lang['General settings']."</h4>";

	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success'>{$lang['Data saved']}</div>";
	}
?>



<form class="form-horizontal" action='?page=settings_exec&action=saveGeneralSettings' method='POST'>


	<fieldset>
		<legend><?php echo $lang['Page settings']; ?></legend>

		<div class="control-group">
			<label class="control-label" for="pageTitle"><?php echo $lang['Page title']; ?></label>
			<div class="controls">
				<input type="text" name='pageTitle' id="pageTitle" placeholder="<?php echo $lang['Page title']; ?>" value='<?php echo $config['pagetitle']; ?>'>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="mail_from"><?php echo $lang['Outgoing mailaddress']; ?></label>
			<div class="controls">
				<input type="text" name='mail_from' id="mail_from" placeholder="<?php echo $lang['Outgoing mailaddress']; ?>" value='<?php echo $config['mail_from']; ?>'>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="chart_max_days"><?php echo $lang['Chart max days']; ?></label>
			<div class="controls">
				<input style='width:50px;' type="text" name='chart_max_days' id="chart_max_days" placeholder="<?php echo $lang['Chart max days']; ?>" value='<?php echo $config['chart_max_days']; ?>'>
			</div>
		</div>

		<?php
			echo "<div class='control-group'>";
				echo "<label class='control-label' for='language'>{$lang['Public']} ".strtolower($lang['Language'])."</label>";
				echo "<div class='controls'>";

					echo "<label class='language'>";
						$sourcePath = "lib/languages/";
						$sourcePath = utf8_decode($sourcePath); // Encode for æøå-characters
						$handler = opendir($sourcePath);
						
						echo "<select name='language'>";
							while ($file = readdir($handler)) {
								$file = utf8_encode($file); // Encode for æøå-characters
								
								list($filename, $ext) = explode(".", $file);

								if ($ext == "php") {
									if ($config['public_page_language'] == $filename)
										echo "<option value='$filename' selected='selected'>$filename</option>";

									else
										echo "<option value='$filename'>$filename</option>";
								}
							}
			      	  	echo "</select>";
			        echo "</label>";

				echo "</div>";
			echo "</div>";
		?>

	</fieldset>


	<hr />

	<div class="control-group">
		<div class="controls pull-right">
			<button type="submit" class="btn btn-primary"><?php echo $lang['Save data']; ?></button>
		</div>
	</div>


</form>