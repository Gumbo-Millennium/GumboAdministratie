<?php
namespace System\Session;

interface Driver
{
	/**
	 * Laad een session via de ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id);

	/**
	 * Sla een session op.
	 *
	 * @param  array  $session
	 */
	public function save($session);

	/**
	 * Verwijder een session via de ID.
	 *
	 * @param  string  $id
	 */
	public function delete($id);
} 