<?php
namespace System\Routing;

use System\Request;

class Router
{
	/**
	 * De request method en URI.
	 *
	 * @var string
	 */
	public $request;

	/**
	 * Alle geladen routes.
	 *
	 * @var array
	 */
	public $routes;

	/**
	 * Maak een nieuwe router voor een request method en URI.
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @param  Loader  $loader
	 */
	public function __construct($method, $uri, $loader = null)
	{
		// Plaats de request method en URI in route form. Routes beginnen met
		// de request method en een forward slash.
		$this->request = $method.' /'.trim($uri, '/');

		$this->routes = $loader->load($uri);
	}

	/**
	 * Maak een nieuwe router voor een request method en URI.
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @param  Loader  $loader
	 * @return Router
	 */
	public static function make($method, $uri, $loader)
	{
		return new static($method, $uri, $loader);
	}

	/**
	 * Doorzoek een set routes voor een route die overeenkomt met een
	 * method en URI.
	 *
	 * @return Route
	 */
	public function route()
	{
		// Check eerst voor een letterlijke route match. Als we er één vinden, is
		// het niet nodig om alle routes langs te gaan.
		if (isset($this->routes[$this->request])) {
			return Request::$route = new Route($this->request, $this->routes[$this->request]);
		}

		foreach ($this->routes as $keys => $callback) {
			// Check alleen routes die meerdere URIs of wildcards hebben.
			// Andere routes zijn opgevangen door de check van letterlijke matches.
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false) {

				foreach (explode(', ', $keys) as $key) {

					if (preg_match('#^'.$this->translate_wildcards($key).'$#', $this->request)) {
						return Request::$route = new Route($keys, $callback, $this->parameters($this->request, $key));
					}
				}
			}
		}
	}

	/**
	 * Vertaal route URI wildcards in echte regular expressions.
	 *
	 * @param  string  $key
	 * @return string
	 */
	private function translate_wildcards($key)
	{
		$replacements = 0;

		// Voor optionele parameters, vertaal eerst de wildcards naar hun
		// regex equalivant, zonder het ")?" einde. We voegen de afsluitingen
		// weer toe als we weten hoeveel replacements we gedaan hebben.
		$key = str_replace(array('/(:num?)', '/(:any?)'), array('(?:/([0-9]+)', '(?:/([a-zA-Z0-9\.\-_:]+)'), $key, $replacements);

		$key .= ($replacements > 0) ? str_repeat(')?', $replacements) : '';

		return str_replace(array(':num', ':any'), array('[0-9]+', '[a-zA-Z0-9\.\-_:]+'), $key);
	}

	/**
	 * Haal de parameters uit de URI gebasseerd op een route URI.
	 *
	 * Elke route segment binnen haakjes wordt gezien als een parameter.
	 *
	 * @param  string  $uri
	 * @param  string  $route
	 * @return array
	 */
	private function parameters($uri, $route)
	{
		return array_values(array_intersect_key(explode('/', $uri), preg_grep('/\(.+\)/', explode('/', $route))));
	}
} 