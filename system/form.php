<?php
namespace System;

class Form
{
	/**
	 * Slaat labelnamen op.
	 *
	 * @var array
	 */
	private static $labels = array();

	/**
	 * Open een HTML form.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return string
	 */
	public static function open($action = null, $method = 'POST', $attributes = array(), $https = false)
	{
		$attributes['action'] = HTML::entities(URL::to(((is_null($action)) ? Request::uri() : $action), $https));

		// PUT en DELETE methods worden gespoofed met een verborgen veld met de request method.
		// Aangezien HTML PUT en DELETE niet ondersteund op forms, gebruiken we POST.
		$attributes['method'] = ($method == 'PUT' or $method == 'DELETE') ? 'POST' : $method;

		if (! array_key_exists('accept-charset', $attributes)) {
			$attributes['accept-charset'] = Config::get('application.encoding');
		}

		$html = '<form'.HTML::attributes($attributes).'>';

		if ($method == 'PUT' or $method == 'DELETE') {
			$html .= PHP_EOL.static::input('hidden', 'REQUEST_METHOD', $method);
		}

		return $html.PHP_EOL;
	}

	/**
	 * Open een HTML form met een HTTPS action.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @return string
	 */
	public static function open_secure($action = null, $method = 'POST', $attributes = array())
	{
		return static::open($action, $method, $attributes, true);
	}

	/**
	 * Open een HTML form dat file uploads accepteert.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return string
	 */
	public static function open_for_files($action = null, $method = 'POST', $attributes = array(), $https = false)
	{
		$attributes['enctype'] = 'multipart/form-data';

		return static::open($action, $method, $attributes, $https);
	}

	/**
	 * Open een HTML form dat file uploads accepteert met een HTTPS action.
	 *
	 * @param  string  $action
	 * @param  string  $method
	 * @param  array   $attributes
	 * @return string
	 */
	public static function open_secure_for_files($action = null, $method = 'POST', $attributes = array())
	{
		return static::open_for_files($action, $method, $attributes, true);
	}

	/**
	 * Sluit een HTML form.
	 *
	 * @return string
	 */
	public static function close()
	{
		return '</form>';
	}

	/**
	 * Genereer een hidden veld met de huidige CSRF token.
	 *
	 * @return string
	 */
	public static function token()
	{
		return static::input('hidden', 'csrf_token', static::raw_token());
	}

	/**
	 * Haal de huidige CSRF token op.
	 *
	 * @return string
	 */
	public static function raw_token()
	{
		if (Config::get('session.driver') == '') {
			throw new \Exception('Sessions moeten aanstaan om CSRF tokens op te halen.');
		}

		return Session::get('csrf_token');
	}

	/**
	 * Maak een HTML label element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function label($name, $value, $attributes = array())
	{
		static::$labels[] = $name;

		return '<label for="'.$name.'"'.HTML::attributes($attributes).'>'.HTML::entities($value).'</label>'.PHP_EOL;
	}

	/**
	 * Maak een HTML input element.
	 *
	 * @param  string  $type
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function input($type, $name, $value = null, $attributes = array())
	{
		$id = static::id($name, $attributes);

		return '<input'.HTML::attributes(array_merge($attributes, compact('type', 'name', 'value', 'id'))).'>'.PHP_EOL;
	}

	/**
	 * Maak een HTML text input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function text($name, $value = null, $attributes = array())
	{
		return static::input('text', $name, $value, $attributes);
	}

	/**
	 * Maak een HTML password input element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return string
	 */
	public static function password($name, $attributes = array())
	{
		return static::input('password', $name, null, $attributes);
	}

