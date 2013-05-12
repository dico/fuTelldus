
PROJECT
-------------------------------------------------------------------------------------------

Projectname: 	fuTelldus
Released:		04.04.2013
Developer:		Robert Andresen
Contact:		robert.andresen@fosen-utvikling.no / mail@robertan.com

License:		This work is licensed under a Creative Commons Attribution-NonCommercial 3.0 Unported License.
				http://creativecommons.org/licenses/by-nc/3.0/


Tested on:		Windows 2008 R2 (Wamp server) and unix-server hosted from third-party (ProISP.no).



DEFAULT APPLICATION USER:
-------------------------------------------------------------------------------------------
	Username (mail):	admin
	Password:			admin




UPGRADE:
-------------------------------------------------------------------------------------------
There is no update-function avalible yet, so best practice for upgrade is to create new/replace all
and start from scratch.

	!!! REMEMBER TO TAKE BACKUP !!!

	- Backup your DATABASE and FILES
	- Start install from scratch
	- Import temperature data you created backup from :-)





INSTALL:
-------------------------------------------------------------------------------------------

	1. Open    /lib/config.inc.php    and add you're database credentials
	2. Import the databases from    fuTelldus.sql   located inside root
	3. Upload all files in a folder on your server

	4. Make sure you have installed the oAuth package on your server
		Take a look at   "Starting with PHP and Telldus LIVE on server with cPanel (oAuth and API).pdf"   for some help.
		I had to copy the HTTP folder from oAuth inside the web-root after install

	5. Open the URL where you uploaded your files and login with admin/admin

	6. To get data automatically, you have to setup an cronjob. Look at the next part.
		You can run the cronjob manually to test it from http://youredomain.no/fuTelldus/cron_temp_log.php

		You could also run the cron from under settings in the page.

		There are two cron-files. One to pull temp-values and one for warning-checks (notifications).




SETTING UP CRONJOB:
-------------------------------------------------------------------------------------------

For ProISP customers, look here for guide: http://www.proisp.no/opprettelse-cron-job-cpanel/



Cron files is located in web-root:
	cron_temp_log.php
	cron_schedule.php



EXAMPLE for datapull every 15 minutes (recomended):
	*/15 * * * * php -q /path/to/your/root/fuTelldus/cron_temp_log.php

EXAMPLE for schedule (temperature warning) every 5 minutes (recomended)
	*/5 * * * * php -q /path/to/your/root/fuTelldus/cron_schedule.php






LANGUAGE:
-------------------------------------------------------------------------------------------
	
Language files are placed under lib/languages. Copy a file and replace the array-values to your language.
Please share any new language with me, so i could implement them as standard in the package :-)


	Timeago uses a JS package. To get langauge packages for this string, please see:
		- https://github.com/rmm5t/jquery-timeago/tree/master/locales

		For new timeago-language, you need to update index.php, line 28+


