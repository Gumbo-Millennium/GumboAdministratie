<?php
namespace System\DB;

use System\Config;

class Connector
{
	/**
	 * De PDO connection opties.
	 *
	 * @var array
	 */
	public $options = array(
		\PDO::ATTR_CASE => \PDO::CASE_LOWER,
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
		\PDO::ATTR_STRINGIFY_FETCHES => false,
		\PDO::ATTR_EMULATE_PREPARES => false,
	);

	/**
	 * Maak een PDO databaseverbinding.
	 *
	 * @param  object  $connection
	 * @return \PDO
	 */
	public function connect($config)
	{
		switch ($config->driver) {
			case 'sqlite':
				return $this->connect_to_sqlite($config);

			case 'mysql':
			case 'pgsql':
				return $this->connect_to_server($config);

			default:
				return $this->connect_to_generic($config);
		}

		throw new \Exception('Database driver '.$config->driver.' wordt niet ondersteund.');
	}

	/**
	 * Maak verbinding met een SQLite database.
	 *
	 * SQLite database paden kunnen opgegeven worden als relatief pad van de application/db
	 * directory, of als een absoluut pad naar elke locatie op het bestandsysteem. In-memory
	 * databases zijn ook ondersteund.
	 *
	 * @param  object  $config
	 * @return \PDO
	 */
	private function connect_to_sqlite($config)
	{
		if ($config->database == ':memory:') {
			return new \PDO('sqlite::memory:', null, null, $this->options);

		} elseif (file_exists($path = DATABASE_PATH.$config->database.'.sqlite')) {
			return new \PDO('sqlite:'.$path, null, null, $this->options);

		} elseif (file_exists($config->database)) {
			return new \PDO('sqlite:'.$config->database, null, null, $this->options);
		}

		throw new \Exception("SQLite database [".$config->database."] kon niet gevonden worden.");
	}

	/**
	 * Verbind met een MySQL of PostgreSQL database server.
	 *
	 * @param  object  $config
	 * @return \PDO
	 */
	private function connect_to_server($config)
	{
		$dsn = $config->driver.':host='.$config->host.';dbname='.$config->database;

		if (isset($config->port)) {
			$dsn .= ';port='.$config->port;
		}

		$connection = new \PDO($dsn, $config->username, $config->password, $this->options);

		if (isset($config->charset)) {
			$connection->prepare("SET NAMES '".$config->charset."'")->execute();
		}

		return $connection;
	}

	/**
	 * Verbind met een generieke data source.
	 *
	 * @param  object  $config
	 * @return \PDO
	 */
	private function connect_to_generic($config)
	{
		return new \PDO($config->driver.':'.$config->dsn, $config->username, $config->password, $this->options);
	}
} 