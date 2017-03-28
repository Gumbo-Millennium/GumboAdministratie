<?php
namespace System;

class Memcached
{
	/**
	 * De Memcache instance.
	 *
	 * @var \Memcache
	 */
	private static $instance = null;

	/**
	 * Haal de singleton Memcache instance op.
	 *
	 * @return \Memcache
	 */
	public static function instance()
	{
		if (is_null(static::$instance)) {
			static::$instance = static::connect(Config::get('cache.servers'));
		}

		return static::$instance;
	}

	/**
	 * Verbind met de geconfigureerde Memcached servers.
	 *
	 * @param  array      $servers
	 * @return \Memcache
	 */
	private static function connect($servers)
	{
		if (! class_exists('Memcache')) {
			throw new \Exception('Geprobeerd om Memcached te gebruiken, maar de Memcached PHP extentie is niet geïnstalleerd op de server.');
		}

		$memcache = new \Memcache;

		foreach($servers as $server) {
			$memcache->addserver($server['host'], $server['port'], true, $server['weight']);
		}

		if ($memcache->getVersion() === false) {
			throw new \Exception('Mecached is geconfigureerd, maar er kon geen verbinding gemaakt worden. Controleer de Memcache configuratie.');
		}

		return $memcache;
	}
} 