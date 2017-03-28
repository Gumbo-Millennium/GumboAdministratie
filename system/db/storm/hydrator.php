<?php
namespace System\DB\Storm;

class Hydrator
{
	/**
	 * Laad de array van gevulde models en hun eager relaties.
	 *
	 * @param  Model  $storm
	 * @return array
	 */
	public static function hydrate($storm)
	{
		$results = static::base(get_class($storm), $storm->query->get());

		if (count($results) > 0) {

			foreach ($storm->includes as $include) {

				if (! method_exists($storm, $include)) {
					throw new \Exception("Poging gedaan om [$include] te eager loaden, maar de relatie is niet gedefineerd.");
				}

				static::eagerly($storm, $results, $include);
			}
		}

		return $results;
	}

	/**
	 * Vul de basismodels voor een query.
	 *
	 * De resulterende model array is gekeyed met hun primary keys van de models.
	 * Dit zorgt ervoor dat de models makkelijk gematched kunnen worden aan hun childs.
	 *
	 * @param  string  $class
	 * @param  array   $results
	 * @return array
	 */
	private static function base($class, $results)
	{
		$models = array();

		foreach ($results as $result) {
			$model = new $class;

			$model->attributes = (array) $result;

			$model->exists = true;

			$models[$model->id] = $model;
		}

		return $models;
	}

	/**
	 * Eagerly load een relatie.
	 *
	 * @param  object  $storm
	 * @param  array   $parents
	 * @param  string  $include
	 */
	private static function eagerly($storm, &$parents, $include)
	{
		// We spoofen tijdelijk de belongs_to key zodat de query zonder problemen opgehaald
		// kan worden, omdat de belongs_to method eigenlijk het attribuut ophaald.
		$storm->attributes[$spoof = $include.'_id'] = 0;

		$relationship = $storm->$include();

		unset($storm->attributes[$spoof]);

		// Reset de WHERE clause en bindings op de query. We voegen zo onze eigen WHERE clause toe.
		// Dit laat toe dat we een reeks van verwante models kunnen laden in plaats van slechts één.
		$relationship->query->reset_where();

		// Initialiseer de relatie attribuut op de parents. Zoals verwacht zijn "many" relaties zijn
		// geinitialiseerd als een array en "one" relaties als null.
		foreach ($parents as &$parent) {
			$parent->ignore[$include] = (in_array($storm->relating, array('has_many', 'has_and_belongs_to_many'))) ? array() : null;
		}

		if (in_array($relating = $storm->relating, array('has_one', 'has_many', 'belongs_to'))) {
			static::$relating($relationship, $parents, $storm->relating_key, $include);
		} else {
			static::has_and_belongs_to_many($relationship, $parents, $storm->relating_key, $storm->relating_table, $include);
		}
	}

	/**
	 * Eagerly load een 1:1 relatie.
	 *
	 * @param  object  $relationship
	 * @param  array   $parents
	 * @param  string  $relating_key
	 * @param  string  $include
	 */
	private static function has_one($relationship, &$parents, $relating_key, $include)
	{
		foreach ($relationship->where_in($relating_key, array_keys($parents))->get() as $key => $child) {
			$parents[$child->$relating_key]->ignore[$include] = $child;
		}
	}

	/**
	 * Eagerly load een 1:* relatie.
	 *
	 * @param  object  $relationship
	 * @param  array   $parents
	 * @param  string  $relating_key
	 * @param  string  $include
	 */
	private static function has_many($relationship, &$parents, $relating_key, $include)
	{
		foreach ($relationship->where_in($relating_key, array_keys($parents))->get() as $key => $child) {
			$parents[$child->$relating_key]->ignore[$include][$child->id] = $child;
		}
	}

	/**
	 * Eagerly load een 1:1 behorende relatie.
	 *
	 * @param  object  $relationship
	 * @param  array   $parents
	 * @param  string  $relating_key
	 * @param  string  $include
	 */
	private static function belongs_to($relationship, &$parents, $relating_key, $include)
	{
		$keys = array();

		foreach ($parents as &$parent) {
			$keys[] = $parent->$relating_key;
		}

		$children = $relationship->where_in('id', array_unique($keys))->get();

		foreach ($parents as &$parent) {

			if (array_key_exists($parent->$relating_key, $children)) {
				$parent->ignore[$include] = $children[$parent->$relating_key];
			}
		}
	}

	/**
	 * Eagerly load een meer-op-meer relatie.
	 *
	 * @param  object  $relationship
	 * @param  array   $parents
	 * @param  string  $relating_key
	 * @param  string  $relating_table
	 * @param  string  $include
	 */
	private static function has_and_belongs_to_many($relationship, &$parents, $relating_key, $relating_table, $include)
	{
		// De model "has and belongs to many" method stelt de SELECT clause in; maar, we moeten
		// het leegmaken aangezien we de foreign key aan de select toevoegen.
		$relationship->query->select = null;

		$relationship->query->where_in($relating_table.'.'.$relating_key, array_keys($parents));

		// De foreign key is toegevoegd aan de select om de models makkelijk terug te matchen met hun parents.
		// Anders is er geen duidelijke connectie tussen de models om ze met elkaar te matchen.
		$children = $relationship->query->get(array(Model::table(get_class($relationship)).'.*', $relating_table.'.'.$relating_key));

		$class = get_class($relationship);

		foreach ($children as $child) {
			$related = new $class;

			$related->attributes = (array) $child;

			$related->exists = true;

			// Verwijder de foreign key aangezien het alleen was toegevoegd aan de query om de models te matchen.
			unset($related->attributes[$relating_key]);

			$parents[$child->$relating_key]->ignore[$include][$child->id] = $related;
		}
	}
} 