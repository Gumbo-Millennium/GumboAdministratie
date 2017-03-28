<?php
namespace System\Session;

use System\Cache;
use System\Config;

class APC implements Driver
{
	/**
	 * Laad een session via de ID.
	 *
	 * @param  string $id
	 * @return array
	 */
	public function load($id)
	{
		return Cache::driver('apc')->get($id);
	}

	/**
	 * Sla een session op.
	 *
	 * @param  array $session
	 */
	public function save($session)
	{
		Cache::driver('apc')->put($session['id'], $session, Config::get('session.lifetime'));
	}

	/**
	 * Verwijder een session via de ID.
	 *
	 * @param  string $id
	 */
	public function delete($id)
	{
		Cache::driver('apc')->forget($id);
	}
}