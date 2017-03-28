<?php
namespace System\Session;

use System\Config;
use System\Crypter;

class Cookie implements Driver
{
	/**
	 * De Crypter instance.
	 *
	 * @var Crypter
	 */
	private $crypter;

	/**
	 * Maak een nieuwe Cookie session driver instance.
	 */
	public function __construct()
	{
		$this->crypter = new Crypter();

		if (Config::get('application.key') == '') {
			throw new \Exception("Je moet een application key instellen voordat je de Cookie session driver kan gebruiken.");
		}
	}

	/**
	 * Laad een session via de ID.
	 *
	 * @param  string $id
	 * @return array
	 */
	public function load($id)
	{
		if (\System\Cookie::has('session_payload')) {
			return unserialize($this->crypter->decrypt(\System\Cookie::get('session_payload')));
		}
	}

	/**
	 * Sla een session op.
	 *
	 * @param  array $session
	 */
	public function save($session)
	{
		if (! headers_sent()) {
			extract(Config::get('session'));

			$payload = $this->crypter->encrypt(serialize($session));

			\System\Cookie::put('session_payload', $payload, $lifetime, $path, $domain, $https, $http_only);
		}
	}

	/**
	 * Verwijder een session via de ID.
	 *
	 * @param  string $id
	 */
	public function delete($id)
	{
		\System\Cookie::forget('session_payload');
	}
}