	/**
	 * Maak een HTML hidden input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function hidden($name, $value = null, $attributes = array())
	{
		return static::input('hidden', $name, $value, $attributes);
	}

	/**
	 * Maak een HTML search input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function search($name, $value = null, $attributes = array())
	{
		return static::input('search', $name, $value, $attributes);
	}

	/**
	 * Maak een HTML email input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function email($name, $value = null, $attributes = array())
	{
		return static::input('email', $name, $value, $attributes);
	}

	/**
	 * Maak een HTML telefoon input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function telephone($name, $value = null, $attributes = array())
	{
		return static::input('tel', $name, $value, $attributes);
	}

	/**
	 * Maak een HTML URL input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function url($name, $value = null, $attributes = array())
	{
		return static::input('url', $name, $value, $attributes);
	}

	/**
	 * Maak een HTML number input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function number($name, $value = null, $attributes = array())
	{
		return static::input('number', $name, $value, $attributes);
	}
	
	/**
	 * Maak een HTML date input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function date($name, $value = null, $attributes = array())
	{
		return static::input('date', $name, $value, $attributes);
	}

	/**
	 * Maak een HTML file input element.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return string
	 */
	public static function file($name, $attributes = array())
	{
		return static::input('file', $name, null, $attributes);
	}

	/**
	 * Maak een HTML textarea element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function textarea($name, $value, $attributes)
	{
		$attributes = array_merge($attributes, array('id' => static::id($name, $attributes), 'name' => $name));

		if (! isset($attributes['rows'])) $attributes['rows'] = 10;

		if (! isset($attributes['cols'])) $attributes['cols'] = 50;

		return '<textarea'.HTML::attributes($attributes).'>'.HTML::entities($value).'</textarea>'.PHP_EOL;
	}

	/**
	 * Maak een HTML select element.
	 *
	 * @param  string  $name
	 * @param  array   $options
	 * @param  string  $selected
	 * @param  array   $attributes
	 * @return string
	 */
	public static function select($name, $options = array(), $selected= null, $attributes = array())
	{
		$attributes = array_merge($attributes, array('id' => static::id($name, $attributes), 'name' => $name));

		$html = array();

		foreach ($options as $value => $display) {
			$option_attributes = array('value' => HTML::entities($value), 'selected' => ($value == $selected || (is_array($selected) && in_array($value, $selected))) ? 'selected' : null);

			$html[] = '<option'.HTML::attributes($option_attributes).'>'.HTML::entities($display).'</option>';
		}

		return '<select'.HTML::attributes($attributes).'>'.implode('', $html).'</select>'.PHP_EOL;
	}

	/**
	 * Maak een HTML checkbox input element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  bool    $checked
	 * @param  array   $attributes
	 * @return string
	 */
	public static function checkbox($name, $value = null, $checked = false, $attributes = array())
	{
		return static::checkable('checkbox', $name, $value, $checked, $attributes);
	}

	/**
	 * Maak een HTML radio button element.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  bool    $checked
	 * @param  array   $attributes
	 * @return string
	 */
	public static function radio($name, $value = null, $checked = false, $attributes = array())
	{
		return static::checkable('radio', $name, $value, $checked, $attributes);
	}

	/**
	 * Maak een checkable input element.
	 *
	 * @param  string  $type
	 * @param  string  $name
	 * @param  string  $value
	 * @param  bool    $checked
	 * @param  array   $attributes
	 * @return string
	 */
	private static function checkable($type, $name, $value, $checked, $attributes)
	{
		$attributes = array_merge($attributes, array(static::id($name, $attributes), ($checked) ? 'checked' : null));

		return static::input($type, $name, $value, $attributes);
	}

	/**
	 * Maak een HTML submit input element.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function submit($value, $attributes = array())
	{
		return static::input('submit', null, $value, $attributes);
	}

	/**
	 * Maak een HTML reset input element.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function reset($value, $attributes = array())
	{
		return static::input('reset', null, $value, $attributes);
	}

	/**
	 * Maak een HTML image input element.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function image($value, $attributes = array())
	{
		return static::input('image', null, $value, $attributes);
	}

	/**
	 * Maak een HTML button element.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function button($value, $attributes = array())
	{
		return '<button'.HTML::attributes($attributes).'>'.HTML::entities($value).'</button>'.PHP_EOL;
	}

	/**
	 * Bepaal de ID attribuut voor een form element.
	 *
	 * Een expliciet opgegeven ID in de attributen heeft voorrang, daarna worden
	 * de labelnamen gecheckt voor een label die met de elementnaam matched.
	 *
	 * @param  string  $name
	 * @param  array   $attributes
	 * @return string
	 */
	private static function id($name, $attributes)
	{
		if (array_key_exists('id', $attributes)) return $attributes['id'];

		if (in_array($name, static::$labels)) return $name;
	}
} 