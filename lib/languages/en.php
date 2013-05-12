<?php
	
	/* Language: English
	 * Script: Fosen Utvikling AS - fuTelldus
	 * Author: Robert Andresen
	 * Last edited: 02.01.2013
	*/
	
$lang = array(
	
	// Navigation
	"Home" => "Home",
	"Sensors" => "Sensors",
	"Chart" => "Chart",
	"Report" => "Report",
	"Lights" => "Lights",
	"Settings" => "Settings",
	"Log out" => "Log out",

	"Page settings" => "Page settings",
	"Users" => "Users",
	"Shared sensors" => "Delte sensorer",
	"Test cron-files" => "Test cron-files",
	"View public page" => "View public page",
	"View public sensors" => "View public sensors",



	// User
	"Usersettings" => "Usersettings",
	"Userprofile" => "Userprofile",
	"My profile" => "My profile",
	"Not logged in" => "Not logged in",
	

	// Messages
	"Userdata updated" => "Userdata updated",
	"Old password is wrong" => "Old password is wrong",
	"New password does not match" => "New password does not match",
	"User added" => "User added",
	"User deleted" => "User deleted",
	"Sensor added to monitoring" => "Sensor added to monitoring",
	"Sensor removed from monitoring" => "Sensor removed from monitoring",
	"Wrong timeformat" => "Something was wrong with the date/time selected. Make sure TO-time is after FROM-time :-)",
	"Nothing to display" => "Nothing to display",
	"Data saved" => "Data saved",
	"Deleted" => "Deleted",

	
	// Form
	"Login" => "Login",
	"Email" => "Email",
	"Password" => "Password",
	"Leave field to keep current" => "Leave password field empty to keep currect",
	"User language" => "User language",
	"Save data" => "Save data",
	"Create user" => "Create user",
	"Create new" => "Create new",
	"Page title" => "Page title",
	"General settings" => "General settings",
	"Delete" => "Delete",
	"Are you sure you want to delete" => "Are you sure you want to delete?",
	"Edit" => "Edit",
	"Date to" => "Date to",
	"Date from" => "Date from",
	"Show data" => "Show data",
	"Jump" => "Jump",
	"Jump description" => "Jump over selected number of time logged. The temperature is logged every 15 minutes, so a jump of 4 will show one result in hour. 4*24=96 for one a day.",
	"XML URL" => "XML URL",
	"Description" => "Description",
	"Outgoing mailaddress" => "Outgoing mailaddress",
	"Select chart" => "Select chart",
	"Default chart" => "Default chart",
	"Chart max days" => "View chart for max days back in time",


	// Telldus
	"Telldus keys" => "Telldus keys",
	"Public key" => "Public key",
	"Private key" => "Private key",
	"Token" => "Token",
	"Token secret" => "Token secret",
	"Telldus connection test" => "Telldus connection test",
	"Sync lists everytime" => "Sync lists everytime",
	"List synced" => "List synced",


	// Temperature & chart
	"Latest readings" => "Latest readings",
	"Temperature" => "Temperature",
	"Humidity" => "Humidity",
	"Combine charts" => "Combine charts",
	"Split charts" => "Split charts",
	"View chart" => "View chart",


	// Sensors
	"Sensor" => "Sensor",
	"Sensorname" => "Sensorname",
	"Sensordata" => "Sensordata",
	"Sensor ID" => "Sensor ID",
	"Sensors description" => "<p>Add your sensors to the cronjob for logging the sensordata into database.</p><p>Sensorlist is retrieved with keys added under <a href='?page=settings&view=user'>your userprofile</a>.</p>",
	"Non public" => "Non public",
	"Public" => "Public",


	// Shared sensors
	"Add shared sensor" => "Add shared sensor",


	// Schedule
	"Schedule" => "Schedule",
	"Notifications" => "Notifications",
	"Repeat every" => "Repeat every",
	"Higher than" => "Higher than",
	"Lower than" => "Lower than",
	"Send to" => "Send to",
	"Send warning" => "Send warning",
	"Rule" => "Rule",
	"Last sent" => "Last sent",
	"Device action" => "Device action",
	"No device action" => "No device action",

	// Mail notifications
	"Notification mail low temperature" => "Warning: Temperature is low!<br /><br />Sensor: %%sensor%%<br />Temperature is %%value%% &deg;",
	"Notification mail high temperature" => "Warning: Temperature is high!<br /><br />Sensor: %%sensor%%<br />Temperature is %%value%% &deg;",
	"Notification mail low humidity" => "Warning: Humidity is low!<br /><br />Sensor: %%sensor%%<br />Humidity is %%value%% &deg;",
	"Notification mail high humidity" => "Warning: Humidity is high!<br /><br />Sensor: %%sensor%%<br />Humidity is %%value%% &deg;",
	



	// Lights
	"On" => "On",
	"Off" => "Off",
	"Groups" => "Groups",
	"Devices" => "Devices",
	


	// Div
	"Language" => "Language",
	"New" => "New",
	"Repeat" => "Repeat",
	"Admin" => "Admin",
	"Total" => "Total",
	"Max" => "Max",
	"Min" => "Min",
	"Avrage" => "Avrage",
	"Stop" => "Stop",
	"Data" => "Data",
	"ID" => "ID",
	"Name" => "Name",
	"Ignored" => "Ignored",
	"Client" => "Client",
	"Client name" => "Client name",
	"Online" => "Online",
	"Editable" => "Editable",
	"Last update" => "Last update",
	"Monitor" => "Monitor",
	"Protocol" => "Protocol",
	"Timezone offset" => "Timezone offset",
	"Time" => "Time",
	"Active" => "Active",
	"Disabled" => "Disabled",
	"Location" => "Location",
	"Celsius" => "Celsius",
	"Degrees" => "Degrees",
	"Type" => "Type",
	"Value" => "Value",
	"Cancel" => "Cancel",
	"Warning" => "Warning",
	"High" => "High",
	"Low" => "Low",
	"Primary" => "Primary",
	"Secondary" => "Secondary",
	"Now" => "Now",
	"Action" => "Action",

		// send warning IF temperature IS more/less THAN   / FOR sensor ...
		"If" => "If",
		"Is" => "Is",
		"Than" => "Than",
		"For" => "For",


	// Time (ago)
	"since" => "since",

	"secound" => "secound",
	"minute" => "minute",
	"hour" => "hour",
	"day" => "day",
	"week" => "week",
	"month" => "month",
	"year" => "year",

	"secounds" => "secounds",
	"minutes" => "minutes",
	"hours" => "hours",
	"days" => "days",
	"weeks" => "weeks",
	"months" => "months",
	"years" => "years",

);

?>