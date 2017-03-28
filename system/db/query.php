<?php
namespace System\DB;

use System\Str;
use System\Config;
use System\Paginator;

class Query
{
	/**
	 * De database connectie.
	 *
	 * @var Connection
	 */
	public $connection;

	/**
	 * De SELECT clause.
	 *
	 * @var string
	 */
	public $select;

	/**
	 * Geeft aan of de query distinct resultaten moet returnen.
	 *
	 * @var bool
	 */
	public $distinct = false;

	/**
	 * De FROM clause.
	 *
	 * @var string
	 */
	public $from;

	/**
	 * De tabelnaam.
	 *
	 * @var string
	 */
	public $table;

	/**
	 * De WHERE clause.
	 *
	 * @var string
	 */
	public $where = 'WHERE 1 = 1';

	/**
	 * De ORDER BY kolommen.
	 *
	 * @var array
	 */
	public $orderings = array();

	/**
	 * De LIMIT value.
	 *
	 * @var int
	 */
	public $limit;

	/**
	 * De OFFSET value.
	 *
	 * @var int
	 */
	public $offset;

	/**
	 * De query value bindings.
	 *
	 * @var array
	 */
	public $bindings = array();

	/**
	 * Maak een nieuwe query instance.
	 *
	 * @param  string      $table
	 * @param  Connection  $connection
	 */
	function __construct($table, $connection)
	{
		$this->table = $table;
		$this->connection = $connection;
		$this->from = 'FROM '.$this->wrap($table);
	}

	/**
	 * Maak een nieuwe query instance.
	 *
	 * @param  string      $table
	 * @param  Connection  $connection
	 * @return Query
	 */
	public static function table($table, $connection)
	{
		return new static($table, $connection);
	}

	/**
	 * Forceer de query of distinct resultaten terug te geven.
	 *
	 * @return Query
	 */
	public function distinct()
	{
		$this->distinct = true;
		return $this;
	}

	/**
	 * Voeg kolommen toe aan de SELECT clause
	 *
	 * @param  array  $columns
	 * @return Query
	 */
	public function select($columns = array('*'))
	{
		$this->select = ($this->distinct) ? 'SELECT DISTINCT ' : 'SELECT ';

		$this->select .= implode(', ', array_map(array($this, 'wrap'), $columns));

		return $this;
	}

	/**
	 * Stel de FROM clause in.
	 *
	 * @param  string  $from
	 * @return Query
	 */
	public function from($from)
	{
		$this->from = $from;
		return $this;
	}

	/**
	 * Voeg een join toe aan de query.
	 *
	 * @param  string  $table
	 * @param  string  $column1
	 * @param  string  $operator
	 * @param  string  $column2
	 * @param  string $type
	 * @return Query
	 */
	public function join($table, $column1, $operator, $column2, $type = 'INNER')
	{
		$this->from .= ' '.$type.' JOIN '.$this->wrap($table).' ON '.$this->wrap($column1).' '.$operator.' '.$this->wrap($column2);
		return $this;
	}

	/**
	 * Voeg een left join toe aan de query.
	 *
	 * @param  string  $table
	 * @param  string  $column1
	 * @param  string  $operator
	 * @param  string  $column2
	 * @return Query
	 */
	public function left_join($table, $column1, $operator, $column2)
	{
		return $this->join($table, $column1, $operator, $column2, 'LEFT');
	}

	/**
	 * Reset de where clause naar zijn initiele staat. Alle bindings worden verwijderd.
	 */
	public function reset_where()
	{
		$this->where = 'WHERE 1 = 1';
		$this->bindings = array();
	}

	/**
	 * Voeg een handmatige where conditie toe aan de query.
	 *
	 * @param  string  $where
	 * @param  array   $bindings
	 * @param  string  $connector
	 * @return Query
	 */
	public function raw_where($where, $bindings = array(), $connector = 'AND')
	{
		$this->where .= ' '.$connector.' '.$where;
		$this->bindings = array_merge($this->bindings, $bindings);

		return $this;
	}

