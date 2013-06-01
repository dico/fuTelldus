<?php

/**
 * Scan network to retrieve hosts and services information.
 
 Wakeup iPhone
  nmap -P0 -sT -p62078 192.168.1.109
  
  Scan network
    nmap -sP 192.168.1.1/24 | grep -B2 -i 58:1F:AA:B0:31:2A
 */
 
$target = "192.168.1.1";
$mac = "58:1F:AA:B0:31:2A";

$output = shell_exec("nmap -sP ".$target."/24 | grep -B2 -i ".$mac." 2>&1");

if (strlen(stristr($output,$mac))>0) {
	preg_match("/\(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\)/", $output, $matches); 
	$deviceIP = $matches[0];
	
	// remove first and last character because we added them to our regExp
	$deviceIP = substr($deviceIP, 1 , strlen($deviceIP)-2); 
echo $deviceIP;
	// contact the iphone on port 62078 to keep wifi on
	//echo shell_exec("nmap -P0 -sT -p62078 ".$deviceIP." 2>&1");

} else {
	echo "no device found\n";
}



?>
