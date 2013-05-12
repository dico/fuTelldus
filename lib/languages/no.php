<?php
	
	/* Language: Norwegian
	 * Script: Fosen Utvikling AS - fuTelldus
	 * Author: Robert Andresen
	 * Last edited: 02.01.2013
	*/
	
$lang = array(
	
	// Navigation
	"Home" => "Hjem",
	"Sensors" => "Sensorer",
	"Chart" => "Graf",
	"Report" => "Rapporter",
	"Lights" => "Lys",
	"Settings" => "Innstillinger",
	"Log out" => "Logg ut",

	"Page settings" => "Side innstillinger",
	"Users" => "Brukere",
	"Shared sensors" => "Delte sensorer",
	"Test cron-files" => "Test cron-filer",
	"View public page" => "Vis offentlig side",
	"View public sensors" => "View offentlige sensorer",



	// User
	"Usersettings" => "Brukerinnstillinger",
	"Userprofile" => "Brukerprofil",
	"My profile" => "Min profil",
	"Not logged in" => "Ikke innlogget",
	

	// Messages
	"Userdata updated" => "Brukerdata oppdatert",
	"Old password is wrong" => "Gammelt passord stemte ikke",
	"New password does not match" => "Passordene var ikke like",
	"User added" => "Bruker opprettet",
	"User deleted" => "Bruker slettet",
	"Sensor added to monitoring" => "Sensor lagt til monitorering",
	"Sensor removed from monitoring" => "Sensor fjernet fra monitorering",
	"Wrong timeformat" => "Noe er galt med tidsformatet. Sjekk at tid TIL er etter tid FRA :-)",
	"Nothing to display" => "Nothing to display",
	"Data saved" => "Data saved",
	"Deleted" => "Slettet",
	
	
	// Form
	"Login" => "Innlogging",
	"Email" => "Epost",
	"Password" => "Passord",
	"Leave field to keep current" => "La passordfelt være blankt for å beholde gjeldende",
	"User language" => "Brukerspråk",
	"Save data" => "Lagre data",
	"Create user" => "Opprett bruker",
	"Create new" => "Lag ny",
	"Page title" => "Sidetittel",
	"General settings" => "Generelle innstillinger",
	"Delete" => "Slett",
	"Are you sure you want to delete" => "Er du sikker på at du ønsker å slette?",
	"Edit" => "Endre",
	"Date to" => "Dato til",
	"Date from" => "Dato fra",
	"Show data" => "Vis data",
	"Jump" => "Hopp",
	"Jump description" => "Hopp over valgt antall tidspunkt i loggen. Temperaturen logges hvert 15 minutt, så et hopp på 4 vil resultere i visning av en temperatur pr. time. 4*24=96 for en pr. dag.",
	"XML URL" => "XML URL",
	"Description" => "Description",
	"Outgoing mailaddress" => "Utgående e-postadresse",
	"Select chart" => "Velg graf",
	"Default chart" => "Bruk standard graf",
	"Chart max days" => "Vis graf for maks antall dager tilbake i tid",


	// Telldus
	"Telldus keys" => "Telldus nøkler",
	"Public key" => "Public key",
	"Private key" => "Private key",
	"Token" => "Token",
	"Token secret" => "Token secret",
	"Telldus connection test" => "Telldus test-tilkobling",
	"Sync lists everytime" => "Synkroniser lister hver gang",
	"List synced" => "Liste synkronisert",


	// Temperature & chart
	"Latest readings" => "Siste målinger",
	"Temperature" => "Temperatur",
	"Humidity" => "Luftfuktighet",
	"Combine charts" => "Kombiner",
	"Split charts" => "Split grafer",
	"View chart" => "Vis graf",


	// Sensors
	"Sensor" => "Sensor",
	"Sensorname" => "Sensorname",
	"Sensordata" => "Sensordata",
	"Sensor ID" => "Sensor ID",
	"Sensors description" => "<p>Legg sensorer til cronjob for å logge historikk i databasen.</p><p>Sensorlisten hentes med nøkler lagt til under <a href='?page=settings&view=user'>din brukerprofil</a>.</p>",
	"Non public" => "Ikke offentlig",
	"Public" => "Offentlig",


	// Shared sensors
	"Add shared sensor" => "Add shared sensor",


	// Schedule
	"Schedule" => "Planlegg",
	"Notifications" => "Varsler",
	"Repeat every" => "Gjenta hvert",
	"Higher than" => "Høyere enn",
	"Lower than" => "Lavere enn",
	"Send to" => "Send til",
	"Send warning" => "Send varsel",
	"Rule" => "Regel",
	"Last sent" => "Sist sendt",
	"Device action" => "Enhets hendelse",
	"No device action" => "Ingen hendelse",

	// Mail notifications
	"Notification mail low temperature" => "Advarsel: Temperatur er lav!<br /><br />Sensor: %%sensor%%<br />Temperatur er %%value%% &deg;",
	"Notification mail high temperature" => "Advarsel: Temperatur er høy!<br /><br />Sensor: %%sensor%%<br />Temperatur er %%value%% &deg;",
	"Notification mail low humidity" => "Advarsel: Luftfuktighet er lav!<br /><br />Sensor: %%sensor%%<br />Luftfuktighet er %%value%% %",
	"Notification mail high humidity" => "Advarsel: Luftfuktighet er høy!<br /><br />Sensor: %%sensor%%<br />Luftfuktighet er %%value%% %",
	


	// Lights
	"On" => "På",
	"Off" => "Av",
	"Groups" => "Grupper",
	"Devices" => "Enheter",



	// Div
	"Language" => "Språk",
	"New" => "Ny",
	"Repeat" => "Gjenta",
	"Admin" => "Admin",
	"Total" => "Total",
	"Max" => "Maks",
	"Min" => "Min",
	"Avrage" => "Gjennomsnitt",
	"Stop" => "Stopp",
	"Data" => "Data",
	"ID" => "ID",
	"Name" => "Navn",
	"Ignored" => "Ignorert",
	"Client" => "Klient",
	"Client name" => "Klientnavn",
	"Online" => "Online",
	"Editable" => "Endrebar",
	"Last update" => "Siste oppdatering",
	"Monitor" => "Monitorer",
	"Protocol" => "Protocol",
	"Timezone offset" => "Timezone offset",
	"Time" => "Tid",
	"Active" => "Active",
	"Disabled" => "Disabled",
	"Location" => "Lokasjon",
	"Celsius" => "Celsius",
	"Degrees" => "Grader",
	"Type" => "Type",
	"Value" => "Verdi",
	"Cancel" => "Avbryt",
	"Warning" => "Advarsel",
	"High" => "High",
	"Low" => "Low",
	"Primary" => "Primær",
	"Secondary" => "Sekundær",
	"Now" => "Nå",
	"Action" => "Action",

		// send warning IF temperature IS more/less THAN   / FOR sensor ...
		"If" => "Hvis",
		"Is" => "er",
		"Than" => "enn",
		"For" => "For",


	// Time (ago)
	"since" => "siden",

	"secound" => "sekund",
	"minute" => "minutt",
	"hour" => "time",
	"day" => "dag",
	"week" => "uke",
	"month" => "måned",
	"year" => "år",

	"secounds" => "sekunder",
	"minutes" => "minutter",
	"hours" => "timer",
	"days" => "dager",
	"weeks" => "uker",
	"months" => "måneder",
	"years" => "år",

);

?>