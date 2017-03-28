<?php
namespace System;

if (Config::get('session.driver') == '') {
	throw new \Exception("Je moet een session driver opgeven voordat de Auth class gebruikt kan worden.");
}

class Auth
{
	/**
	 * De huidige gebruiker van de applicatie.
	 *
	 * Als er geen gebruiker is ingelogd, is dit NULL. Anders, bevat dit het resultaat
	 * van de "by_id" closure in de authentication configbestand.
	 *
	 * normaal gesproken dient de gebruiker via de "user" method benaderd te worden.
	 *
	 * @var object
	 */
	public static $user;

	/**
	 * De key die word gebruikt om user ID op te slaan in de sessie.
	 *
	 * @var string
	 */
	protected static $key = 'aurora_user_id';

	/**
	 * Bepaal of de huidige gebruiker van de applicatie is ingelogd.
	 *
	 * @return bool
	 */
	public static function check()
	{
		return ! is_null(static::user());
	}

	/**
	 * Haal de huidige gebruiker van de applicatie op.
	 *
	 * Om de gebruiker op te halen, wordt de user ID in de sessie gepassed naar
	 * de "by_id" closure in het authenticatie config bestand. Het resultaat van
	 * de closure wordt gecached en gereturnt.
	 *
	 * @return object
	 * @see    Auth::$user
	 */
	public static function user()
	{
		if (is_null(static::$user) and Session::has(static::$key)) {
			static::$user = call_user_func(Config::get('auth.by_id'), Session::get(static::$key));
		}

		return static::$user;
	}

	/**
	 * Probeer een gebruiker in te loggen in je applicatie.
	 *
	 * Als de gebruiker credentials geldig zijn. De user's ID wordt opgeslagen in de session en
	 * de user word gezien als "ingelogd" bij volgende requests naar de applicatie.
	 *
	 * Het wachtwoord gepassed naar de method moet plain text zijn, aangezien het
	 * gehashed wordt door de Hash class tijdens authenticeren.
	 *
	 * @param  string  $username
	 * @param  string  $password
	 * @return bool
	 */
	public static function login($username, $password)
	{
		if (! is_null($user = call_user_func(Config::get('auth.by_email'), $username))) {
			
			if (Hash::check($password, $user->wachtwoord)) {
				static::remember($user);

				return true;
			}
		}

		return false;
	}

	/**
	 * Log een gebruiker in bij je applicatie.
	 *
	 * De user's ID wordt opgeslagen in de session en de gebruiker zal gezien worden als
	 * "ingelogd" bij de volgende requests naar je applicatie.
	 *
	 * Note: De gebruiker die doorgegeven wordt moet een object zijn met een "id" property.
	 *
	 * @param  object  $user
	 */
	public static function remember($user)
	{
		static::$user = $user;

		Session::put(static::$key, $user->id);
	}

	/**
	 * Log de gebruiker uit van je applicatie.
	 *
	 * De user ID wordt verwijderd van de sessie en de gebruiker wordt niet
	 * meer gezien als ingelogd bij volgende requests.
	 */
	public static function logout()
	{
		static::$user = null;

		Session::forget(static::$key);
	}
}