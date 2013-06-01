<?php
	
	/* Get parameters
	--------------------------------------------------------------------------- */
	if (isset($_GET['id'])) $getID = clean($_GET['id']);
	if (isset($_GET['action'])) $action = clean($_GET['action']);


	/* Check access
	--------------------------------------------------------------------------- */
	if ($user['admin'] != 1) {
		if ($getID != $user['user_id']) {
			header("Location: ?page=settings&view=user&action=edit&id={$user['user_id']}");
			exit();
		}
	}



	// Check for action or user is
	if (!isset($_GET['id'])) {
		if (!isset($_GET['action'])) {
			header("Location: ?page=settings&view=users");
			exit();
		}
	}

	/* Get userdata
	--------------------------------------------------------------------------- */
	$result = $mysqli->query("SELECT * FROM ".$db_prefix."users WHERE user_id='".$getID."'");
	$selectedUser = $result->fetch_array();

	/* Get user telldus-config
	--------------------------------------------------------------------------- */
	$result = $mysqli->query("SELECT * FROM ".$db_prefix."users_telldus_config WHERE user_id='".$getID."'");
	$selectedUserTelldusConf = $result->fetch_array();
	





	echo "<h4>".$lang['Usersettings']."</h4>";


	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-info'>".$lang['Userdata updated']."</div>";
		elseif ($_GET['msg'] == 02) echo "<div class='alert alert-error'>".$lang['Old password is wrong']."</div>";
		elseif ($_GET['msg'] == 03) echo "<div class='alert alert-error'>".$lang['New password does not match']."</div>";
		elseif ($_GET['msg'] == 04) echo "<div class='alert alert-info'>".$lang['Test message sent']."</div>";
	}




	if ($action == "edit")
		echo "<form class='form-horizontal' action='?page=settings_exec&action=userSave&id=$getID' method='POST'>";
	else
		echo "<form class='form-horizontal' action='?page=settings_exec&action=userAdd' method='POST'>";
