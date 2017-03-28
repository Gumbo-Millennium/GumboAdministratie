<?php
namespace System\Cache;

interface Driver
{
	/**
	 * Bepaal of een item bestaat in de cache.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key);

	/**
	 * Haal een item uit de cache.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function get($key);

	/**
	 * Schrijf een item naar de cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes);

	/**
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key);
} 