<?php
namespace System;

class View
{
	/**
	 * De naam van de view.
	 *
	 * @var string
	 */
	public $view;

	/**
	 * De view data.
	 *
	 * @var array
	 */
	public $data = array();

	/**
	 * De module die de view bevat.
	 *
	 * @var string
	 */
	public $module;

	/**
	 * Het pad naar de view.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * De gedefinieerde view composers.
	 *
	 * @var array
	 */
	public static $composers;

	/**
	 * Maak een nieuwe view instance.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 */
	public function __construct($view, $data = array())
	{
		$this->data = $data;

		list($this->module, $this->path, $this->view) = static::parse($view);

		$this->compose();
	}

	/**
	 * Maak een nieuwe view instance.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return View
	 */
	public static function make($view, $data = array())
	{
		return new static($view, $data);
	}

	/**
	 * Maak een nieuwe view instance van een viewnaam.
	 *
	 * De viewnamen voor de actieve module worden eerst gezocht, gevolgd
	 * door de viewnamen van de application directory, gevolgd door de
	 * viewnamen van alle andere modules.
	 *
	 * @param  string  $name
	 * @param  array   $data
	 * @return View
	 */
	protected static function of($name, $data = array())
	{
		foreach (array_unique(array_merge(array(ACTIVE_MODULE, 'application'), Config::get('application.modules'))) as $module) {
			static::load_composers($module);

			foreach (static::$composers[$module] as $key => $value) {

				if ($name === $value or (isset($value['name']) and $name === $value['name'])) {
					return new static($key, $data);
				}
			}
		}

		throw new \Exception("Named view [$name] is niet gedefinieerd.");
	}

	/**
	 * Parse een view identifier en haal de module, pad en viewnaam op.
	 *
	 * @param  string  $view
	 * @return array
	 */
	protected static function parse($view)
	{
		$module = (strpos($view, '::') !== false) ? substr($view, 0, strpos($view, ':')) : 'application';

		$path = ($module == 'application') ? VIEW_PATH : MODULE_PATH.$module.'/views/';

		if ($module != 'application') {
			$view = substr($view, strpos($view, ':') + 2);
		}

		return array($module, $path, $view);
	}

	/**
	 * Roep de composer aan voor de view instance.
	 */
	protected function compose()
	{
		static::load_composers($this->module);

		if (isset(static::$composers[$this->module][$this->view])) {

			foreach ((array) static::$composers[$this->module][$this->view] as $key => $value) {
				if (is_callable($value)) return call_user_func($value, $this);
			}
		}
	}

	/**
	 * Laad de view composers voor de opgegeven modules.
	 *
	 * @param  string  $module
	 */
	protected static function load_composers($module)
	{
		if (isset(static::$composers[$module])) return;

		$composers = ($module == 'application') ? APP_PATH.'composers'.EXT : MODULE_PATH.$module.'/composers'.EXT;

		static::$composers[$module] = (file_exists($composers)) ? require $composers : array();
	}

	/**
	 * Return de geparste inhoud van de view.
	 *
	 * @return string
	 */
	public function get()
	{
		$view = str_replace('.', '/', $this->view);

		if (! file_exists($this->path.$view.EXT)) {
			Exception\Handler::make(new \Exception("View [$view] bestaat niet."))->handle();
		}

		foreach ($this->data as &$data) {
			if ($data instanceof View or $data instanceof Response) $data = (string) $data;
		}

		ob_start() and extract($this->data, EXTR_SKIP);

		try { include $this->path.$view.EXT; } catch (\Exception $e) { Exception\Handler::make($e)->handle(); }

		return ob_get_clean();
	}

	/**
	 * Voeg een view instance toe aan de view data.
	 *
	 * @param  string  $key
	 * @param  string  $view
	 * @param  array   $data
	 * @return View
	 */
	public function partial($key, $view, $data = array())
	{
		return $this->bind($key, new static($view, $data));
	}

	/**
	 * Voeg een key / value pair toe aan de view data.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return View
	 */
	public function bind($key, $value)
	{
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Magic Method voor het afhandelen van het dynamisch aanmaken van named views.
	 */
	public static function __callStatic($method, $parameters)
	{
		if (strpos($method, 'of_') === 0) {
			return static::of(substr($method, 3), Arr::get($parameters, 0, array()));
		}
	}

	/**
	 * Magic Method om items uit de view data te halen.
	 */
	public function __get($key)
	{
		return $this->data[$key];
	}

	/**
	 * Magic Method om items in de view data op te slaan.
	 */
    public function __set($key, $value)
	{
		$this->bind($key, $value);
	}

	/**
	 * Magic Method om te bepalen of een item in de view data zit.
	 */
	public function __isset($key)
	{
		return array_key_exists($key, $this->data);
	}

	/**
	 * Magic Method om items uit de view data te verwijderen.
	 */
	public function __unset($key)
	{
		unset($this->data[$key]);
	}

	/**
	 * Return de geparste inhoud van de view.
	 */
	public function __toString()
	{
		return $this->get();
	}


}