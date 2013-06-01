<?php
	
	/**
	* 01. Prevent SQL-injections
	*
	* @param  String    $str  The string to clean
	* @return String
	*/
	
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return ($str);
	}


	/**
	* 02. Get single database field
	*
	* @param  String    $field  	Database field
	* @param  String    $tbl_name 	Tablename
	* @param  String    $where  	WHERE statement
	* @return String
	*/

	function getField($field, $tbl_name, $where) {
		global $mysqli;
		
		$data = $mysqli->query("SELECT $field FROM $tbl_name $where")->fetch_object()->$field;
		
		return $data;
	}


	/**
	* 03. Shorten string
	*
	* @param  String    $string  	Input string
	* @param  String    $value 		Shorten value
	* @return String
	*/

	function shortenString($string, $value = 20) {
		if (strlen($string) >= $value) {
		    return substr($string, 0, 10). " ... " . substr($string, -5);
		}
		else {
		    return $string;
		}
	}

	function sendNotification($pushover_key, $subject, $message) {
		global $config;
	
		$ch = curl_init();
		$params = array(
			CURLOPT_URL => "https://api.pushover.net/1/messages.json",
			CURLOPT_POSTFIELDS => array(
				"token" => $config['pushover_api_token'],
				"user" => $pushover_key,
				"title" => $subject,
				"message" => $message,
			));
		curl_setopt_array($ch, $params);
		$response = curl_exec($ch);
		curl_close($ch);
	}

	function sendMail($to, $subject, $message) {
		global $config;

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Additional headers
		$headers .= "To: $to" . "\r\n";
		$headers .= "From: ".$config['pagetitle']." <".$config['mail_from'].">" . "\r\n";

		// Mail it
		mail($to, $subject, $message, $headers);
	}

	function includePlugin($pluginIndexFile) {
		include_once $pluginIndexFile;

		$nameSpace = getNameSpaceFromFile($pluginIndexFile);
		return $nameSpace;
	}
	
	function getNameSpaceFromFile($file) {
		$h = fopen($file, 'r', true) or die('wtf');
		if (!isset($h)) {
			return "";
		}
		$n = '';

		while (!feof($h) && !$n)
		{
			$l = fgets($h);
			if (stripos($l, 'namespace') !== false) $n = preg_replace('/namespace\ +(\w+);/i', '$1', $l);
		}

		fclose($h);
		return $n ? trim(str_replace(';', '', str_replace('namespace', '', trim($n)))) : '';	
	}

	/*
	function sendMail($to, $subject, $message) {
		global $config;

		// Decode for æøå
		$subject = utf8_decode($subject);
		$message = utf8_decode($message);
	
		
		//$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers = "Content-type: text/html; charset=iso-8859-1" . "\r\n";
		$headers .= "From: {$config['pagetitle']} <{$config['mail_from']}>" . "\r\n";
		$headers .= "Return-Path: {$config['pagetitle']} <{$config['mail_from']}>" . "\r\n";
		$headers .= "Reply-To: {$config['mail_from']}" . "\r\n";
		$headers .= "X-Mailer: PHP/" . phpversion();

		mail($to, $subject, $message, $headers, '-f'.$senderAddress.'');
	}
	*/

?>