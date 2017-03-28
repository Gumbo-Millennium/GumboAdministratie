<?php
namespace System\Session;

use System\Config;

class DB implements Driver, Sweeper
{
	/**
	 * Laad een session via de ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		$session = $this->table()->find($id);

		if (! is_null($session)) {
			return array(
				'id'            => $session->id,
				'last_activity' => $session->last_activity,
				'data'          => unserialize($session->data)
			);
		}
	}

	/**
	 * Sla een session op.
	 *
	 * @param  array $session
	 */
	public function save($session)
	{
		$this->delete($session['id']);

		$this->table()->insert(array(
			'id'            => $session['id'],
			'last_activity' => $session['last_activity'],
			'data'          => serialize($session['data'])
		));
	}

	/**
	 * Verwijder een session via de ID.
	 *
	 * @param  string $id
	 */
	public function delete($id)
	{
		$this->table()->delete($id);
	}

	/**
	 * Verwijder alle verlopen sessies.
	 *
	 * @param  int $expiration
	 */
	public function sweep($expiration)
	{
		$this->table()->where('last_activity', '<', $expiration)->delete();
	}

	/**
	 * Haal een session database query op.
	 *
	 * @return \System\DB\Query
	 */
	private function table()
	{
		return \System\DB::connection()->table(Config::get('session.table'));
	}
}