	/**
	 * Voeg een handmatige or where conditie toe aan de query.
	 *
	 * @param  string  $where
	 * @param  array   $bindings
	 * @return Query
	 */
	public function raw_or_where($where, $bindings = array())
	{
		return $this->raw_where($where, $bindings, 'OR');
	}

	/**
	 * Voeg een where conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @param  string  $connector
	 * @return Query
	 */
	public function where($column, $operator, $value, $connector = 'AND')
	{
		$this->where .= ' '.$connector.' '.$this->wrap($column).' '.$operator.' ?';
		$this->bindings[] = $value;

		return $this;
	}

	/**
	 * Voeg een or where conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @return Query
	 */
	public function or_where($column, $operator, $value)
	{
		return $this->where($column, $operator, $value, 'OR');
	}

	/**
	 * Voeg een where conditie voor de primary key toe aan de query.
	 * Dit is gewoon een shortcut method voor het gemak.
	 *
	 * @param  mixed  $value
	 * @return Query
	 */
	public function where_id($value)
	{
		return $this->where('id', '=', $value);
	}

	/**
	 * Voeg een or where conditie voor de primary key toe aan de query.
	 * Dit is gewoon een shortcut method voor het gemak.
	 *
	 * @param  mixed  $value
	 * @return Query
	 */
	public function or_where_id($value)
	{
		return $this->or_where('id', '=', $value);
	}

	/**
	 * Voeg een where in conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @param  string  $connector
	 * @return Query
	 */
	public function where_in($column, $values, $connector = 'AND')
	{
		$this->where .= ' '.$connector.' '.$this->wrap($column).' IN ('.$this->parameterize($values).')';
		$this->bindings = array_merge($this->bindings, $values);

		return $this;
	}

	/**
	 * Voeg een or where in conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @return Query
	 */
	public function or_where_in($column, $values)
	{
		return $this->where_in($column, $values, 'OR');
	}

	/**
	 * Voeg een where not in conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @param  string  $connector
	 * @return Query
	 */
	public function where_not_in($column, $values, $connector = 'AND')
	{
		$this->where .= ' '.$connector.' '.$this->wrap($column).' NOT IN ('.$this->parameterize($values).')';
		$this->bindings = array_merge($this->bindings, $values);

		return $this;
	}

	/**
	 * Voeg een or where not in conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @return Query
	 */
	public function or_where_not_in($column, $values)
	{
		return $this->where_not_in($column, $values, 'OR');
	}

	/**
	 * Voeg een where null conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @param  string  $connector
	 * @return Query
	 */
	public function where_null($column, $connector = 'AND')
	{
		$this->where .= ' '.$connector.' '.$this->wrap($column).' IS NULL';
		return $this;
	}

	/**
	 * Voeg een or where null conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @return Query
	 */
	public function or_where_null($column)
	{
		return $this->where_null($column, 'OR');
	}

	/**
	 * Voeg een where not null conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @param  string  $connector
	 * @return Query
	 */
	public function where_not_null($column, $connector = 'AND')
	{
		$this->where .= ' '.$connector.' '.$this->wrap($column).' IS NOT NULL';
		return $this;
	}

	/**
	 * Voeg een or where not null conditie toe aan de query.
	 *
	 * @param  string  $column
	 * @return Query
	 */
	public function or_where_not_null($column)
	{
		return $this->where_not_null($column, 'OR');
	}

