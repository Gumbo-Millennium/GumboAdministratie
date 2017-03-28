<?php

return array(

	/*
	 * Applicatie URL
	 *--------------------------------------------------------------------------
	 *
	 * De URL om de applicatie te openen. Geen trailing slash.
	 */

	'url' => 'http://gumbo.local',

	/*
	 * Applicatie Index
	 *--------------------------------------------------------------------------
	 *
	 * Als je de "index.php" in je URLs gebruikt kun je dit negeren.
	 *
	 * Als je echter mod_rewrite of iets dergelijks gebruikt om schonere
	 * URLs te krijgen stel deze optie in als een lege string.
	 */

	'index' => '',

	/*
	 * Applicatie Taal
	 *--------------------------------------------------------------------------
	 *
	 * De default taal voor de applicatie. Deze taal wordt standaard gebruikt
	 * door Lang library als de default taal met string localisation.
	 */

	'language' => 'nl',

	/*
	 * Applicatie Character Encoding
	 *--------------------------------------------------------------------------
	 *
	 * De default character encoding gebruikt door je applicatie. Dit is de
	 * character encoding dat wordt gebruikt door de Str, Text, en Form classes.
	 */

	'encoding' => 'UTF-8',

	/*
	 * Applicatie Tijdzone
	 *--------------------------------------------------------------------------
	 *
	 * De default tijdzone van je applicatie. De tijdzone wordt gebruikt wanneer
	 * Aurora een datum nodig heeft, zoals bij het schrijven naar een logbestand.
	 */

	'timezone' => 'Europe/Amsterdam',

	/*
	 * Auto-Loaded Packages
	 *--------------------------------------------------------------------------
	 *
	 * De packages die bij elke request afhandeling geladen moet worden. Dit
	 * zouden normaal gesproken packages moeten zijn die je bij bijne elke
	 * request naar je applicatie gebruikt.
	 *
	 * Elke package hier gespecificeerd zal gebootstrapped worden en kan
	 * eenvoudig gebruikt worden door app's routes, models en libraries.
	 *
	 * Note: De package namen in deze array moeten hetzelfde zijn als de
	 *       package map in application/packages.
	 */

	'packages' => array(),

	/*
	 * Actieve Modules
	 *--------------------------------------------------------------------------
	 *
	 * Modules zijn een eenvoudige manier om je applicatie in logische
	 * componentenop te delen. Elke module heeft zijn eigen libraries, models,
	 * routes, views, taalbestandenn en configuratie.
	 *
	 * Hier kun je aangeven welke modules "Actief" zijn voor je applicatie.
	 * Dit geeft Aurora  een makkelijke manier om te weten welke mappen er
	 * gecheckt moeten worden met het auto-loaden van classes, routes en views.
	 */

	'modules' => array(),

	/*
	 * Applicatie Key
	 *--------------------------------------------------------------------------
	 *
	 * De applicatie key zou een 32 karakter string moeten zijn dat totaal
	 * random en geheim is. Deze key wordt gebruikt in de encryptie class om
	 * veilige, encrypted strings te genereren.
	 */

	'key' => '',
	
	
	/*
	 * Mailchimp
	 *--------------------------------------------------------------------------
	 *
	 * Om de synchronisatie van Mailchimp te testen moet je een eigen Mailchimp account hebben. 
	 * Vervolgens kan je de API key en en ID van de te synchroniseren lijst hieronder invoeren.
	 * De lijst heeft 2 custom tags. *LIDNUMMER* en *LIDSTATUS*
	 */
	'mailchimp_api' => '',
	'mailchimp_list' => '',
);