<?php
namespace System\DB\Storm;

use System\DB;
use System\Str;
use System\Config;
use System\Paginator;

abstract class Model
{
	/**
	 * De connectie die gebruikt moet worden door de model.
	 *
	 * @var string
	 */
	public static $connection;

	/**
	 * De model query instance.
	 *
	 * @var DB\Query
	 */
	public $query;

	/**
	 * Geeft aan of de model in de database bestaat.
	 *
	 * @var bool
	 */
	public $exists = false;

	/**
	 * De attributen van de model.
	 *
	 * Normaal heeft een model een attribuut voor elke kolom in de tabel.
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * De veranderde attributen van de model.
	 *
	 * @var array
	 */
	public $dirty = array();

	/**
	 * De model's genegeerde attributen.
	 *
	 * Genegeerde attributen worden niet opgeslagen in de database en
	 * worden vooral gebruikt om relaties bij te houden.
	 *
	 * @var array
	 */
	public $ignore = array();

	/**
	 * De relaties die eagerly geladen moeten worden.
	 *
	 * @var array
	 */
	public $includes = array();

	/**
	 * De type relatie dat de model op dit moment gebruikt.
	 *
	 * @var string
	 */
	public $relating;

	/**
	 * De foreign key van de "relating" relatie.
	 *
	 * @var string
	 */
	public $relating_key;

	/**
	 * De tabelnaam van de model.
	 *
	 * Dit wordt gebruikt tijdens many-to-many eager loading.
	 *
	 * @var string
	 */
	public $relating_table;

	/**
	 * Maak een nieuwe Storm model instance.
	 *
	 * @param  array  $attributes
	 */
	public function __construct($attributes = array())
	{
		$this->fill($attributes);
	}

	/**
	 * Stel de attributen van een model in met een array.
	 *
	 * @param  array  $attributes
	 * @return Model
	 */
	public function fill($attributes)
	{
		foreach ($attributes as $key => $value) {
			$this->$key = $value;
		}

		return $this;
	}

	/**
	 * Stel de eagerly loaded models in op de querybare model.
	 *
	 * @return Model
	 */
	private function _with()
	{
		$this->includes = func_get_args();
		return $this;
	}

	/**
	 * Factory voor het maken van querybare Storm model instances.
	 *
	 * @param  string  $class
	 * @return object
	 */
	public static function query($class)
	{
		$model = new $class;

		// Aangezien deze method alleen gebruikt word voor het instantieren van
		// models voor queries, stellen we alvast de query instance in.
		$model->query = DB::connection(static::$connection)->table(static::table($class));

		return $model;
	}

	/**
	 * Haal de tabelnaam voor een model op.
	 *
	 * @param  string  $class
	 * @return string
	 */
	public static function table($class)
	{
		if (property_exists($class, 'table')) return $class::$table;

		return strtolower(static::model_name($class));
	}

	/**
	 * Haal een Storm modelnaam op zonder namespaces.
	 *
	 * @param  string|Model  $model
	 * @return string
	 */
	public static function model_name($model)
	{
		$class = (is_object($model)) ? get_class($model) : $model;

		$segments = array_reverse(explode('\\', $class));

		return $segments[0];
	}

	/**
	 * Haal alle models uit de database.
	 *
	 * @return array
	 */
	public static function all()
	{
		return Hydrator::hydrate(static::query(get_called_class()));
	}

	/**
	 * Haal een model met de primary key op.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public static function find($id)
	{
		return static::query(get_called_class())->where('id', '=', $id)->first();
	}

	/**
	 * Haal een array van models op van de database.
	 *
	 * @return array
	 */
	private function _get($columns = array('*'))
	{
		$this->query->select($columns);

		return Hydrator::hydrate($this);
	}

	/**
	 * Haal het eerste model resultaat op.
	 *
	 * @return mixed
	 */
	private function _first($columns = array('*'))
	{
		return (count($results = $this->take(1)->_get($columns)) > 0) ? reset($results) : null;
	}

	/**
	 * Haal de gepagineerde model resultaten op.
	 *
	 * @param  int        $per_page
	 * @return Paginator
	 */
	private function _paginate($per_page = null, $columns = array('*'))
	{
		$total = $this->query->count();

		if (is_null($per_page)) {
			$per_page = (property_exists(get_class($this), 'per_page')) ? static::$per_page : 20;
		}

		return Paginator::make($this->select($columns)->for_page(Paginator::page($total, $per_page), $per_page)->get(), $total, $per_page);
	}

