<?php
namespace System\Routing;

use System\Package;
use System\Response;

class Route
{
	/**
	 * De route key, inclusief request method en URI.
	 *
	 * @var string
	 */
	public $key;

	/**
	 * De route callback of array.
	 *
	 * @var mixed
	 */
	public $callback;

	/**
	 * De parameters die gepassed zullen worden naar de route functie.
	 *
	 * @var array
	 */
	public $parameters;

	/**
	 * Maak een nieuwe Route instance.
	 *
	 * @param  string  $key
	 * @param  mixed   $callback
	 * @param  array   $parameters
	 */
	public function __construct($key, $callback, $parameters = array())
	{
		$this->key = $key;
		$this->callback = $callback;
		$this->parameters = $parameters;
	}

	/**
	 * Voer de route functie uit.
	 *
	 * @return Response
	 */
	public function call()
	{
		$response = null;

		// De callback mag een array zijn, wat betekend dat het een filters of een naam heeft en
		// we moeten het verder bekijken om te bepalen wat te doen. Als de callback gewoon een
		// closure is, kunnen we die uitvoeren en het resultaat returnen.
		if (is_callable($this->callback)) {
			$response = call_user_func_array($this->callback, $this->parameters);

		} elseif (is_array($this->callback)) {

			if (isset($this->callback['needs'])) {
				Package::load(explode(', ', $this->callback['needs']));
			}

			$response = isset($this->callback['before']) ? Filter::call($this->callback['before'], array(), true) : null;

			if (is_null($response) and ! is_null($handler = $this->find_route_function())) {
				$response = call_user_func_array($handler, $this->parameters);
			}
		}

		$response = Response::prepare($response);

		if (is_array($this->callback) and isset($this->callback['after'])) {
			Filter::call($this->callback['after'], array($response));
		}

		return $response;
	}

	/**
	 * Haal de route functie uit de route.
	 *
	 * Als er een "do" index bestaat op de callback, is dat de handler.
	 * Anders returnen we de eerste aanroepbare array value.
	 *
	 * @return Callable
	 */
	private function find_route_function()
	{
		if (isset($this->callback['do'])) return $this->callback['do'];

		foreach ($this->callback as $value) {
			if (is_callable($value)) return $value;
		}
	}
} 