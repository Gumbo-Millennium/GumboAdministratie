<?php
namespace System;

class Session
{
	/**
	 * De actieve session driver.
	 *
	 * @var Session\Driver
	 */
	public static $driver;

	/**
	 * De session payload, welke de session ID, data en laatste activiteit timestamp bevat.
	 *
	 * @var array
	 */
	public static $session = array();

	/**
	 * Haal de session driver op.
	 *
	 * @return Session\Driver
	 */
	public static function driver()
	{
		if (is_null(static::$driver)) {

			switch (Config::get('session.driver')) {
				case 'cookie':
					return static::$driver = new Session\Cookie;

				case 'file':
					return static::$driver = new Session\File;

				case 'db':
					return static::$driver = new Session\DB;

				case 'memcached':
					return static::$driver = new Session\Memcached;

				case 'apc':
					return static::$driver = new Session\APC;

				default:
					throw new \Exception("Session driver [$driver] wordt niet ondersteund.");
			}
		}

		return static::$driver;
	}

	/**
	 * laad een gebruiker via de ID.
	 *
	 * @param  string  $id
	 */
	public static function load($id)
	{
		static::$session = (! is_null($id)) ? static::driver()->load($id) : null;

		if (static::invalid(static::$session)) {
			static::$session = array('id' => Str::random(40), 'data' => array());
		}

		if (! static::has('csrf_token')) {
			static::put('csrf_token', Str::random(16));
		}

		static::$session['last_activity'] = time();
	}

	/**
	 * Bepaal of een session geldig is.
	 *
	 * Een session wordt gezien als geldig als het bestaat en niet verlopen is.
	 *
	 * @param  array  $session
	 * @return bool
	 */
	private static function invalid($session)
	{
		return is_null($session) or (time() - $session['last_activity']) > (Config::get('session.lifetime') * 60);
	}

	/**
	 * Bepaal of er een item bestaat in de session of flashdata.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return (! is_null(static::get($key)));
	}

	/**
	 * Haal een item uit de session of flash data.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		foreach (array($key, ':old:'.$key, ':new:'.$key) as $possibility) {
			if (array_key_exists($possibility, static::$session['data'])) return static::$session['data'][$possibility];
		}

		return is_callable($default) ? call_user_func($default) : $default;
	}

	/**
	 * Plaats een item in de session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 */
	public static function put($key, $value)
	{
		static::$session['data'][$key] = $value;
	}

	/**
	 * Plaats een item in de session flashdata.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 */
	public static function flash($key, $value)
	{
		static::put(':new:'.$key, $value);
	}

	/**
	 * Verwijder een item uit de session.
	 *
	 * @param  string  $key
	 */
	public static function forget($key)
	{
		unset(static::$session['data'][$key]);
	}

	/**
	 * Verwijder alle items uit de session.
	 */
	public static function flush()
	{
		static::$session['data'] = array();
	}

	/**
	 * Vernieuw de session ID.
	 */
	public static function regenerate()
	{
		static::driver()->delete(static::$session['id']);

		static::$session['id'] = Str::random(40);
	}

	/**
	 * Sluit de session.
	 *
	 * De session wordt opgeslagen in vaste opslag en de session cookie wordt gestuurd
	 * naar de browser. De old input data wordt ook opgeslagen in de session flash
	 * data.
	 */
	public static function close()
	{
		static::flash('aurora_old_input', Input::get());

		static::age_flash();

		static::driver()->save(static::$session);

		static::write_cookie();

		if (mt_rand(1, 100) <= 2 and static::driver() instanceof Session\Sweeper) {
			static::driver()->sweep(time() - (Config::get('session.lifetime') * 60));
		}
	}

	/**
	 * Laat de session flashdata verlopen.
	 */
	private static function age_flash()
	{
		foreach (static::$session['data'] as $key => $value) {
			if (strpos($key, ':old:') === 0) static::forget($key);
		}

		foreach (static::$session['data'] as $key => $value) {

			if (strpos($key, ':new:') === 0) {
				static::put(':old:'.substr($key, 5), $value);

				static::forget($key);
			}
		}
	}

	/**
	 * Schrijf de session cookie.
	 */
	private static function write_cookie()
	{
		if (! headers_sent()) {
			$minutes = (Config::get('session.expire_on_close')) ? 0 : Config::get('session.lifetime');

			Cookie::put('aurora_session', static::$session['id'], $minutes, Config::get('session.path'), Config::get('session.domain'), Config::get('session.https'), Config::get('session.http_only'));
		}
	}
} 