<?php
namespace System\Routing;

class Filter
{
	/**
	 * De gelaade route filters.
	 *
	 * @var array
	 */
	private static $filters = array();

	/**
	 * Registreer een set van route filters.
	 *
	 * @param  array  $filters
	 */
	public static function register($filters)
	{
		static::$filters = array_merge(static::$filters, $filters);
	}

	/**
	 * Verwijder alle geregistreerde route filters.
	 */
	public static function clear()
	{
		static::$filters = array();
	}

	/**
	 * Roep een set route filters aan.
	 *
	 * @param  string  $filters
	 * @param  array   $parameters
	 * @param  bool    $override
	 * @return mixed
	 */
	public static function call($filters, $parameters = array(), $override = false)
	{
		foreach (explode(', ', $filters) as $filter) {

			if (! isset(static::$filters[$filter])) continue;

			$response = call_user_func_array(static::$filters[$filter], $parameters);

			// "Before" filters mogen de request cycle overriden. Bijvoorbeeld, een authenticatie
			// filter mag een user naar een login view redirecten als ze nog niet ingelogd zijn.
			// Daarom returnen we de eerste filter respnse als overriding aanstaat.
			if (! is_null($response) and $override) return $response;
		}
	}
}