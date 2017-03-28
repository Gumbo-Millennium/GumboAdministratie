<?php
namespace System;

class Cache
{
	/**
	 * Alle actieve cache drivers.
	 *
	 * @var Cache\Driver
	 */
	public static $drivers = array();

	/**
	 * Haal een cache driver instance op.
	 *
	 * Als er geen drivernaam is opgegeven  zal de default cache driver gereturnd
	 * worden zoals aangegeven in de cache configuration file.
	 *
	 * @param  string  $driver
	 * @return Cache\Driver
	 */
	public static function driver($driver = null)
	{
		if (is_null($driver)) $driver = Config::get('cache.driver');

		if (! array_key_exists($driver, static::$drivers)) {

			switch ($driver) {
				case 'file':
					return static::$drivers[$driver] = new Cache\File;

				case 'memcached':
					return static::$drivers[$driver] = new Cache\Memcached;

				case 'apc':
					return static::$drivers[$driver] = new Cache\APC;

				default:
					throw new \Exception("Cache driver [$driver] word niet ondersteund.");
			}
		}

		return static::$drivers[$driver];
	}

	/**
	 * Haal een item van de cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  string  $driver
	 * @return mixed
	 */
	public static function get($key, $default = null, $driver = null)
	{
		if (is_null($item = static::driver($driver)->get($key))) {
			return is_callable($default) ? call_user_func($default) : $default;
		}

		return $item;
	}

	/**
	 * Haal een item op uit de cache. Als het item niet bestaat in de cache, sla
	 * de default waarde op in de cache en return het.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @param  int     $minutes
	 * @param  string  $driver
	 * @return mixed
	 */
	public static function remember($key, $default, $minutes, $driver = null)
	{
		if (! is_null($item = static::get($key, null, $driver))) return $item;

		$default = is_callable($default) ? call_user_func($default) : $default;

		static::driver($driver)->put($key, $default, $minutes);

		return $default;
	}

	/**
	 * Pass alle andere methods naar de default driver.
	 *
	 * Method calls naar de driver doorpassen zorgt voor een betere API voor jou.
	 * Bijvoorbeeld, in plaats van Cache::driver()->foo(), kun je gewoon Cache::foo() doen.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::driver(), $method), $parameters);
	}
} 