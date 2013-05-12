<?php
	
	
	echo "<h4>Telldus connect</h4>";


	if (isset($_SESSION['loggedin'])) {
		echo "<p>You are logged in!</p>";
		echo "Telldus IDs: <b>" . $_SESSION['telldus_id'] . "</b><br>";
		echo "Email: <b>" . $_SESSION['email'] . "</b><br>";
		echo "Fullname: <b>" . $_SESSION['fullname'] . "</b><br>";


		echo "<div style='margin:15px;'>";
			echo "<a href='http://api.telldus.com/keys/index'>Get you're API keys here</a>";
		echo "</div>";

	} else {
		echo "You are not logged in. <a href='telldus_auth.php'>Login</a>";
	}


?>