	/**
	 * Voeg dynamische where condities toe aan de query.
	 *
	 * Dynamische queries worden afgevangen door de __call method en worden hier geparsed.
	 * Ze bieden een makkelijke API voor het bouwen van simpele condities.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return Query
	 */
	private function dynamic_where($method, $parameters)
	{
		// Strip de "where_" van de method af.
		$finder = substr($method, 6);

		// Splits de kolomnamen van de connector.
		$segments = preg_split('/(_and_|_or_)/i', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);

		// De connector variabel zal bepalen welke connector er wordt gebruikt voor de conditie.
		// We veranderen deze als we nieuwe connectors tegenkomen in de dynamic method string.
		//
		// De index variabel helpt ons om de juiste parameter waarde voor de where condition te
		// vinden. We tellen deze iedere keer op als we een condition toevoegen.
		$connector = 'AND';

		$index = 0;

		foreach ($segments as $segment) {

			if ($segment != '_and_' and $segment != '_or_') {

				if (! array_key_exists($index, $parameters)) {
					throw new \Exception("Verkeerd aantal parameters voor dynamic finder [$method].");
				}

				$this->where($segment, '=', $parameters[$index], $connector);

				$index++;
			} else {
				$connector = trim(strtoupper($segment), '_');
			}
		}

		return $this;
	}

	/**
	 * Voeg een orderning toe aan de query.
	 *
	 * @param  string  $column
	 * @param  string  $direction
	 * @return Query
	 */
	public function order_by($column, $direction = 'asc')
	{
		$this->orderings[] = $this->wrap($column).' '.strtoupper($direction);
		return $this;
	}

	/**
	 * Stel de query offset in.
	 *
	 * @param  int  $value
	 * @return Query
	 */
	public function skip($value)
	{
		$this->offset = $value;
		return $this;
	}

	/**
	 * Stel de query limit in.
	 *
	 * @param  int  $value
	 * @return Query
	 */
	public function take($value)
	{
		$this->limit = $value;
		return $this;
	}

	/**
	 * Stel de limit en offset waardes in voor een opgegeven pagina.
	 *
	 * @param  int    $page
	 * @param  int    $per_page
	 * @return Query
	 */
	public function for_page($page, $per_page)
	{
		return $this->skip(($page - 1) * $per_page)->take($per_page);
	}


	/**
	 * Zoek een record met de primary key.
	 *
	 * @param  int    $id
	 * @param  array  $columns
	 * @return object
	 */
	public function find($id, $columns = array('*'))
	{
		return $this->where('id', '=', $id)->first($columns);
	}

	/**
	 * Haal een "verzamel" waarde op.
	 *
	 * @param  string  $aggregator
	 * @param  string  $column
	 * @return mixed
	 */
	private function aggregate($aggregator, $column)
	{
		$this->select = 'SELECT '.$aggregator.'('.$this->wrap($column).') AS '.$this->wrap('aggregate');

		return $this->first()->aggregate;
	}

	/**
	 * Haal de gepagineerde query resultaten op.
	 *
	 * @param  int        $per_page
	 * @param  array      $columns
	 * @return Paginator
	 */
	public function paginate($per_page, $columns = array('*'))
	{
		$total = $this->count();

		return Paginator::make($this->for_page(Paginator::page($total, $per_page), $per_page)->get($columns), $total, $per_page);
	}

	/**
	 * Voer de query als een SELECT statement uit en return het eerste resultaat.
	 *
	 * @param  array   $columns
	 * @return object
	 */
	public function first($columns = array('*'))
	{
		return (count($results = $this->take(1)->get($columns)) > 0) ? $results[0] : null;
	}

	/**
	 * Voer de query als een SELECT statement uit.
	 *
	 * @param  array  $columns
	 * @return array
	 */
	public function get($columns = array('*'))
	{
		if (is_null($this->select)) {
			$this->select($columns);
		}

		$results = $this->connection->query($this->compile_select(), $this->bindings);

		// Reset de SELECT clause zodat meer queries uitgevoerd kunnen worden met dezelfde instance.
		// Dit is handig voor aggregates ophalen en daarna de echte resultaten ophalen.
		$this->select = null;

		return $results;
	}

