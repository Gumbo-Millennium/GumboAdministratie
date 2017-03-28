<?php
namespace System\Cache;

use System\Config;

class Memcached implements Driver
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
		return (($cache = \System\Memcached::instance()->get(Config::get('cache.key').$key)) !== false) ? $cache : null;
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
		\System\Memcached::instance()->set(Config::get('cache.key').$key, $value, 0, $minutes * 60);
	}

	/**
	 * Verwijder een item van de cache.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		\System\Memcached::instance()->delete(\Config::get('cache.key').$key);
	}
}