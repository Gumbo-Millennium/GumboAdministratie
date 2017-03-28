<?php
namespace System\DB;

use System\Config;

class Manager
{
	/**
	 * De opgezette database verbindingen.
	 *
	 * @var array
	 */
	public static $connections = array();

	/**
	 * Haal een databaseconnectie op. Als er geen databasenaam opgegeven is, zal de
	 * default connectie gereturnt worden zoals gedefinieerd in de db config file.
	 *
	 * Note: Databaseconnecties worden gemanaged als singletons.
	 *
	 * @param  string      $connection
	 * @return Connection
	 */
	public static function connection($connection = null)
	{
		if (is_null($connection)) {
			$connection = Config::get('db.default');
		}

		if (! array_key_exists($connection, static::$connections)) {

			if (is_null($config = Config::get('db.connections.'.$connection))) {
				throw new \Exception("Databaseconnectie [$connection] is niet gedefiniëerd.");
			}

			static::$connections[$connection] = new Connection($connection, (object) $config, new Connector);
		}

		return static::$connections[$connection];
	}

	/**
	 * Begin een query tegen een tabel.
	 *
	 * @param  string  $table
	 * @param  string  $connection
	 * @return Query
	 */
	public static function table($table, $connection = null)
	{
		return static::connection($connection)->table($table);
	}

	/**
	 * Magic Method voor het aanroepen van methods op de default databaseconnectie.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::connection(), $method), $parameters);
	}
}