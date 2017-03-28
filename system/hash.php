<?php
namespace System;

class Hash
{
	/**
	 * Hash een string met PHPass.
	 *
	 * PHPass levert betrouwbare bcrypt hasing, en wordt gebruikt door veel
	 * populaire PHP applicaties zoals Wordpress en Joomla.
	 *
	 * @access public
	 * @param  string  $value
	 * @return string
	 */
	public static function make($value, $rounds = 10)
	{
		return static::hasher($rounds)->HashPassword($value);
	}

	/**
	 * Bepaal of een niet gehashde value een opgegeven hash matched.
	 *
	 * @param  string  $value
	 * @param  string  $hash
	 * @return bool
	 */
	public static function check($value, $hash)
	{
		return static::hasher()->CheckPassword($value, $hash);
	}

	/**
	 * Maak een nieuwe PHPass instance.
	 *
	 * @return \PasswordHash
	 */
	private static function hasher($rounds = 10)
	{
		require_once SYS_PATH.'vendor/phpass'.EXT;

		return new \PasswordHash($rounds, false);
	}
}