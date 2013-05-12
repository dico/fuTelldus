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