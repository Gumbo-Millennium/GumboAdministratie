<?php
namespace System;

class Benchmark
{
	/**
	 * Alle Benchmark starttijden.
	 *
	 * @var array
	 */
	public static $marks = array();

	/**
	 * Start een benchmark.
	 *
	 * Nadat een benchmark is gestart, kan de verlopen tijd in milliseconden
	 * opgehaald worden met de "check" method.
	 *
	 * @param  string  $name
	 * @see    Benchmark::check
	 */
	public static function start($name)
	{
		static::$marks[$name] = microtime(true);
	}

	/**
	 * Haal de verstreken tijd op in miliseconden sinds de benchmark is gestart.
	 *
	 * @param  string  $name
	 * @return float
	 * @see    Benchmark::start
	 */
	public static function check($name)
	{
		if (array_key_exists($name, static::$marks)) {
			return  number_format((microtime(true) - static::$marks[$name]) * 1000, 2);
		}

		return 0.0;
	}

	/**
	 * Haal het geheugengebruik op in megabytes.
	 *
	 * @return float
	 */
	public static function memory()
	{
		return number_format(memory_get_usage() / 1024 / 1024, 2);
	}
} 