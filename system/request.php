<?php
namespace System;

class Request
{
	/**
	 * De route die de huidige request afhandeld.
	 *
	 * @var Route
	 */
	public static $route;

	/**
	 * De request URI.
	 *
	 * @var string
	 */
	public static $uri;

	/**
	 * Haal de Request URI op.
	 *
	 * Als de request naar de root van de applicatie is, zal er een enkele slash worden gereturnt.
	 *
	 * @return string
	 */
	public static function uri()
	{
		if (! is_null(static::$uri)) return static::$uri;

		$uri = static::raw_uri();

		if (strpos($uri, $base = parse_url(Config::get('application.url'), PHP_URL_PATH)) === 0) {
			$uri = substr($uri, strlen($base));
		}

		if (strpos($uri, $index = '/index.php') === 0) {
			$uri = substr($uri, strlen($index));
		}

		return static::$uri = (($uri = trim($uri, '/')) == '') ? '/' : $uri;
	}

	private static function raw_uri()
	{
		if (isset($_SERVER['PATH_INFO'])) {
			$uri = $_SERVER['PATH_INFO'];

		} elseif (isset($_SERVER['REQUEST_URI'])) {
			$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		} else {
			throw new \Exception('Kan de request URI niet bepalen.');
		}

		if ($uri === false) {
			throw new \Exception("Misvormde request URI. Request beëindigd.");
		}

		return $uri;
	}

	/**
	 * Haal de request method op.
	 *
	 * @return string
	 */
	public static function method()
	{
		return (static::spoofed()) ? $_POST['REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Bepaal of de request method gespoofed word door een hidden form element.
	 *
	 * Hidden form elementen worden gebruikt om PUT en DELETE requests te
	 * spoofen omdat die niet ondersteund worden door HTML forms.
	 *
	 * @return bool
	 */
	public static function spoofed()
	{
		return is_array($_POST) and array_key_exists('REQUEST_METHOD', $_POST);
	}

	/**
	 * Haal aanvrager's IP adres op.
	 *
	 * @return string
	 */
	public static function ip()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];

		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];

		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * Haal de HTTP protocol op voor de request.
	 *
	 * @return string
	 */
	public static function protocol()
	{
		return (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	}

	/**
	 * Bepaal of de request HTTPS gebruikt.
	 *
	 * @return bool
	 */
	public static function is_secure()
	{
		return (static::protocol() == 'https');
	}

	/**
	 * Bepaal of de request een AJAX request is.
	 *
	 * @return bool
	 */
	public static function is_ajax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}

	/**
	 * Bepaal of de route die de request afhandeld een bepaalde naam heeft.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function route_is($name)
	{
		return (is_array(static::$route->callback) and isset(static::$route->callback['name']) and static::$route->callback['name'] === $name);
	}

	/**
	 * Magic Method om dynamische static methods af te handelen.
	 */
	public static function __callStatic($method, $parameters)
	{
		if (strpos($method, 'route_is_') === 0) {
			return static::route_is(substr($method, 9));
		}
	}
}