	/**
	 * Haal de query op van een 1:1 relatie.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	public function has_one($model, $foreign_key = null)
	{
		$this->relating = __FUNCTION__;

		return $this->has_one_or_many($model, $foreign_key);
	}

	/**
	 * Haal de query op van een 1:* relatie.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	public function has_many($model, $foreign_key = null)
	{
		$this->relating = __FUNCTION__;

		return $this->has_one_or_many($model, $foreign_key);
	}

	/**
	 * Haal de query op van een 1:1 of 1:* relatie.
	 *
	 * De default foreign key voor has one en has many relaties is de naam van
	 * de model met een toegevoegde _id. Bijvoorbeeld, de foreign key voor
	 * een User model zou user_id zijn. Photo word photo_id, etc.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	private function has_one_or_many($model, $foreign_key)
	{
		$this->relating_key = (is_null($foreign_key)) ? strtolower(static::model_name($this)).'_id' : $foreign_key;

		return static::query($model)->where($this->relating_key, '=', $this->id);
	}

	/**
	 * Haal de query op van een 1:1 behorende relatie.
	 *
	 * De default foreign key voor belonging relaties is de naam van de
	 * relatie method met _id. Dus, als een model een "manager" method heeft
	 * die een belongs_to relatie returnt, is de key manager_id.
	 *
	 * @param  string  $model
	 * @param  string  $foreign_key
	 * @return mixed
	 */
	public function belongs_to($model, $foreign_key = null)
	{
		$this->relating = __FUNCTION__;

		if (! is_null($foreign_key)) {
			$this->relating_key = $foreign_key;
		} else {
			list(, $caller) = debug_backtrace(false);

			$this->relating_key = $caller['function'].'_id';
		}

		return static::query($model)->where('id', '=', $this->attributes[$this->relating_key]);
	}

	/**
	 * Haal de query op van een *:* relatie.
	 *
	 * De default foreign key voor meer-op-meer relaties is de naam van de model
	 * met een toegevoegde _id. Dit is dezelfde conventie als has_one en has_many.
	 *
	 * @param  string  $model
	 * @param  string  $table
	 * @param  string  $foreign_key
	 * @param  string  $associated_key
	 * @return mixed
	 */
	public function has_and_belongs_to_many($model, $table = null, $foreign_key = null, $associated_key = null)
	{
		$this->relating = __FUNCTION__;

		$this->relating_table = (is_null($table)) ? $this->intermediate_table($model) : $table;

		// Door het overriden van de foreign en associated keys toe te staan wordt flexibiliteit gegeven
		// voor naar zichzelf referende meer-op-meer relaties, zoals een "buddy list".
		$this->relating_key = (is_null($foreign_key)) ? strtolower(static::model_name($this)).'_id' : $foreign_key;

		// De associated key is de foreign key naam van de gerelateerde model. Dus, als de related model
		// "Rol" is, is de geassocieerde key op de koppeltabel "rol_id".
		$associated_key = (is_null($associated_key)) ? strtolower(static::model_name($model)).'_id' : $associated_key;

		return static::query($model)
			->select(array(static::table($model).'.*'))
			->join($this->relating_table, static::table($model).'.id', '=', $this->relating_table.'.'.$associated_key)
			->where($this->relating_table.'.'.$this->relating_key, '=', $this->id);
	}

	/**
	 * Bepaal de koppeltabelnaam voor de opgegeven model.
	 *
	 * Standaard is de koppeltabel naam de namen van de models in alphabetische
	 * volgorde, samengevoegd met een underscore.
	 *
	 * @param  string  $model
	 * @return string
	 */
	private function intermediate_table($model)
	{
		$models = array(static::model_name($model), static::model_name($this));

		sort($models);

		return strtolower($models[0].'_'.$models[1]);
	}

