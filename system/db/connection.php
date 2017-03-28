<?php
namespace System\DB;

class Connection
{
	/**
	 * De connectienaam.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * De connectie configuratie.
	 *
	 * @var array
	 */
	public $config;

	/**
	 * De PDO connectie.
	 *
	 * @var \PDO
	 */
	public $pdo;

	/**
	 * Alle queries da zijn uitgevoerd op de connectie.
	 *
	 * @var array
	 */
	public $queries = array();

	/**
	 * Maak een nieuwe Connection instance.
	 *
	 * @param  string     $name
	 * @param  object     $config
	 * @param  Connector  $connector
	 */
	public function __construct($name, $config, $connector)
	{
		$this->name = $name;
		$this->config = $config;
		$this->pdo = $connector->connect($this->config);
	}

	/**
	 * Voer een SQL query uit tegen de connectie en return het eerste resultaat.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return object
	 */
	public function first($sql, $bindings = array())
	{
		return (count($results = $this->query($sql, $bindings)) > 0) ? $results[0] : null;
	}

	/**
	 * Voer een SQL query uit tegen de connectie.
	 *
	 * De method returnt het volgende gebasseerd op query type:
	 *
	 *     SELECT -> Array van stdClasses
	 *     UPDATE -> Aantal beinvloede rijen.
	 *     DELETE -> Aantal beinvloede rijen.
	 *     ELSE   -> Boolean true/false afhankelijk van succes.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return array
	 */
	public function query($sql, $bindings = array())
	{
		$this->queries[] = $sql;

		$query = $this->pdo->prepare($sql);

		$result = $query->execute($bindings);

		if (strpos(strtoupper($sql), 'SELECT') === 0 or strpos(strtoupper($sql), 'SHOW') === 0) {
			return $query->fetchAll(\PDO::FETCH_CLASS, 'stdClass');

		} elseif (strpos(strtoupper($sql), 'UPDATE') === 0 or strpos(strtoupper($sql), 'DELETE') === 0) {
			return $query->rowCount();
		}

		return $result;
	}

	/**
	 * Begin een query tegen een tabel.
	 *
	 * Deze methode is gewoon een handige shortcut voor Query::table
	 *
	 * @param  string  $table
	 * @return Query
	 */
	public function table($table)
	{
		return new Query($table, $this);
	}

	/**
	 * Haal de keyword identifier wrapper voor deze connectie op.
	 *
	 * @return string
	 */
	public function wrapper()
	{
		if (array_key_exists('wrap', $this->config) and $this->config['wrap'] === false) return '';

		return ($this->driver() == 'mysql') ? '`' : '"';
	}

	/**
	 * Haal de drivernaam op voor een databaseconnectie.
	 *
	 * @return string
	 */
	public function driver()
	{
		return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
	}

	/**
	 * Haal de tabel prefix op voor een databaseconnectie.
	 *
	 * @return string
	 */
	public function prefix()
	{
		return (array_key_exists('prefix', $this->config)) ? $this->config['prefix'] : '';
	}
}