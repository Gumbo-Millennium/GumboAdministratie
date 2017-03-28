<?php
namespace System\Routing;

class Finder
{
	/**
	 * De genaamde routes die tot nu toe zijn gevonden.
	 *
	 * @var array
	 */
	public static $names = array();

	/**
	 * Vind een route op naam.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public static function find($name, $routes)
	{
		if (array_key_exists($name, static::$names)) return static::$names[$name];

		$arrayIterator = new \RecursiveArrayIterator($routes);

		$recursiveIterator = new \RecursiveIteratorIterator($arrayIterator);

		// Aangezien routes diep genest kunnen zijn in sub-maooen, moeten we recursief
		// door de mappen heen loopen en alle routes verzamelen.
		foreach ($recursiveIterator as $iterator) {
			$route = $recursiveIterator->getSubIterator();

			if (isset($route['name']) and $route['name'] == $name) {
				return static::$names[$name] = array($arrayIterator->key() => iterator_to_array($route));
			}
		}
	}
} 