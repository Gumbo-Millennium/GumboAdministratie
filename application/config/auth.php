<?php

return array(

	/*
	 * Haal users op via ID
	 *--------------------------------------------------------------------------
	 *
	 * Deze method wordt aangeroepen door de Auth::user() method wanneer er
	 * geprobeerd wordt om een user via hun user ID op te halen, zoals wanneer
	 * er een user met de ID opgeslagen in de session opgehaald wordt.
	 *
	 * Je bent vrij om deze methode aan te passen voor je applicatie hoe je maar wil.
	 */

	'by_id' => function($id)
	{
		return Persoon::find($id);
	},

	/*
	 * Haal users op via username
	 *--------------------------------------------------------------------------
	 *
	 * Deze method wordt aangeroepen door de Auth::check() method wanneer er
	 * geprobeerd wordt om een user via hun username op te halen, zoals wanneer
	 * er credentials uit een login form gecheckt wordt.
	 *
	 * Je bent vrij om deze methode aan te passen voor je applicatie hoe je maar wil.
	 *
	 * Note: Deze method moet een object returnen met een "id" en "password"
	 *       property. Het type object maakt niet uit.
	 */

	'by_email' => function($username)
	{
		return Persoon::where_email($username)->first();
	},
);