?>



	<fieldset>
		<legend><?php echo $lang['Login']; ?></legend>

		<div class="control-group">
			<label class="control-label" for="inputEmail"><?php echo $lang['Email']; ?></label>
			<div class="controls">
				<input type="text" name='inputEmail' id="inputEmail" placeholder="<?php echo $lang['Email']; ?>" value='<?php echo $selectedUser['mail']; ?>'>
			</div>
		</div>



		<div class="controls" style='margin-top:25px; margin-bottom:10px;'>
			<span class="help-block"><?php echo $lang['Leave field to keep current']; ?></span>
		</div>


		<div class="control-group">
			<label class="control-label" for="inputPassword"><?php echo $lang['New'] . " " . strtolower($lang['Password']); ?> </label>
			<div class="controls">
				<input type="password" name='newPassword' id="newPassword" placeholder="<?php echo $lang['New'] . " " . strtolower($lang['Password']); ?>" autocomplete="off">
			</div>
		</div>


		<div class="control-group">
			<label class="control-label" for="inputPassword"><?php echo $lang['Repeat'] . " " . strtolower($lang['Password']); ?></label>
			<div class="controls">
				<input type="password" name='newCPassword' id="newCPassword" placeholder="<?php echo $lang['Repeat'] . " " . strtolower($lang['Password']); ?>" autocomplete="off">
			</div>
		</div>


		<?php
			if ($user['admin'] == 1) {
				echo "<div class='control-group'>";
					echo "<div class='controls'>";

						echo "<label class='checkbox'>";
							if ($selectedUser['admin'] == 1) $adminChecked = "checked='checked'";
				          echo "<input type='checkbox' name='admin' value='1' $adminChecked> " . $lang['Admin'];
				        echo "</label>";

					echo "</div>";
				echo "</div>";

			}
		?>

	</fieldset>

	
	<?php
		echo "<fieldset>";
			echo "<legend>{$lang['Notification']}</legend>";
			echo "<div class='control-group'>";
				echo "<label class='control-label' for='insertPushoverKey'>".$lang['Pushover key']."</label>";
				
				echo "<div class='controls'>";
					echo "<label class='insertPushoverKey'>";
						echo "<input type='text' name='pushover_key' id='pushover_key' value='".$selectedUser['pushover_key']."' style='width:250px;'/>";
						echo "<a class='btn btn-success' style='margin-left:15px;'  href='#test_notification' data-toggle='modal'\">".$lang['Test']."</a>";
			        echo "</label>";
				echo "</div>";
			echo "</div>";
		echo "</fieldset>";	
	?>
	
	<!-- The modal test dialog for notifications -->
	<div class="modal fade" id="test_notification">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3><?php echo $lang['Send Notification'] ?></h3>
		</div>
		<div class="modal-body">
			<p><b><?php echo $lang['Your Pushover key'] ?>:</b> <?php echo $selectedUser['pushover_key'] ?> </p>
			<!--<p><b><?php echo $lang['Select device'] ?>:</b> -->
		</div>
		<div class="modal-footer">
			<a href="?page=settings_exec&action=sendTestNotification&pushover_key=<?php echo $selectedUser['pushover_key'] ?>&subject=Test&message=Test notification&id=<?php echo $getID ?>" class="btn btn-success" id="send_notification"><?php echo $lang['Send'] ?></a>
			<a href="#" class="btn" data-dismiss="modal"><?php echo $lang['Close'] ?></a>
		</div>
	</div>
	
	<!-- sendNotification($selectedUser['pushover_key'], "Test", "Test message")  -->
	<fieldset>
		<legend><?php echo $lang['Language']; ?></legend>
		<?php
			echo "<div class='control-group'>";
				echo "<label class='control-label' for='language'>".$lang['User language']."</label>";
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
									if ($defaultLang == $filename)
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



	<fieldset>
		<legend>Telldus</legend>

		<?php
			echo "<div class='control-group'>";
				echo "<label class='control-label' for='syncLists'>".$lang['Sync lists everytime']."</label>";
				
				echo "<div class='controls'>";

					$syncTelldusOff = "";
					$syncTelldusOn = "";
					if ($selectedUserTelldusConf['sync_from_telldus'] == 1) $syncTelldusOn = "checked='checked'";
					else $syncTelldusOff = "checked='checked'";

					echo "<label class='radio'>";
						echo "<input type='radio' name='syncTelldusLists' id='syncTelldusListsOn' value='1' $syncTelldusOn> {$lang['On']}";
					echo "</label>";

					echo "<label class='radio'>";
						echo "<input type='radio' name='syncTelldusLists' id='syncTelldusListsOff' value='0' $syncTelldusOff> {$lang['Off']}";
					echo "</label>";

				echo "</div>";
			echo "</div>";

		?>


		<div class="control-group">
			<label class="control-label" for="public_key"><?php echo $lang['Public key']; ?></label>
			<div class="controls">
				<input style='width:350px;' type="text" name='public_key' id="public_key" placeholder="<?php echo $lang['Public key']; ?>" value='<?php echo $selectedUserTelldusConf['public_key']; ?>'>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="private_key"><?php echo $lang['Private key']; ?></label>
			<div class="controls">
				<input style='width:350px;' type="text" name='private_key' id="private_key" placeholder="<?php echo $lang['Private key']; ?>" value='<?php echo $selectedUserTelldusConf['private_key']; ?>'>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="token_key"><?php echo $lang['Token']; ?></label>
			<div class="controls">
				<input style='width:350px;' type="text" name='token_key' id="token_key" placeholder="<?php echo $lang['Token']; ?>" value='<?php echo $selectedUserTelldusConf['token']; ?>'>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="token_secret_key"><?php echo $lang['Token secret']; ?></label>
			<div class="controls">
				<input style='width:350px;' type="text" name='token_secret_key' id="token_secret_key" placeholder="<?php echo $lang['Token secret']; ?>" value='<?php echo $selectedUserTelldusConf['token_secret']; ?>'>
			</div>
		</div>

	</fieldset>




	<hr />

	<div class="control-group">
		<div class="controls pull-right">
			<?php
				if ($getID == $user['user_id']) {
					echo "<a class='btn btn-warning' style='margin-right:15px;' href='login/logout.php' onclick=\"return confirm('Are you sure?')\">".$lang['Log out']."</a>";
				}

				if ($action == "edit") {
					echo "<button type='submit' class='btn btn-primary'>".$lang['Save data']."</button>";
				} else {
					echo "<button type='submit' class='btn btn-success'>".$lang['Create user']."</button>";
				}
			?>	
		</div>
	</div>


</form>