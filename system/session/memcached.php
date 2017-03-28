<?php
namespace System\Session;

use System\Cache;
use System\Config;

class Memcached implements Driver
{
	/**
	 * Laad een session via de ID.
	 *
	 * @param  string $id
	 * @return array
	 */
	public function load($id)
	{
		return Cache::driver('memcached')->get($id);
	}

	/**
	 * Sla een session op.
	 *
	 * @param  array $session
	 */
	public function save($session)
	{
		Cache::driver('memcached')->put($session['id'], $session, Config::get('session.lifetime'));
	}

	/**
	 * Verwijder een session via de ID.
	 *
	 * @param  string $id
	 */
	public function delete($id)
	{
		Cache::driver('memcached')->forget($id);
	}
}