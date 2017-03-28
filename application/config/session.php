<?php

return array(

	/*
	 * Session Driver
	 *--------------------------------------------------------------------------
	 *
	 * De naam van de session driver voor je applicatie.
	 *
	 * Aangezien HTTP stateless is, worden session gebruikt om de "staat"
	 * tussen meerdere requests van dezelfde user te behouden.
	 *
	 * Ondersteunde Drivers: 'file', 'db', 'memcached', 'apc'.
	 */

	'driver' => 'file',

	/*
	 * Session Database
	 *--------------------------------------------------------------------------
	 *
	 * De database tabel waar de sessions opgeslagen moeten worden.
	 *
	 * Deze optie is alleen relevant wanneer je de "db" session driver gebruikt.
	 */

	'table' => 'sessions',

	/*
	 * Session Lifetime
	 *--------------------------------------------------------------------------
	 *
	 * Het aantal minuten dat een session inactief kan zijn voor deze verloopt.
	 */

	'lifetime' => 60,

	/*
	 * Stop Session na Afsluiten
	 *--------------------------------------------------------------------------
	 *
	 * Bepaalt of een session moet verlopen wanneer de web browser gesloten wordt.
	 */

	'expire_on_close' => false,

	/*
	 * Session Cookie Pad
	 *--------------------------------------------------------------------------
	 *
	 * Het pad waarvoor de session cookie beschikbaar is.
	 */

	'path' => '/',

	/*
	 * Session Cookie Domein
	 *--------------------------------------------------------------------------
	 *
	 * Het domein waarvoor de session cookie beschikbaar is.
	 */

	'domain' => null,

	/*
	 * Session Cookie HTTPS
	 *--------------------------------------------------------------------------
	 *
	 * Bepaalt of een session cookie alleen over HTTPS verstuurd mag worden.
	 */

	'https' => false,

	/*
	 * HTTP Only Session Cookie
	 *--------------------------------------------------------------------------
	 *
	 * Bepaald of de session cookie alleen via HTTP bereikbaar mag zijn.
	 *
	 * Note: Het doel van de "HTTP Only" optie is om toegang tot cookies via
	 *       client-side scripting talen te weerhouden. Toch moet deze optie
	 *       niet worden gezien als totale XSS bescherming.
	 */

	'http_only' => false,
);