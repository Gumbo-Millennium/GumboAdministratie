<?php
namespace System;

class Input
{
	/**
	 * De input data voor de request.
	 *
	 * @var array
	 */
	public static $input;

	/**
	 * Haal alle input data op voor een request.
	 *
	 * Deze method returnt een samengevoegde array met Input::get en Input::file
	 *
	 * @return array
	 */
	public static function all()
	{
		return array_merge(static::get(), static::file());
	}

	/**
	 * Kijk of de input data een item bevat.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return (! is_null(static::get($key)) and trim((string) static::get($key)) !== '');
	}

	/**
	 * Haal een item van de input data op.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public static function get($key = null, $default = null)
	{
		if (is_null(static::$input)) static::hydrate();

		return Arr::get(static::$input, $key, $default);
	}

	/**
	 * Kijk of de oude input data een item bevat.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function had($key)
	{
		return (! is_null(static::old($key)) and trim((string) static::old($key)) !== '');
	}

	/**
	 * Haal input data op van de vorige request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public static function old($key = null, $default = null)
	{
		if (Config::get('session.driver') == '') {
			throw new \Exception("Sessions moeten geactiveerd zijn om oude input op te halen.");
		}

		return Arr::get(Session::get('aurora_old_input', array()), $key, $default);
	}

	/**
	 * Haal een item uit de geüploadde file data.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
	 */
	public static function file($key = null, $default = null)
	{
		return Arr::get($_FILES, $key, $default);
	}

	/**
	 * Vul de input data voor de request.
	 */
	public static function hydrate()
	{
		switch (Request::method()) {
			case 'GET':
				static::$input =& $_GET;
				break;

			case 'POST':
				static::$input =& $_POST;
				break;

			case 'PUT':
			case 'DELETE':
				if (Request::spoofed()) {
					static::$input =& $_POST;
				} else {
					parse_str(file_get_contents('php://input'), static::$input);
				}
		}
	}
} 