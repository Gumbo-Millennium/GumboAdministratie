<?php
namespace System;

class Package
{
	/**
	 * Alle geladen packages.
	 *
	 * @var array
	 */
	public static $loaded = array();

	/**
	 * Laad een package of set van packages.
	 *
	 * @param  string|array  $packages
	 */
	public static function load($packages)
	{
		foreach ((array) $packages as $package) {

			if (! static::loaded($package) and file_exists($bootstrap = PACKAGE_PATH.$package.'/bootstrap'.EXT)) {
				require $bootstrap;
			}

			static::$loaded[] = $package;
		}
	}

	/**
	 * Bepaal of een opgegeven package geladen is.
	 *
	 * @param  string  $package
	 * @return bool
	 */
	public static function loaded($package)
	{
		return array_key_exists($package, static::$loaded);
	}
}