	/**
	 * Compileer de query in een SQL SELECT statement.
	 *
	 * @return string
	 */
	private function compile_select()
	{
		$sql = $this->select.' '.$this->from.' '.$this->where;

		if (count($this->orderings) > 0) {
			$sql .= ' ORDER BY '.implode(', ', $this->orderings);
		}

		if (! is_null($this->limit)) {
			$sql .= ' LIMIT '.$this->limit;
		}

		if (! is_null($this->offset)) {
			$sql .= ' OFFSET '.$this->offset;
		}

		return $sql;
	}

	/**
	 * Voer een INSERT statement uit.
	 *
	 * @param  array  $values
	 * @return bool
	 */
	public function insert($values)
	{
		$this->connection->query($this->compile_insert($values), array_values($values));
	}

	/**
	 * Voer een INSERT statement uit en haal de insert ID op.
	 *
	 * @param  array  $values
	 * @return int
	 */
	public function insert_get_id($values)
	{
		$sql = $this->compile_insert($values);

		if ($this->connection->driver() == 'pgsql') {
			$query = $this->connection->pdo->prepare($sql.' RETURNING '.$this->wrap('id'));

			$query->execute(array_values($values));

			return $query->fetch(\PDO::FETCH_CLASS, 'stdClass')->id;
		}

		$this->connection->query($sql, array_values($values));

		return $this->connection->pdo->lastInsertId();
	}

	/**
	 * Compileer de query in een SQL INSERT statement.
	 *
	 * @param  array   $values
	 * @return string
	 */
	private function compile_insert($values)
	{
		$sql = 'INSERT INTO '.$this->wrap($this->table);

		$columns = array_map(array($this, 'wrap'), array_keys($values));

		return $sql .= ' ('.implode(', ', $columns).') VALUES ('.$this->parameterize($values).')';
	}

	/**
	 * Voer de query uit als een UPDATE statement.
	 *
	 * @param  array  $values
	 * @return bool
	 */
	public function update($values)
	{
		$sql = 'UPDATE '.$this->wrap($this->table).' SET ';

		foreach (array_keys($values) as $column) {
			$sets[] = $this->wrap($column).' = ?';
		}

		return $this->connection->query($sql.implode(', ', $sets).' '.$this->where, array_merge(array_values($values), $this->bindings));
	}

	/**
	 * Voer de query uit als een DELETE statement.
	 *
	 * @param  int   $id
	 * @return bool
	 */
	public function delete($id = null)
	{
		if (! is_null($id)) $this->where('id', '=', $id);

		return $this->connection->query('DELETE FROM '.$this->wrap($this->table).' '.$this->where, $this->bindings);
	}

	/**
	 * Wrap een waarde in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	private function wrap($value)
	{
		if (strpos(strtolower($value), ' as ') !== false) {
			return $this->wrap_alias($value);
		}

		$wrap = $this->connection->wrapper();

		foreach (explode('.', $value) as $segment) {
			$wrapped[] = ($segment != '*') ? $wrap.$segment.$wrap : $segment;
		}

		return implode('.', $wrapped);
	}

	/**
	 * Wrap een alias in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	private function wrap_alias($value)
	{
		$segments = explode(' ', $value);

		return $this->wrap($segments[0]).' AS '.$this->wrap($segments[2]);
	}

	/**
	 * Maak query parameters van een array van waardes.
	 *
	 * @param  array  $values
	 * @return string
	 */
	private function parameterize($values)
	{
		return implode(', ', array_fill(0, count($values), '?'));
	}

	/**
	 * Magic Method om dynamische functies af te handelen.
	 */
	public function __call($method, $parameters)
	{
		if (strpos($method, 'where_') === 0) {
			return $this->dynamic_where($method, $parameters, $this);
		}

		if (in_array($method, array('count', 'min', 'max', 'avg', 'sum'))) {
			return ($method == 'count') ? $this->aggregate(strtoupper($method), '*') : $this->aggregate(strtoupper($method), $parameters[0]);
		}

		throw new \Exception("Method [$method] is niet gedefineerd in de Query class.");
	}
} 