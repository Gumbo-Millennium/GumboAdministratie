<?php

return array(

	/*
	 * Error Detail
	 *--------------------------------------------------------------------------
	 *
	 * Gedetailleerde error berichten bevatten informatie over het bestand
	 * waarin een fout voorkomt, een stack trace en een snapshot van de source
	 * code waarin de fout voorkwam.
	 *
	 * In een productieomgeving is het beter om dit uit te zetten voor betere
	 * security en gebruikerservaring.
	 */

	'detail' => true,

	/*
	 * Error Logging
	 *--------------------------------------------------------------------------
	 *
	 * Error Logging gebruikt de "logger" functie die hieronder staat om error
	 * berichten te loggen, zodat je complete vrijheid hebt om te bepalen hoe
	 * errors gelogd worden. Geniet van de flexibiliteit.
	 */

	'log' => false,

	/*
	 * Error Logger
	 *--------------------------------------------------------------------------
	 *
	 * Omdat er diverse manieren zijn om error logging af te handelen, krijg
	 * je volledige flexibiliteit om dit op jouw eigen manier te doen.
	 *
	 * Deze functie wordt aangeroepen wanneer er een error ontstaat in je
	 * applicatie. Je kan de error op welke manier je maar wil loggen.
	 *
	 * De error "severity" dat naar de method gepassed wordt is de leesbare
	 * severity zoals "Parsing Error" of "Fatal Error".
	 *
	 * Een simpel logging systeem is al opgezet. Standaard worden alle error
	 * gelogd in de storage/log.txt file.
	 */

	'logger' => function($severity, $message)
	{
		File::append(STORAGE_PATH.'log.txt', date('Y-m-d H:i:s').' '.$severity.' - '.$message.PHP_EOL);
	},
);