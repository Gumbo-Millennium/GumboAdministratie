<?php
namespace System;

class Config
{
	/**
	 * Alle gelaade configuratieitems.
	 *
	 * @var array
	 */
	public static $items = array();

	/**
	 * Bepaal of een configuratieitem of bestand bestaat.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return ! is_null(static::get($key));
	}

	/**
	 * Haal een configuratieitem op.
	 *
	 * Configuratieitems worden opgehaald met de "dot" notatie. Dus, vragen voor de
	 * "application.timezone" configuratieitem zal de "timezone" optie returnen van
	 * de "application" configuratiebestand.
	 *
	 * Als de naam van een configuratieitem word opgegeven zonder een item, dan zal
	 * de hele configuratie array opgehaald worden.
	 *
	 * @param  string  $key
	 * @param  string  $default
	 * @return array
	 */
	public static function get($key, $default = null)
	{
		list($module, $file, $key) = static::parse($key);

		if (! static::load($module, $file)) {
			return is_callable($default) ? call_user_func($default) : $default;
		}

		if (is_null($key)) return static::$items[$module][$file];

		return Arr::get(static::$items[$module][$file], $key, $default);
	}

	/**
	 * Stel een configuratieitem in.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 */
	public static function set($key, $value)
	{
		list($module, $file, $key) = static::parse($key);

		if (! static::load($module, $file)) {
			throw new \Exception("Error met het instellen van de configuratieoptie. Configuratiebestand [$file] is niet gedefinieerd.");
		}

		Arr::set(static::$items[$module][$file], $key, $value);
	}

	/**
	 * Parse een configuratie key.
	 *
	 * De waarde links van de punt is het configuratiebestand,
	 * terwijl rechts van de punt het item binnen de file is.
	 *
	 * @param  string  $key
	 * @return array
	 */
	private static function parse($key)
	{
		$module = (strpos($key, '::') !== false) ? substr($key, 0, strpos($key, ':')) : 'application';

		if ($module !== 'application') {
			$key = substr($key, strpos($key, ':') + 2);
		}

		$key = (count($segments = explode('.', $key)) > 1) ? implode('.', array_slice($segments, 1)) : null;

		return array($module, $segments[0], $key);
	}

	/**
	 * Laad alle configuratieitems van een bestand.
	 *
	 * @param  string  $file
	 * @param  string  $module
	 * @return bool
	 */
	private static function load($module, $file)
	{
		if (isset(static::$items[$module]) and array_key_exists($file, static::$items[$module])) return true;

		$path = ($module === 'application') ? CONFIG_PATH : MODULE_PATH.$module.'/config/';

		// Laad de basis configuratie file. Wanneer deze geladen is, zullen we de omgevings-
		// specifieke configuratieopties in de basisarray samenvoegen. Dit zorgt voor het
		// eenvoudig bepalen van configuratieopties op basis van de omgeving.
		$config = (file_exists($base = $path.$file.EXT)) ? require $base : array();

		if (isset($_SERVER['AURORA_ENV']) and file_exists($path = $path.$_SERVER['AURORA_ENV'].'/'.$file.EXT)) {
			$config = array_merge($config, require $path);
		}

		if (count($config) > 0) static::$items[$module][$file] = $config;

		return isset(static::$items[$module][$file]);
	}
} 