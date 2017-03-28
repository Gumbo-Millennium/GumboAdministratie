<?php
namespace System;

class Cookie
{
	/**
	 * Bepaal of een cookie bestaat.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function has($name)
	{
		return ! is_null(static::get($name));
	}

	/**
	 * Haal een waarde uit een cookie.
	 *
	 * @param  string  $name
	 * @param  mixed   $default
	 * @return string
	 */
	public static function get($name, $default = null)
	{
		return Arr::get($_COOKIE, $name, $default);
	}

	/**
	 * Stel een "permanente" cookie in. De cookie leeft voor 5 jaar.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @param  bool    $http_only
	 * @return bool
	 */
	public static function forever($name, $value, $path = '/', $domain = null, $secure = false, $http_only = false)
	{
		return static::put($name, $value, 2628000, $path, $domain, $secure, $http_only);
	}

	/**
	 * Stel een cookie in. Als een negatief aantal minuten is
	 * opgegeven, word de cookie verwijderd.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  int     $minutes
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @param  bool    $http_only
	 * @return bool
	 */
	public static function put($name, $value, $minutes = 0, $path = '/', $domain = null, $secure = false, $http_only = false)
	{
		if ($minutes < 0) unset($_COOKIE[$name]);

		return setcookie($name, $value, ($minutes != 0) ? time() + ($minutes * 60) : 0, $path, $domain, $secure, $http_only);
	}

	/**
	 * Verwijder een cookie.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function forget($name)
	{
		return static::put($name, null, -60);
	}
} 