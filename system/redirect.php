<?php
namespace System;

class Redirect
{
	/**
	 * De redirect response
	 *
	 * @var Response
	 */
	public $response;

	/**
	 * Maak een nieuwe redirect instance.
	 *
	 * @param  Response  $response
	 */
	public function __construct($response)
	{
		$this->response = $response;
	}

	/**
	 * Maak een redirect response.
	 *
	 * @param  string    $url
	 * @param  int       $status
	 * @param  string    $method
	 * @param  bool      $https
	 * @return Redirect
	 */
	public static function to($url, $status = 302, $method = 'location', $https = false)
	{
		$url = URL::to($url, $https);

		return ($method == 'refresh')
			? new static(Response::make('', $status)->header('Refresh', '0;url='.$url))
			: new static(Response::make('', $status)->header('Location', $url));
	}

	/**
	 * Maak een redirect response naar een HTTPS URL.
	 *
	 * @param  string    $url
	 * @param  int       $status
	 * @param  string    $method
	 * @return Response
	 */
	public static function to_secure($url, $method = 'location', $status = 302)
	{
		return static::to($url, $status, $method, true);
	}

	/**
	 * Voeg een item toe aan de session flash data.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Response
	 */
	public function with($key, $value)
	{
		if (Config::get('session.driver') == '') {
			throw new \Exception("Geprobeerd om data the flashen naar de session, maar er is geen session driver opgegeven.");
		}

		Session::flash($key, $value);

		return $this;
	}

	/**
	 * Magic Method om redirects naar routes af te handelen.
	 */
	public static function __callStatic($method, $parameters)
	{
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		if (strpos($method, 'to_secure_') === 0) {
			return static::to(URL::to_route(substr($method, 10), $parameters, true));
		}

		if (strpos($method, 'to_') === 0) {
			return static::to(URL::to_route(substr($method, 3), $parameters));
		}

		throw new \Exception("Method [$method] is niet gedefineerd in de Redirect class.");
	}


} 