<?php
namespace System\Routing;

use System\Config;

class Loader
{
	/**
	 * Alle routes voor de application.
	 *
	 * @var array
	 */
	private static $routes;

	/**
	 * Het pad waar de routes staan.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Maak een nieuwe route loader instance.
	 *
	 * @param  string  $path
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Laad de benodigde routes voor de request URI.
	 *
	 * @param  string  $uri
	 * @return array
	 */
	public function load($uri)
	{
		$base = (file_exists($path = $this->path.'routes'.EXT)) ? require $path : array();

		return array_merge($this->load_nested_routes(explode('/', $uri)), $base);
	}

	/**
	 * Laad de benodigde routes van de routes map.
	 *
	 * @param  array  $segments
	 * @return array
	 */
	private function load_nested_routes($segments)
	{
		// Als de request URI alleen meer dan één segment is, en de laatste segment bevat een punt, nemen
		// we aan dat de request voor een specifiek format is (users.json of users.xml) en halen alles na
		// de punt weg zodat we het juiste bestand kunnen laden.
		if (count($segments) > 0 and strpos(end($segments), '.') !== false) {
			$segment = array_pop($segments);

			array_push($segments, substr($segments, 0, strpos($segment, '.')));
		}

		// Aangezien het geen deel is van de route directory structuur, shift de modulenaam van het
		// begin van de array zodat we het juiste bestand kunnen vinden.
		if (count($segments) > 0 and ACTIVE_MODULE !== 'application') {
			array_shift($segments);
		}

		// Werk achterwaards door de URI segmenten tot we de diepst mogelijke
		// matchende route directory. Als we die vinden, returnen we die routes.
		foreach (array_reverse($segments, true) as $key => $value) {

			if (file_exists($path = $this->path.'routes/'.implode('/', array_slice($segments, 0, $key + 1)).EXT)) {
				return require $path;
			}
		}

		return array();
	}

	/**
	 * Haal alle routes op voor de applicatie.
	 *
	 * Om performance te verbeteren, wordt deze operatie slechts één keer uitgevoerd. De routes
	 * zullen gecached worden en gereturnt bij elke volgende call.
	 *
	 * @param  bool    $reload
	 * @param  string  $path
	 * @return array
	 */
	public static function all($reload = false, $path = APP_PATH)
	{
		if (! is_null(static::$routes) and ! $reload) return static::$routes;

		// Voeg alle modulepaden samen met het opgegeven pad zodat alle actieve
		// moduleroutes ook geladen worden. Dus, standaard zoekt deze method de
		// application pad en alle actieve module paden voor routes.
		$paths = array_merge(array($path), array_map(function ($module) { return MODULE_PATH.$module.'/'; }, Config::get('application.modules')));

		$routes = array();

		foreach ($paths as $path) {
			if (file_exists($path.'routes'.EXT)) {
				$routes = array_merge($routes, require $path.'routes'.EXT);
			}

			if (is_dir($path.'routes')) {
				// Aangezien route files diep binnen route mappen genest kunnen zijn, moeten we
				// recursief door de directory loopen om elk bestand te vinden.
				$directoryIterator = new \RecursiveDirectoryIterator($path.'routes');

				$recursiveIterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

				foreach ($recursiveIterator as $file) {
					if (filetype($file) === 'file' and strpos($file, EXT) !== false) {
						$routes = array_merge($routes, require $file);
					}
				}
			}
		}

		return static::$routes = $routes;
	}
}