	/**
	 * Sla de model op in de database.
	 *
	 * @return bool
	 */
	public function save()
	{
		// Als de model geen dirty attributen heeft, is er geen reden om het
		// op te slaan in de database.
		if ($this->exists and count($this->dirty) == 0) return true;

		$model = get_class($this);

		// Aangezien de model geïnstantieerd was met "new", is een query instance niet ingesteld.
		// Alleen models die gebruikt worden voor queries hebben standaard een query instance.
		$this->query = DB::connection(static::$connection)->table(static::table($model));

		if (property_exists($model, 'timestamps') and $model::$timestamps) {
			$this->timestamp();
		}

		// Als de model al bestaat in de database updaten we het gewoon.
		// Anders, inserten we de model en stellen we een ID in.
		if ($this->exists) {
			$success = ($this->query->where_id($this->attributes['id'])->update($this->dirty) === 1);
		} else {
			$success = is_numeric($this->attributes['id'] = $this->query->insert_get_id($this->attributes));
		}

		($this->exists = true) and $this->dirty = array();

		return $success;
	}

	/**
	 * Stel de creation en update timestamps in op de model.
	 */
	private function timestamp()
	{
		$this->updated_at = date('Y-m-d H:i:s');

		if (! $this->exists) $this->created_at = $this->updated_at;
	}

	/**
	 * Verwijder een model van de database.
	 *
	 * @param  int  $id
	 * @return int
	 */
	public function delete($id = null)
	{
		// Als de delete method aangeroepen word op een bestaand model, willen we alleen die
		// model verwijderen. Als het word aangeroepen uit een Storm query model, is het
		// waarschijnlijk de developer's intentie om meer dan één model te verwijderen, dus
		// passen we de delete statement naar de query instance.
		if (! $this->exists) return $this->query->delete();

		return DB::connection(static::$connection)->table(static::table(get_class($this)))->delete($this->id);
	}

	/**
	 * Magic Method om model attributen op te halen.
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->attributes))
			return $this->attributes[$key];

		// Is de aangevraagde item een model relatie dat al geladen is?
		// Alle geladen relaties staan opgeslagen in de "ignore" array.
		elseif (array_key_exists($key, $this->ignore))
			return $this->ignore[$key];

		// Is de aangevraagde item een model relatie? Als dat zo is, laden we hem
		// dynamisch en returnen we de resultaten van de relatie query.
		elseif (method_exists($this, $key))
		{
			$query = $this->$key();

			return $this->ignore[$key] = (in_array($this->relating, array('has_one', 'belongs_to'))) ?  $query->first() : $query->get();
		}
	}

	/**
	 * Magic Method om model attributen in te stellen.
	 */
	public function __set($key, $value)
	{
		// Als de key een relatie is, voeg het toe aan ignored attributen.
		// Ignored attributen zijn niet opgeslagen in de database.
		if (method_exists($this, $key))
			$this->ignore[$key] = $value;
		else
		{
			$this->attributes[$key] = $value;
			$this->dirty[$key] = $value;
		}
	}

	/**
	 * Magic Method om te bepalen of eem model attribuut bestaat.
	 */
	public function __isset($key)
	{
		return (array_key_exists($key, $this->attributes) or array_key_exists($key, $this->ignore));
	}

	/**
	 * Magic Method om model attributen te unsetten.
	 */
	public function __unset($key)
	{
		unset($this->attributes[$key], $this->ignore[$key], $this->dirty[$key]);
	}

	/**
	 * Magic Method voor het afhandelen van dynamische method calls.
	 */
	public function __call($method, $parameters)
	{
		// Om de "with", "get", "first" en "paginate" methods toe te staan om statisch, en op
		// een instance aangeroepen te worden moeten we private, underscored versies van de
		// methods hebben en die dynamisch afhandelen.
		if (in_array($method, array('with', 'get', 'first', 'paginate'))) {
			return call_user_func_array(array($this, '_'.$method), $parameters);
		}

		// Alle verzameling en persistance functies kunnen direct naar de query instance gepassed
		// worden. Voor deze functies returnen we gewoon de response van de query.
		if (in_array($method, array('insert', 'update', 'count', 'sum', 'min', 'max', 'avg'))) {
			return call_user_func_array(array($this->query, $method), $parameters);
		}

		// Pass de method naar de query instance. Dit maakt het chainen van methods van de
		// query builder mogelijk, wat zorgt voor dezelfde makkelijke query API als in de
		// query builder zelf.
		call_user_func_array(array($this->query, $method), $parameters);

		return $this;
	}

	/**
	 * Magic Method voor het afhandelen van dynamische static method calls.
	 */
	public static function __callStatic($method, $parameters)
	{
		// Pass de method gewoon naar een model instance en laat de __call method het afhandelen.
		return call_user_func_array(array(static::query(get_called_class()), $method), $parameters);
	}
} 