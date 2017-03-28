<?php
namespace System\Cache;

use System\Config;

class APC implements Driver
{
	/**
	 * Bepaal of een item bestaat in de cache.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return (! is_null($this->get($key)));
	}

	/**
	 * Haal een item uit de cache.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function get($key)
	{
		return (! is_null($cache = apc_fetch(Config::get('cache.key').$key))) ? $cache : null;
	}

	/**
	 * Schrijf een item naar de cache.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @param  int     $minutes
	 * @return void
	 */
	public function put($key, $value, $minutes)
	{
		apc_store(Config::get('cache.key').$key, $value, $minutes * 60);
	}

	/**
	 * Verwijder een item van de cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		apc_delete(Config::get('cache.key').$key);
	}
}