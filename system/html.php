<?php
namespace System;

class HTML
{
	/**
	 * Converteer HTML karakters naar entities.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, Config::get('application.encoding'), false);
	}

	/**
	 * Genereer een JavaScript referentie.
	 *
	 * @param  string  $url
	 * @param  array   $attributes
	 * @return string
	 */
	public static function script($url, $attributes = array())
	{
		return '<script type="text/javascript" src="'.static::entities(URL::to_asset($url)).'"'.static::attributes($attributes).'></script>'.PHP_EOL;
	}

	/**
	 * Genereer een CSS referentie.
	 *
	 * @param  string  $url
	 * @param  array   $attributes
	 * @return string
	 */
	public static function style($url, $attributes = array())
	{
		if (! array_key_exists('media', $attributes)) $attributes['media'] = 'all';

		$attributes = $attributes + array('rel' => 'stylesheet', 'type' => 'text/css');

		return '<link href="'.static::entities(URL::to_asset($url)).'"'.static::attributes($attributes).'>'.PHP_EOL;
	}

	/**
	 * Genereer een HTML span.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function span($value, $attributes = array())
	{
		return '<span'.static::attributes($attributes).'>'.static::entities($value).'</span>';
	}

	/**
	 * Genereer een HTML link.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @param  bool    $asset
	 * @return string
	 */
	public static function link($url, $title, $attributes = array(), $https = false, $asset = false)
	{
		return '<a href="'.static::entities(URL::to($url, $https, $asset)).'"'.static::attributes($attributes).'>'.static::entities($title).'</a>';
	}

	/**
	 * Genereer een HTTPS HTML link.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public static function link_to_secure($url, $title, $attributes = array())
	{
		return static::link($url, $title, $attributes, true);
	}

	/**
	 * Genereer een HTML link naar een asset.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @param  bool    $https
	 * @return string
	 */
	public static function link_to_asset($url, $title, $attributes = array(), $https = false)
	{
		return static::link($url, $title, $attributes, $https, true);
	}

	/**
	 * Genereer een HTTPS HTML link naar een asset.
	 *
	 * @param  string  $url
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public static function link_to_secure_asset($url, $title, $attributes = array())
	{
		return static::link_to_asset($url, $title, $attributes, true);
	}

	/**
	 * Genereer een HTML link naar een route.
	 *
	 * @param  string  $name
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	public static function link_to_route($name, $title, $parameters = array(), $attributes = array(), $https = false)
	{
		return static::link(URL::to_route($name, $parameters, $https), $title, $attributes);
	}

	/**
	 * Genereer een HTTPS HTML link naar een route.
	 *
	 * @param  string  $name
	 * @param  string  $title
	 * @param  array   $parameters
	 * @param  array   $attributes
	 * @return string
	 */
	public static function link_to_secure_route($name, $title, $parameters = array(), $attributes = array())
	{
		return static::link_to_route($name, $title, $parameters, $attributes, true);
	}

	/**
	 * Genereer een HTML mailto link.
	 *
	 * @param  string  $email
	 * @param  string  $title
	 * @param  array   $attributes
	 * @return string
	 */
	public static function mailto($email, $title = null, $attributes = array())
	{
		$email = static::email($email);

		if (is_null($title)) $title = $email;

		return '<a href="&#109;&#097;&#105;&#108;&#116;&#111;&#058;'.$email.'"'.static::attributes($attributes).'>'.static::entities($title).'</a>';
	}

	/**
	 * Obfuscate een emailadres om te voorkomen dat spambots deze sniffen.
	 *
	 * @param  string  $email
	 * @return string
	 */
	public static function email($email)
	{
		return str_replace('@', '&#64;', static::obfuscate($email));
	}

	/**
	 * Genereer een HTML image.
	 *
	 * @param  string  $url
	 * @param  string  $alt
	 * @param  array   $attributes
	 * @return string
	 */
	public static function image($url, $alt = '', $attributes = array())
	{
		$attributes['alt'] = static::entities($alt);

		return '<img src="'.static::entities(URL::to_asset($url)).'"'.static::attributes($attributes).'>';
	}

	/**
	 * Genereer een ordered list.
	 *
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	public static function ol($list, $attributes = array())
	{
		return static::list_elements('ol', $list, $attributes);
	}

	/**
	 * Genereer een unordered list.
	 *
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	public static function ul($list, $attributes = array())
	{
		return static::list_elements('ul', $list, $attributes);
	}

	/**
	 * Genereer een ordered, of unordered list.
	 *
	 * @param  string  $type
	 * @param  array   $list
	 * @param  array   $attributes
	 * @return string
	 */
	private static function list_elements($type, $list, $attributes = array())
	{
		$html = '';

		foreach ($list as $key => $value) {
			$html .= (is_array($value)) ? static::list_elements($type, $value) : '<li>'.static::entities($value).'</li>';
		}

		return '<'.$type.static::attributes($attributes).'>'.$html.'</'.$type.'>';
	}

	/**
	 * Bouw een lijst van HTML attributen.
	 *
	 * @param  array   $attributes
	 * @return string
	 */
	public static function attributes($attributes)
	{
		$html = array();

		foreach ($attributes as $key => $value) {
			// Neem aan dat numeric-keyed attributen dezelfde key en value hebben.
			// Voorbeeld: required="required", autofocus="autofocus", etc.
			if (is_numeric($key)) $key = $value;

			if (! is_null($value)) {
				$html[] = $key.'="'.static::entities($value).'"';
			}
		}

		return (count($html) > 0) ? ' '.implode(' ', $html) : '';
	}

	/**
	 * Obfuscate een string om te voorkomen dat spambots deze sniffen.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function obfuscate($value)
	{
		$safe = '';

		foreach (str_split($value) as $letter) {

			switch (rand(1, 3)) {
				// Converteer de letter naar de entity representatie.
				case 1:
					$safe .= '&#'.ord($letter).';';
					break;

				// Coverteer de letter naar een Hex karaktercode.
				case 2:
					$safe .= '&#x'.dechex(ord($letter)).';';
					break;

				// Geen encoding.
				case 3:
					$safe .= $letter;
			}
		}

		return $safe;
	}

	/**
	 * Magic Method voor het behandelen van dynamische static methods.
	 */
	public static function __callStatic($method, $parameters)
	{
		if (strpos($method, 'link_to_secure_') === 0) {
			array_unshift($parameters, substr($method, 15));

			return forward_static_call_array('HTML::link_to_secure_route', $parameters);
		}

		if (strpos($method, 'link_to_') === 0) {
			array_unshift($parameters, substr($method, 8));

			return forward_static_call_array('HTML::link_to_route', $parameters);
		}

		throw new \Exception("Static method [$method] is niet gedefineerd in de HTML class.");
	}
}