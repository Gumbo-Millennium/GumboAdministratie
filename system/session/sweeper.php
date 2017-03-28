<?php
namespace System\Session;

interface Sweeper
{
	/**
	 * Verwijder alle verlopen sessions.
	 *
	 * @param int  $expiration
	 */
	public function sweep($expiration);
}