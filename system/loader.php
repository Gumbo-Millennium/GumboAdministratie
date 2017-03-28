<?php
namespace System;

class Loader
{
	/**
	 * De paden die doorzocht moeten worden door de loader.
	 *
	 * @var array
	 */
	public static $paths = array(BASE_PATH, MODEL_PATH, LIBRARY_PATH);

	/**
	 * Alle class aliases.
	 *
	 * @var array
	 */
	public static $aliases = array();

	/**
	 * Alle actieve modules.
	 *
	 * @var array
	 */
	public static $modules = array();

	/**
	 * Bootstrap de auto-loader.
	 */
	public static function bootstrap()
	{
		static::$aliases = Config::get('aliases');
		static::$modules = Config::get('application.modules');
	}

	/**
	 * Laad een class file voor een opgegeven class naam.
	 *
	 * Deze functie is geregistreerd op de SPL auto-loader stack door de front controller tijdens elke request.
	 *
	 * Alle Aurora class namen volgen een namespace naar directory conventie.
	 *
	 * @param  string  $class
	 * @return bool
	 */
	public static function load($class)
	{
		$file = strtolower(str_replace('\\', '/', $class));

		if (array_key_exists($class, static::$aliases)) return class_alias(static::$aliases[$class], $class);

		(static::load_from_registered($file)) or static::load_from_module($file);
	}

	/**
	 * Laad een class dat is opgeslagen in de geregistreerde directories.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	private static function load_from_registered($file)
	{
		foreach (static::$paths as $directory) {

			if (file_exists($path = $directory.$file.EXT)) {
				require $path;

				return true;
			}
		}

		return false;
	}

	/**
	 * Laad een class dat is opgeslagen in een module.
	 *
	 * @param  string  $file
	 * @return mixed
	 */
	private static function load_from_module($file)
	{
		// Aangezien alle module models en libraries genamespaced moeten
		// zijn naar de modulenaam, pakken we de modulenaam van de file.
		$module = substr($file, 0, strpos($file, '/'));

		if (in_array($module, static::$modules)) {
			$module = MODULE_PATH.$module.'/';

			// Snij de modulenaam van de bestandsnaam. Ook al zijn module libraries en
			// models genamespaced onder de module, zal er natuurlijk geen map zijn die
			// matched met de namespace in de libraries en models folders van de
			// module. Het eraf snijden zorgt ervoor dat we goed kunnen zoeken naar de
			// relevante class file.
			$file = substr($file, strpos($file, '/') + 1);

			foreach (array($module.'models', $module.'libraries') as $directory) {
				if (file_exists($path = $directory.'/'.$file.EXT)) return require $path;
			}
		}
	}

	/**
	 * Registreer een pad in de auto-loader. Nadat het pad geregistreerd is, wordt
	 * deze op dezelfde manier als de models en libraries mappen gecheckt.
	 *
	 * @param  string  $path
	 */
	public static function register($path)
	{
		static::$paths[] = rtrim($path, '/').'/';
	}
}