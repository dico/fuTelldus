<?php

	$default	= "mainpage";
	$directory	= "inc";
	$extension	= "php";

	if(isset($_GET['page'])) {
		$page = $_GET['page'];

		if (preg_match('/(http:\/\/|^\/|\.+?\/)/', $page)) echo "Error"; 

		elseif (!empty($page))
		{
			if (file_exists("$directory/$page.$extension"))
				include("$directory/$page.$extension");
			else
				echo "<h2>Error 404</h2>\n";
		}
	}

	else
		include("$directory/$default.$extension");
?>