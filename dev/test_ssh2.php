<?php

/**
* Needs ssh2 extension for PHP
* apt-get install libssh2-1-dev libssh2-php
*/
$host = "192.168.1.100";
$sshport = 22;
$adminport = "5000";
$maxOfflineChecks = 50;

$connection = ssh2_connect($host, $sshport);
ssh2_auth_password($connection, 'root', 'Lb77rghf');

// send nas to safe mode
$stdout = ssh2_exec($connection, '/usr/syno/bin/synologset1 sys warn 0x11300011');
$stdout = ssh2_exec($connection, '/usr/syno/bin/syno_poweroff_task -s');

$stdout = ssh2_exec($connection, 'exit');


// wait until admin-console isn't reachable anymore
while (true) {
	$handle = curl_init($host);
	curl_setopt($handle, CURLOPT_PORT, $adminport); 
	curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

	/* Get the HTML or whatever is linked in $url. */
	$response = curl_exec($handle);

	/* Check for 404 (file not found). */
	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	if($httpCode == 0) {
		// NAS is in safe mode, shutdown now possible
		curl_close($handle);
		// wait additional 10 sec to be sure
		sleep(10);
		return 0;
	}

	curl_close($handle);
	
	// if maxOfflineChecks is 0, exit with error
	if ($maxOfflineChecks == 0) {
		return 1;
	}
	
	// wait 2 seconds and try again until maxOffLineChecks is 0
	sleep(2);
	$maxOfflineChecks = $maxOfflineChecks - 1;
}



?>
