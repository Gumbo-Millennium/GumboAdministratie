<?php
namespace System\Session;

class File implements Driver, Sweeper
{

	/**
	 * Laad een sessie via de ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id)
	{
		if (file_exists($path = SESSION_PATH.$id)) return unserialize(file_get_contents($path));
	}

	/**
	 * Sla een session op.
	 *
	 * @param  array  $session
	 */
	public function save($session)
	{
		file_put_contents(SESSION_PATH.$session['id'], serialize($session), LOCK_EX);
	}

	/**
	 * Verwijder een session via de ID.
	 *
	 * @param  string  $id
	 */
	public function delete($id)
	{
		@unlink(SESSION_PATH.$id);
	}

	/**
	 * Verwijder alle verlopen sessies.
	 *
	 * @param  int  $expiration
	 */
	public function sweep($expiration)
	{
		foreach (glob(SESSION_PATH.'*') as $file) {
			if (filetype($file) == 'file' and filemtime($file) < $expiration) @unlink($file);
		}
	}
}