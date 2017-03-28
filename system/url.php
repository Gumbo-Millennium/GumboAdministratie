<?php
namespace System;

class URL
{
	/**
	 * Genereer een applicatie URL.
	 *
	 * Als de huidige URL al goed gevormd is, wordt het onveranderd gereturnt.
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @param  bool    $asset
	 * @return string
	 */
	public static function to($url = '', $https = false, $asset = false)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) !== false) return $url;

		$base = Config::get('application.url').'/'.Config::get('application.index');

		if ($asset and Config::get('application.index') !== '') {
			$base = str_replace('/'.Config::get('application.index'), '', $base);
		}

		if ($https and strpos($base, 'http://') === 0) {
			$base = 'https://'.substr($base, 7);
		}

		return rtrim($base, '/').'/'.trim($url, '/');
	}

	/**
	 * Genereer een applicatie URL met HTTPS.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public static function to_secure($url = '')
	{
		return static::to($url, true);
	}

	/**
	 * Genereer een applicatie URL naar een asset. Het indexbestand
	 * word niet toegevoegd aan de URL.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public static function to_asset($url)
	{
		return static::to($url, Request::is_secure(), true);
	}

	/**
	 * Genereer een URL voor een route naam.
	 *
	 * Voor routes die wildcard parameters hebben, mag een array gepassed worden
	 * als de tweede parameter. De waardes van deze array zullen gebruikt worden
	 * om de wildcard segmenten te vullen van de route URI.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_route($name, $parameters = array(), $https = false)
	{
		if (! is_null($route = Routing\Finder::find($name, Routing\Loader::all()))) {
			$uris = explode(', ', key($route));

			$uri = substr($uris[0], strpos($uris[0], '/'));

			foreach ($parameters as $parameter) {
				$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
			}

			$uri = str_replace(array('/(:any?)', '/num?)'), '', $uri);

			return static::to($uri, $https);
		}

		throw new \Exception("Fout tijdens het genereren van een genaamde route voor route [$name]. Route is niet gedefineerd.");
	}

	/**
	 * Genereer een HTTPS URL voor een route naam.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @return string
	 */
	public static function to_secure_route($name, $parameters = array())
	{
		return static::to_route($name, $parameters, true);
	}

	/**
	 * Maak een URL vriendelijke "slug"
	 *
	 * @param  string  $title
	 * @param  string  $separator
	 * @return string
	 */
	public static function slug($title, $separator = '-')
	{
		$title = Str::ascii($title);

		// Verwijder alle karakters die niet de seperator, letters, nummers, of whitespace zijn.
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', Str::lower($title));

		// Vervang alle seperator karakters en whitespace door een enkele seperator.
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return trim($title, $separator);
	}

	/**
	 * Magic Method voor het maken van dynamische route URLs.
	 */
	public static function __callStatic($method, $parameters)
	{
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		if (strpos($method, 'to_secure_') === 0) {
			return static::to_route(substr($method, 10), $parameters, true);
		}

		if (strpos($method, 'to_') === 0) {
			return static::to_route(substr($method, 3), $parameters);
		}

		throw new \Exception("Method [$method] is niet gedefineerd in de URL class.");
	}
} 