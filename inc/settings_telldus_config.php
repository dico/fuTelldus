<h4>Telldus settings</h4>


<?php
	if (isset($_GET['msg'])) {
		if ($_GET['msg'] == 01) echo "<div class='alert alert-success'>Configuration saved</div>";
	}
?>


<p>Token and token secret is avalible under <a href='?page=settings&view=telldus_api'>Telldus API</a> after you have connected. 
		<a href='http://api.telldus.com/keys/index'>Public and private key needs to be requested in telldus webpages</a>.</p>


<form class="form-horizontal" action='?page=settings_exec&action=saveTelldusConfig' method='POST'>


	<?php
		if (!isset($_SESSION['fuTelldus_user_loggedin'])) {
			if (!empty($config['telldus_public_key'])) $publicPlaceholder = "*** Public key saved ***";
			else $publicPlaceholder = "Enter public key here";

			if (!empty($config['telldus_private_key'])) $privatePlaceholder = "*** Private key saved ***";
			else $privatePlaceholder = "Enter private key here";

			if (!empty($config['telldus_token'])) $tokenPlaceholder = "*** Token saved ***";
			else $tokenPlaceholder = "Enter token here";

			if (!empty($config['telldus_token_secret'])) $tokenSecretPlaceholder = "*** Token-secret key saved ***";
			else $tokenSecretPlaceholder = "Enter token-secret here";
		}

		else {
			if (!empty($config['telldus_public_key'])) $publicPlaceholder = $config['telldus_public_key'];
			else $publicPlaceholder = "Enter public key here";

			if (!empty($config['telldus_private_key'])) $privatePlaceholder = $config['telldus_private_key'];
			else $privatePlaceholder = "Enter private key here";

			if (!empty($config['telldus_token'])) $tokenPlaceholder = $config['telldus_token'];
			else $tokenPlaceholder = "Enter token here";

			if (!empty($config['telldus_token_secret'])) $tokenSecretPlaceholder = $config['telldus_token_secret'];
			else $tokenSecretPlaceholder = "Enter token-secret here";
		}
	?>



	<fieldset>
		<legend>API keys</legend>

		<div class="control-group">
			<label class="control-label" for="public_key">Public key</label>
			<div class="controls">
				<input style='width:350px;' type="text" name='public_key' id="public_key" placeholder="<?php echo $publicPlaceholder; ?>" value=''>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="private_key">Private key</label>
			<div class="controls">
				<input style='width:350px;' type="text" name='private_key' id="private_key" placeholder="<?php echo $privatePlaceholder; ?>" value=''>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="token_key">Token</label>
			<div class="controls">
				<input style='width:350px;' type="text" name='token_key' id="token_key" placeholder="<?php echo $tokenPlaceholder; ?>" value=''>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="token_secret_key">Token secret</label>
			<div class="controls">
				<input style='width:350px;' type="text" name='token_secret_key' id="token_secret_key" placeholder="<?php echo $tokenSecretPlaceholder; ?>" value=''>
			</div>
		</div>

	</fieldset>



	<hr />

	<div class="control-group">
		<div class="controls pull-right">
			<button type="submit" class="btn btn-primary">Save data</button>
		</div>
	</div>


</form>