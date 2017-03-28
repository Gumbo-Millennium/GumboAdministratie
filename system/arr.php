<?php
namespace System;

class Arr
{
	/**
	 * Haal een item op uit een array.
	 *
	 * Als de opgegeven key null is, wordt de hele array gereturnd. De array mag ook
	 * worden benaderd met de JavaScript "dot" style notatie. Het ophalen van items
	 * genest in meerdere arrays is ook ondersteund.
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get($array, $key, $default = null)
	{
		if (is_null($key)) return $array;

		foreach (explode('.', $key) as $segment)
		{
			if (! is_array($array) or ! array_key_exists($segment, $array))
				return is_callable($default) ? call_user_func($default) : $default;

			$array = $array[$segment];
		}

		return $array;
	}

	/**
	 * Zet een array item naar een opgegeven value.
	 *
	 * Deze method is vooral handig voor het instellen van de value in een array
	 * met een variabele diepte, zoals configuratie arrays.
	 *
	 * Net zoals bij de Arr::get method, is JavaScript "dot" syntax ondersteund.
	 *
	 * @param array   $array
	 * @param string  $key
	 * @param mixed   $value
	 */
	public static function set(&$array, $key, $value)
	{
		if (is_null($key)) return $array = $value;

		$keys = explode('.', $key);

		while (count($keys) > 1)
		{
			$key = array_shift($keys);

			if (! isset($array[$key]) or ! is_array($array[$key]))
				$array[$key] = array();

			$array =& $array[$key];
		}

		$array[array_shift($keys)] = $value;
	}
} 